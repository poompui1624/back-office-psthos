<?php

use App\Models\Department;
use App\Models\Employee;
use App\Models\PayrollPeriod;
use App\Models\Payslip;
use App\Models\User;
use Spatie\Permission\Models\Permission;

function slipUser(?Employee $employee, string ...$permissions): User
{
    $user = User::factory()->create(['employee_id' => $employee?->id]);

    foreach ($permissions as $permission) {
        Permission::findOrCreate($permission);
        $user->givePermissionTo($permission);
    }

    return $user;
}

function slipFor(Employee $employee): Payslip
{
    return Payslip::create([
        'payroll_period_id' => PayrollPeriod::factory()->create()->id,
        'employee_id' => $employee->id,
        'net_pay' => 20000,
        'generated_at' => now(),
    ]);
}

test('staff can open their own payslip from the portal', function () {
    $employee = Employee::factory()->create();
    $slip = slipFor($employee);

    // Regression: /me/payslips links to this route, but the controller checked
    // payroll.view, which staff never hold — so the link 403'd on their own slip.
    $this->actingAs(slipUser($employee, 'payslip.view.own'))
        ->get(route('payslips.print', $slip))
        ->assertOk();

    $this->actingAs(slipUser($employee, 'payslip.view.own'))
        ->get(route('payslips.show', $slip))
        ->assertOk();
});

test('staff cannot open someone elses payslip', function () {
    $mine = Employee::factory()->create();
    $theirs = slipFor(Employee::factory()->create());

    $this->actingAs(slipUser($mine, 'payslip.view.own'))
        ->get(route('payslips.show', $theirs))
        ->assertForbidden();
});

test('payroll view alone no longer opens any payslip by id', function () {
    $ward = Department::factory()->create();
    $office = Department::factory()->create();

    $clerk = slipUser(Employee::factory()->inDepartment($ward)->create(), 'payroll.view');
    $otherDepartment = slipFor(Employee::factory()->inDepartment($office)->create());

    $this->actingAs($clerk)
        ->get(route('payslips.show', $otherDepartment))
        ->assertForbidden();
});

test('payroll view covers the viewers own department', function () {
    $ward = Department::factory()->create();

    $clerk = slipUser(Employee::factory()->inDepartment($ward)->create(), 'payroll.view');
    $colleague = slipFor(Employee::factory()->inDepartment($ward)->create());

    $this->actingAs($clerk)
        ->get(route('payslips.show', $colleague))
        ->assertOk();
});

test('hospital wide payroll access opens any payslip', function () {
    $office = Department::factory()->create();
    $slip = slipFor(Employee::factory()->inDepartment($office)->create());

    $hr = slipUser(Employee::factory()->create(), 'payroll.view', 'payroll.view.all');

    $this->actingAs($hr)
        ->get(route('payslips.show', $slip))
        ->assertOk();
});

test('an account with no permissions is refused', function () {
    $slip = slipFor(Employee::factory()->create());

    $this->actingAs(User::factory()->create())
        ->get(route('payslips.show', $slip))
        ->assertForbidden();
});
