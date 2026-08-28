<?php

use App\Models\Employee;
use App\Models\SalaryProfile;
use App\Models\User;
use Spatie\Permission\Models\Permission;

function payrollEditor(string ...$permissions): User
{
    $user = User::factory()->create();

    foreach ($permissions as $permission) {
        Permission::findOrCreate($permission);
        $user->givePermissionTo($permission);
    }

    return $user;
}

test('the edit form posts to update, not store', function () {
    $profile = SalaryProfile::factory()->create(['base_salary' => 20000]);

    // Regression: the edit view was a copy of create. It posted to
    // salary-profiles.store, so saving an edit added a second profile for the
    // same employee instead of changing the existing one.
    $this->actingAs(payrollEditor('payroll.update'))
        ->get(route('salary-profiles.edit', $profile))
        ->assertOk()
        ->assertSee(route('salary-profiles.update', $profile), false)
        ->assertDontSee('action="'.route('salary-profiles.store').'"', false);
});

test('the edit form shows the profile it is editing', function () {
    $employee = Employee::factory()->create();
    $profile = SalaryProfile::factory()->forEmployee($employee)->create(['base_salary' => 31500]);

    $this->actingAs(payrollEditor('payroll.update'))
        ->get(route('salary-profiles.edit', $profile))
        ->assertOk()
        ->assertSee('31500')
        ->assertSee($employee->full_name);
});

test('saving an edit changes the profile instead of creating another', function () {
    $employee = Employee::factory()->create();
    $profile = SalaryProfile::factory()->forEmployee($employee)->create(['base_salary' => 20000]);

    $this->actingAs(payrollEditor('payroll.update'))
        ->put(route('salary-profiles.update', $profile), [
            'employee_id' => $employee->id,
            'base_salary' => 26000,
        ])
        ->assertRedirect();

    expect(SalaryProfile::count())->toBe(1)
        ->and((float) $profile->refresh()->base_salary)->toBe(26000.0);
});
