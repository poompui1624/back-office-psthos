<?php

use App\Models\Department;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\User;
use Spatie\Permission\Models\Permission;

function dashUser(?Employee $employee, string ...$permissions): User
{
    $user = User::factory()->create(['employee_id' => $employee?->id]);

    foreach (array_merge(['dashboard.view'], $permissions) as $permission) {
        Permission::findOrCreate($permission);
        $user->givePermissionTo($permission);
    }

    return $user;
}

test('the dashboard renders for a bare account', function () {
    $this->actingAs(dashUser(null))
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Executive Dashboard');
});

test('the hero greets the user and states the scope', function () {
    $ward = Department::factory()->create(['name' => 'หอผู้ป่วยใน']);
    $employee = Employee::factory()->inDepartment($ward)->create();

    $this->actingAs(dashUser($employee, 'leave.view'))
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('หอผู้ป่วยใน')
        ->assertSee('ขอบเขต');
});

test('a hospital wide viewer is told the scope is the whole hospital', function () {
    $employee = Employee::factory()->create();

    $this->actingAs(dashUser($employee, 'leave.view', 'leave.view.all'))
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('ทั้งโรงพยาบาล');
});

test('cards a user has no permission for are not rendered', function () {
    $employee = Employee::factory()->create();

    $this->actingAs(dashUser($employee))
        ->get(route('dashboard'))
        ->assertOk()
        ->assertDontSee('มูลค่าพัสดุ')
        ->assertDontSee('ใบลารออนุมัติ');
});

test('the dashboard counts only the viewers own department', function () {
    $ward = Department::factory()->create();
    $office = Department::factory()->create();

    $mine = Employee::factory()->inDepartment($ward)->create();
    Employee::factory()->inDepartment($office)->count(4)->create();

    LeaveRequest::factory()->forEmployee($mine)->pending()->create();

    foreach (Employee::where('department_id', $office->id)->get() as $other) {
        LeaveRequest::factory()->forEmployee($other)->pending()->create();
    }

    $supervisor = dashUser($mine, 'leave.view');

    // One pending request in the ward, four in the office. A supervisor
    // without .view.all must be told 1, not 5.
    $this->actingAs($supervisor)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSeeInOrder(['ใบลารออนุมัติ', '1']);

    $hr = dashUser($mine, 'leave.view', 'leave.view.all');

    $this->actingAs($hr)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSeeInOrder(['ใบลารออนุมัติ', '5']);
});
