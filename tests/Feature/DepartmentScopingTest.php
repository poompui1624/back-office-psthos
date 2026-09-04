<?php

use App\Models\Asset;
use App\Models\Department;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\RepairRequest;
use App\Models\User;
use Spatie\Permission\Models\Permission;

/**
 * A user linked to an employee in the given department.
 */
function userInDepartment(?Department $department, string ...$permissions): User
{
    $employee = $department
        ? Employee::factory()->inDepartment($department)->create()
        : null;

    $user = User::factory()->create(['employee_id' => $employee?->id]);

    foreach ($permissions as $permission) {
        Permission::findOrCreate($permission);
        $user->givePermissionTo($permission);
    }

    return $user;
}

test('a supervisor only sees leave from their own department', function () {
    $ward = Department::factory()->create();
    $office = Department::factory()->create();

    $ours = LeaveRequest::factory()
        ->forEmployee(Employee::factory()->inDepartment($ward)->create())
        ->create(['request_no' => 'LV202609010001']);

    $theirs = LeaveRequest::factory()
        ->forEmployee(Employee::factory()->inDepartment($office)->create())
        ->create(['request_no' => 'LV202609010002']);

    $supervisor = userInDepartment($ward, 'leave.view');

    $visible = LeaveRequest::query()->visibleTo($supervisor)->pluck('id');

    expect($visible)->toContain($ours->id)
        ->and($visible)->not->toContain($theirs->id);
});

test('the leave list only renders the viewers own department', function () {
    $ward = Department::factory()->create();
    $office = Department::factory()->create();

    LeaveRequest::factory()
        ->forEmployee(Employee::factory()->inDepartment($ward)->create())
        ->create(['request_no' => 'LV202609010001']);

    LeaveRequest::factory()
        ->forEmployee(Employee::factory()->inDepartment($office)->create())
        ->create(['request_no' => 'LV202609010002']);

    $this->actingAs(userInDepartment($ward, 'leave.view'))
        ->get(route('leave-requests.index'))
        ->assertOk()
        ->assertSee('LV202609010001')
        ->assertDontSee('LV202609010002');
});

test('view all lifts the department limit', function () {
    $ward = Department::factory()->create();
    $office = Department::factory()->create();

    LeaveRequest::factory()->forEmployee(Employee::factory()->inDepartment($ward)->create())->create();
    LeaveRequest::factory()->forEmployee(Employee::factory()->inDepartment($office)->create())->create();

    $hr = userInDepartment($ward, 'leave.view', 'leave.view.all');

    expect(LeaveRequest::query()->visibleTo($hr)->count())->toBe(2);
});

test('a user with no employee record sees nothing rather than everything', function () {
    LeaveRequest::factory()->count(3)->create();

    // Failing closed matters here: an unlinked account must not become a
    // hospital-wide viewer just because it has no department to compare against.
    $orphan = userInDepartment(null, 'leave.view');

    expect(LeaveRequest::query()->visibleTo($orphan)->count())->toBe(0);
});

test('a guest sees nothing', function () {
    LeaveRequest::factory()->count(2)->create();

    expect(LeaveRequest::query()->visibleTo(null)->count())->toBe(0);
});

test('opening another departments record is refused', function () {
    $ward = Department::factory()->create();
    $office = Department::factory()->create();

    $theirs = LeaveRequest::factory()
        ->forEmployee(Employee::factory()->inDepartment($office)->create())
        ->create();

    expect(userInDepartment($ward, 'leave.view')->can('view', $theirs))->toBeFalse()
        ->and(userInDepartment($office, 'leave.view')->can('view', $theirs))->toBeTrue();
});

test('scoping applies across every module that carries a department', function () {
    $ward = Department::factory()->create();
    $office = Department::factory()->create();

    $ourAsset = Asset::factory()->inDepartment($ward)->create();
    Asset::factory()->inDepartment($office)->create();

    $ourRepair = RepairRequest::factory()->inDepartment($ward)->create();
    RepairRequest::factory()->inDepartment($office)->create();

    $user = userInDepartment($ward, 'asset.view', 'repair.view');

    expect(Asset::query()->visibleTo($user)->pluck('id')->all())->toBe([$ourAsset->id])
        ->and(RepairRequest::query()->visibleTo($user)->pluck('id')->all())->toBe([$ourRepair->id]);
});

test('reference data is not department scoped', function () {
    $ward = Department::factory()->create();
    $type = LeaveType::factory()->create();

    // Leave types have no department, so the module permission alone decides.
    expect(userInDepartment($ward, 'leave.view')->can('view', $type))->toBeTrue();
});
