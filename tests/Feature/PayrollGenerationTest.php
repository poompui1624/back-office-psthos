<?php

use App\Models\AttendanceDailySummary;
use App\Models\Employee;
use App\Models\PayrollPeriod;
use App\Models\Payslip;
use App\Models\SalaryProfile;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

function payrollUser(string ...$permissions): User
{
    $user = User::factory()->create();

    foreach ($permissions as $permission) {
        Permission::findOrCreate($permission);
        $user->givePermissionTo($permission);
    }

    return $user;
}

test('generating builds one payslip per active salary profile', function () {
    $period = PayrollPeriod::factory()->forMonth(2026, 7)->create();

    SalaryProfile::factory()->count(3)->create();
    SalaryProfile::factory()->inactive()->create();

    $this->actingAs(payrollUser('payroll.generate'))
        ->post(route('payroll-periods.generate', $period))
        ->assertRedirect(route('payroll-periods.show', $period));

    expect(Payslip::where('payroll_period_id', $period->id)->count())->toBe(3)
        ->and($period->refresh()->status)->toBe('generated')
        ->and($period->generated_at)->not->toBeNull();
});

test('net pay is gross income minus total deductions', function () {
    $period = PayrollPeriod::factory()->forMonth(2026, 7)->create();

    SalaryProfile::factory()->create([
        'base_salary' => 20000,
        'position_allowance' => 3500,
        'professional_allowance' => 1500,
        'other_allowance' => 0,
        'social_security' => 750,
        'tax' => 250,
        'provident_fund' => 0,
        'other_deduction' => 0,
    ]);

    $this->actingAs(payrollUser('payroll.generate'))
        ->post(route('payroll-periods.generate', $period));

    $payslip = Payslip::firstOrFail();

    expect((float) $payslip->gross_income)->toBe(25000.0)
        ->and((float) $payslip->total_deduction)->toBe(1000.0)
        ->and((float) $payslip->net_pay)->toBe(24000.0);
});

test('attendance in the period drives the deductions', function () {
    $period = PayrollPeriod::factory()->forMonth(2026, 7)->create();
    $employee = Employee::factory()->create();

    SalaryProfile::factory()->forEmployee($employee)->withPenalties()->create([
        'base_salary' => 20000,
        'position_allowance' => 0,
        'professional_allowance' => 0,
        'other_allowance' => 0,
        'social_security' => 0,
        'tax' => 0,
        'provident_fund' => 0,
        'other_deduction' => 0,
    ]);

    AttendanceDailySummary::factory()->forEmployee($employee)->onDate('2026-07-06')->late(20)->create();
    AttendanceDailySummary::factory()->forEmployee($employee)->onDate('2026-07-07')->earlyLeave(10)->create();
    AttendanceDailySummary::factory()->forEmployee($employee)->onDate('2026-07-08')->absent()->create();

    $this->actingAs(payrollUser('payroll.generate'))
        ->post(route('payroll-periods.generate', $period));

    $payslip = Payslip::firstOrFail();

    // 20 late minutes and 10 early minutes at 5/minute, plus one absent day at 800.
    expect($payslip->late_minutes)->toBe(20)
        ->and($payslip->early_leave_minutes)->toBe(10)
        ->and($payslip->absent_days)->toBe(1)
        ->and((float) $payslip->total_deduction)->toBe(950.0)
        ->and((float) $payslip->net_pay)->toBe(19050.0);
});

test('attendance outside the period is ignored', function () {
    $period = PayrollPeriod::factory()->forMonth(2026, 7)->create();
    $employee = Employee::factory()->create();

    SalaryProfile::factory()->forEmployee($employee)->withPenalties()->create([
        'base_salary' => 20000,
        'position_allowance' => 0,
        'professional_allowance' => 0,
        'other_allowance' => 0,
        'social_security' => 0,
        'tax' => 0,
        'provident_fund' => 0,
        'other_deduction' => 0,
    ]);

    AttendanceDailySummary::factory()->forEmployee($employee)->onDate('2026-06-30')->late(60)->create();
    AttendanceDailySummary::factory()->forEmployee($employee)->onDate('2026-08-01')->late(60)->create();

    $this->actingAs(payrollUser('payroll.generate'))
        ->post(route('payroll-periods.generate', $period));

    expect(Payslip::firstOrFail()->late_minutes)->toBe(0);
});

test('regenerating updates the existing payslip instead of duplicating it', function () {
    $period = PayrollPeriod::factory()->forMonth(2026, 7)->create();
    $employee = Employee::factory()->create();
    $profile = SalaryProfile::factory()->forEmployee($employee)->create(['base_salary' => 20000]);

    $generator = payrollUser('payroll.generate');

    $this->actingAs($generator)->post(route('payroll-periods.generate', $period));

    $profile->update(['base_salary' => 25000]);

    $this->actingAs($generator)->post(route('payroll-periods.generate', $period));

    expect(Payslip::where('payroll_period_id', $period->id)->count())->toBe(1)
        ->and(Payslip::firstOrFail()->items()->where('code', 'BASE')->count())->toBe(1)
        ->and((float) Payslip::firstOrFail()->items()->where('code', 'BASE')->value('amount'))->toBe(25000.0);
});

test('a closed period cannot be generated again', function () {
    $period = PayrollPeriod::factory()->forMonth(2026, 7)->closed()->create();
    SalaryProfile::factory()->create();

    $this->actingAs(payrollUser('payroll.generate'))
        ->post(route('payroll-periods.generate', $period));

    expect(Payslip::count())->toBe(0);
});

test('closing a period marks it closed', function () {
    $period = PayrollPeriod::factory()->forMonth(2026, 7)->generated()->create();

    $this->actingAs(payrollUser('payroll.update'))
        ->post(route('payroll-periods.close', $period));

    $period->refresh();

    expect($period->status)->toBe('closed')
        ->and($period->closed_at)->not->toBeNull();
});

test('generating without permission is refused', function () {
    $period = PayrollPeriod::factory()->forMonth(2026, 7)->create();
    SalaryProfile::factory()->create();

    $this->actingAs(payrollUser('payroll.view'))
        ->post(route('payroll-periods.generate', $period))
        ->assertForbidden();

    expect(Payslip::count())->toBe(0);
});

test('zero amount lines are left off the payslip', function () {
    $period = PayrollPeriod::factory()->forMonth(2026, 7)->create();

    SalaryProfile::factory()->create([
        'base_salary' => 20000,
        'position_allowance' => 0,
        'professional_allowance' => 0,
        'other_allowance' => 0,
        'social_security' => 0,
        'tax' => 0,
        'provident_fund' => 0,
        'other_deduction' => 0,
    ]);

    $this->actingAs(payrollUser('payroll.generate'))
        ->post(route('payroll-periods.generate', $period));

    $items = Payslip::firstOrFail()->items;

    expect($items)->toHaveCount(1)
        ->and($items->first()->code)->toBe('BASE');
});

test('attendance is read in one query regardless of headcount', function () {
    $period = PayrollPeriod::factory()->forMonth(2026, 7)->create();

    SalaryProfile::factory()->count(10)->create();

    DB::enableQueryLog();

    $this->actingAs(payrollUser('payroll.generate'))
        ->post(route('payroll-periods.generate', $period));

    $attendanceQueries = collect(DB::getQueryLog())
        ->filter(fn (array $entry) => str_contains($entry['query'], 'attendance_daily_summaries'))
        ->count();

    DB::disableQueryLog();

    // The old controller ran one attendance query per salary profile.
    expect($attendanceQueries)->toBe(1);
});

test('a profile whose employee is gone is skipped, not fatal', function () {
    $period = PayrollPeriod::factory()->forMonth(2026, 7)->create();

    $employee = Employee::factory()->create();
    SalaryProfile::factory()->forEmployee($employee)->create();
    SalaryProfile::factory()->create();

    $employee->forceDelete();

    $this->actingAs(payrollUser('payroll.generate'))
        ->post(route('payroll-periods.generate', $period))
        ->assertRedirect(route('payroll-periods.show', $period));

    expect(Payslip::count())->toBe(1);
});
