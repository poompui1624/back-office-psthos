<?php

use App\Models\AttendanceDailySummary;
use App\Models\DutySchedule;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\PayrollPeriod;
use App\Models\Payslip;
use App\Models\SalaryProfile;
use App\Models\ShiftType;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

function otPayrollUser(): User
{
    $user = User::factory()->create();
    Permission::findOrCreate('payroll.generate');
    $user->givePermissionTo('payroll.generate');

    return $user;
}

function bareProfile(Employee $employee, array $overrides = []): SalaryProfile
{
    return SalaryProfile::factory()->forEmployee($employee)->create(array_merge([
        'base_salary' => 20000,
        'position_allowance' => 0,
        'professional_allowance' => 0,
        'other_allowance' => 0,
        'social_security' => 0,
        'tax' => 0,
        'provident_fund' => 0,
        'other_deduction' => 0,
    ], $overrides));
}

test('confirmed overtime becomes a paid line on the payslip', function () {
    $period = PayrollPeriod::factory()->forMonth(2026, 7)->create();
    $employee = Employee::factory()->create();
    bareProfile($employee, ['ot_rate_per_hour' => 100]);

    $otShift = ShiftType::factory()->overtime(1.5)->create();

    DutySchedule::factory()->forEmployee($employee)->for($otShift, 'shiftType')
        ->window('2026-07-10 18:00', '2026-07-10 22:00')->confirmed()->create();

    $this->actingAs(otPayrollUser())->post(route('payroll-periods.generate', $period));

    $payslip = Payslip::firstOrFail();
    $otLine = $payslip->items()->where('code', 'OT')->first();

    // 4 hours x 100 x 1.5
    expect($otLine)->not->toBeNull()
        ->and((float) $otLine->amount)->toBe(600.0)
        ->and((float) $otLine->quantity)->toBe(4.0)
        ->and((float) $payslip->gross_income)->toBe(20600.0)
        ->and((float) $payslip->net_pay)->toBe(20600.0);
});

test('an overtime shift crossing midnight is paid for both halves', function () {
    $period = PayrollPeriod::factory()->forMonth(2026, 7)->create();
    $employee = Employee::factory()->create();
    bareProfile($employee, ['ot_rate_per_hour' => 100]);

    $night = ShiftType::factory()->overtime(1)->create(['crosses_midnight' => true]);

    DutySchedule::factory()->forEmployee($employee)->for($night, 'shiftType')
        ->window('2026-07-10 22:00', '2026-07-11 06:00')->confirmed()->create();

    $this->actingAs(otPayrollUser())->post(route('payroll-periods.generate', $period));

    expect((float) Payslip::firstOrFail()->items()->where('code', 'OT')->value('amount'))->toBe(800.0);
});

test('unconfirmed overtime is not paid', function () {
    $period = PayrollPeriod::factory()->forMonth(2026, 7)->create();
    $employee = Employee::factory()->create();
    bareProfile($employee, ['ot_rate_per_hour' => 100]);

    $otShift = ShiftType::factory()->overtime(1)->create();

    DutySchedule::factory()->forEmployee($employee)->for($otShift, 'shiftType')
        ->window('2026-07-10 18:00', '2026-07-10 22:00')->create(['status' => 'assigned']);

    $this->actingAs(otPayrollUser())->post(route('payroll-periods.generate', $period));

    expect(Payslip::firstOrFail()->items()->where('code', 'OT')->exists())->toBeFalse();
});

test('an employee with no overtime gets no overtime line', function () {
    $period = PayrollPeriod::factory()->forMonth(2026, 7)->create();
    bareProfile(Employee::factory()->create(), ['ot_rate_per_hour' => 100]);

    $this->actingAs(otPayrollUser())->post(route('payroll-periods.generate', $period));

    expect(Payslip::firstOrFail()->items()->where('code', 'OT')->exists())->toBeFalse();
});

test('a day on approved leave is not deducted as absence', function () {
    $period = PayrollPeriod::factory()->forMonth(2026, 7)->create();
    $employee = Employee::factory()->create();
    bareProfile($employee, ['absent_deduction_per_day' => 800]);

    AttendanceDailySummary::factory()->forEmployee($employee)->onDate('2026-07-06')->absent()->create();
    AttendanceDailySummary::factory()->forEmployee($employee)->onDate('2026-07-07')->absent()->create();

    LeaveRequest::factory()->forEmployee($employee)->approved()
        ->between('2026-07-06', '2026-07-06')->create();

    $this->actingAs(otPayrollUser())->post(route('payroll-periods.generate', $period));

    $payslip = Payslip::firstOrFail();

    // Only the 7th counts; the 6th was approved leave.
    expect($payslip->absent_days)->toBe(1)
        ->and((float) $payslip->total_deduction)->toBe(800.0);
});

test('a pending leave request does not excuse absence', function () {
    $period = PayrollPeriod::factory()->forMonth(2026, 7)->create();
    $employee = Employee::factory()->create();
    bareProfile($employee, ['absent_deduction_per_day' => 800]);

    AttendanceDailySummary::factory()->forEmployee($employee)->onDate('2026-07-06')->absent()->create();

    LeaveRequest::factory()->forEmployee($employee)->pending()
        ->between('2026-07-06', '2026-07-06')->create();

    $this->actingAs(otPayrollUser())->post(route('payroll-periods.generate', $period));

    expect(Payslip::firstOrFail()->absent_days)->toBe(1);
});

test('leave outside the period does not excuse absence inside it', function () {
    $period = PayrollPeriod::factory()->forMonth(2026, 7)->create();
    $employee = Employee::factory()->create();
    bareProfile($employee, ['absent_deduction_per_day' => 800]);

    AttendanceDailySummary::factory()->forEmployee($employee)->onDate('2026-07-06')->absent()->create();

    LeaveRequest::factory()->forEmployee($employee)->approved()
        ->between('2026-06-01', '2026-06-30')->create();

    expect(Payslip::count())->toBe(0);

    $this->actingAs(otPayrollUser())->post(route('payroll-periods.generate', $period));

    expect(Payslip::firstOrFail()->absent_days)->toBe(1);
});

test('overtime and leave are each read in one query regardless of headcount', function () {
    $period = PayrollPeriod::factory()->forMonth(2026, 7)->create();
    $otShift = ShiftType::factory()->overtime(1)->create();

    foreach (range(1, 8) as $ignored) {
        $employee = Employee::factory()->create();
        bareProfile($employee, ['ot_rate_per_hour' => 100]);

        DutySchedule::factory()->forEmployee($employee)->for($otShift, 'shiftType')
            ->window('2026-07-10 18:00', '2026-07-10 22:00')->confirmed()->create();
    }

    DB::enableQueryLog();

    $this->actingAs(otPayrollUser())->post(route('payroll-periods.generate', $period));

    $log = collect(DB::getQueryLog());

    $dutyQueries = $log->filter(fn ($e) => str_contains($e['query'], 'from "duty_schedules"'))->count();
    $leaveQueries = $log->filter(fn ($e) => str_contains($e['query'], 'from "leave_requests"'))->count();

    DB::disableQueryLog();

    expect($dutyQueries)->toBe(1)
        ->and($leaveQueries)->toBe(1);
});
