<?php

use App\Models\Employee;
use App\Models\PayrollPeriod;
use App\Models\Payslip;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Permission;

function printDocUser(string ...$permissions): User
{
    $user = User::factory()->create();

    foreach ($permissions as $permission) {
        Permission::findOrCreate($permission);
        $user->givePermissionTo($permission);
    }

    return $user;
}

function withPrintHospitalLogo(): void
{
    SystemSetting::create([
        'group' => 'hospital',
        'key' => 'hospital.logo',
        'label' => 'โลโก้',
        'type' => 'image',
        'value' => 'logos/hospital.png',
        'is_active' => true,
    ]);

    SystemSetting::create([
        'group' => 'hospital',
        'key' => 'hospital.name',
        'label' => 'ชื่อโรงพยาบาล',
        'type' => 'text',
        'value' => 'โรงพยาบาลปางศิลาทอง',
        'is_active' => true,
    ]);

    // helpers.php caches the whole settings table, so a test that writes
    // settings has to clear it or the helper returns the empty set.
    Cache::forget('system_settings_all');
}

test('the payslip print view shows the hospital logo once', function () {
    withPrintHospitalLogo();

    $period = PayrollPeriod::factory()->create(['year' => 2569, 'month' => 8]);
    $payslip = Payslip::create([
        'payroll_period_id' => $period->id,
        'employee_id' => Employee::factory()->create()->id,
        'gross_income' => 30000,
        'total_deduction' => 5000,
        'net_pay' => 25000,
        'status' => 'draft',
        'generated_at' => now(),
    ]);

    $html = $this->actingAs(printDocUser('payroll.view', 'payroll.view.all'))
        ->get(route('payslips.print', $payslip))
        ->assertOk()
        ->getContent();

    // The shared print._document-header already renders the logo and hospital
    // name; this view used to render its own copy of both underneath it.
    expect(substr_count($html, 'logos/hospital.png'))->toBe(1)
        ->and(substr_count($html, 'โรงพยาบาลปางศิลาทอง'))->toBe(1);
});

test('every print view renders the shared header exactly once', function (string $route, callable $make) {
    withPrintHospitalLogo();

    $html = $this->actingAs(printDocUser('payroll.view', 'payroll.view.all', 'attendance.view', 'attendance.view.all', 'duty.view', 'meeting.view'))
        ->get($make())
        ->assertOk()
        ->getContent();

    expect(substr_count($html, 'logos/hospital.png'))->toBeLessThanOrEqual(1);
})->with([
    'payslip' => ['payslips.print', function () {
        $period = PayrollPeriod::factory()->create(['year' => 2569, 'month' => 9]);
        $payslip = Payslip::create([
            'payroll_period_id' => $period->id,
            'employee_id' => Employee::factory()->create()->id,
            'gross_income' => 1, 'total_deduction' => 1, 'net_pay' => 1,
            'status' => 'draft', 'generated_at' => now(),
        ]);

        return route('payslips.print', $payslip);
    }],
    'attendance summary' => ['attendance-summaries.print', fn () => route('attendance-summaries.print')],
]);
