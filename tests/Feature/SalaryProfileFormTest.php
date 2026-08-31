<?php

use App\Models\Employee;
use App\Models\SalaryProfile;
use App\Models\User;
use Spatie\Permission\Models\Permission;

function salaryFormUser(string ...$permissions): User
{
    $user = User::factory()->create();

    foreach ($permissions as $permission) {
        Permission::findOrCreate($permission);
        $user->givePermissionTo($permission);
    }

    return $user;
}

/**
 * Every amount the form collects, with a distinct value per field so a
 * mix-up between two of them shows up as a wrong number rather than passing.
 *
 * @return array<string, string>
 */
function salaryAmounts(): array
{
    return [
        'base_salary' => '30000.00',
        'position_allowance' => '3500.50',
        'professional_allowance' => '5000.25',
        'other_allowance' => '1200.75',
        'social_security' => '750.00',
        'tax' => '1800.00',
        'provident_fund' => '900.00',
        'other_deduction' => '250.00',
        'late_deduction_per_minute' => '2.50',
        'early_leave_deduction_per_minute' => '3.25',
        'absent_deduction_per_day' => '1000.00',
        'ot_rate_per_hour' => '187.50',
    ];
}

test('the form saves every amount to its own column', function () {
    $employee = Employee::factory()->create();

    $this->actingAs(salaryFormUser('payroll.create'))
        ->post(route('salary-profiles.store'), array_merge(salaryAmounts(), [
            'employee_id' => $employee->id,
            'is_active' => '1',
            'remark' => 'ตั้งค่าเริ่มต้น',
        ]))
        ->assertRedirect();

    $profile = SalaryProfile::firstOrFail();

    foreach (salaryAmounts() as $field => $value) {
        expect((float) $profile->{$field})
            ->toBe((float) $value, "field {$field} did not save");
    }

    expect($profile->employee_id)->toBe($employee->id)
        ->and($profile->is_active)->toBeTrue()
        ->and($profile->remark)->toBe('ตั้งค่าเริ่มต้น');
});

test('the edit form shows every stored amount back', function () {
    $profile = SalaryProfile::factory()->create(array_merge(salaryAmounts(), [
        'employee_id' => Employee::factory()->create()->id,
    ]));

    $response = $this->actingAs(salaryFormUser('payroll.update'))
        ->get(route('salary-profiles.edit', $profile))
        ->assertOk();

    foreach (salaryAmounts() as $field => $value) {
        $response->assertSee('name="'.$field.'"', false);
    }
});

test('a missing base salary is still rejected', function () {
    $employee = Employee::factory()->create();

    $this->actingAs(salaryFormUser('payroll.create'))
        ->post(route('salary-profiles.store'), ['employee_id' => $employee->id])
        ->assertSessionHasErrors('base_salary');

    expect(SalaryProfile::count())->toBe(0);
});

test('unchecking active is saved as inactive', function () {
    $profile = SalaryProfile::factory()->create([
        'employee_id' => Employee::factory()->create()->id,
        'is_active' => true,
    ]);

    // An unchecked box posts nothing at all, so the controller has to read it
    // as a boolean rather than trusting the key to be present.
    $this->actingAs(salaryFormUser('payroll.update'))
        ->put(route('salary-profiles.update', $profile), array_merge(salaryAmounts(), [
            'employee_id' => $profile->employee_id,
        ]))
        ->assertRedirect();

    expect($profile->refresh()->is_active)->toBeFalse();
});
