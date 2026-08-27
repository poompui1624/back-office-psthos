<?php

use App\Models\Department;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\User;
use Spatie\Permission\Models\Permission;

/**
 * Give a fresh user exactly the permissions named, creating them if needed.
 */
function userWith(string ...$permissions): User
{
    $user = User::factory()->create();

    foreach ($permissions as $permission) {
        Permission::findOrCreate($permission);
        $user->givePermissionTo($permission);
    }

    return $user;
}

function leavePayload(Employee $employee, LeaveType $type): array
{
    return [
        'employee_id' => $employee->id,
        'department_id' => $employee->department_id,
        'leave_type_id' => $type->id,
        'start_date' => '2026-09-01',
        'end_date' => '2026-09-03',
        'start_period' => 'full',
        'end_period' => 'full',
        'total_days' => 3,
        'reason' => 'ลาพักผ่อนประจำปี',
        'contact_during_leave' => '0800000000',
    ];
}

test('creating a leave request stores it as pending and logs the action', function () {
    $employee = Employee::factory()->create();
    $type = LeaveType::factory()->create();
    $creator = userWith('leave.create');

    $this->actingAs($creator)
        ->post(route('leave-requests.store'), leavePayload($employee, $type))
        ->assertRedirect(route('leave-requests.index'));

    $leave = LeaveRequest::firstOrFail();

    expect($leave->status)->toBe('pending')
        ->and($leave->employee_id)->toBe($employee->id)
        ->and($leave->department_id)->toBe($employee->department_id)
        ->and($leave->created_by)->toBe($creator->id)
        ->and($leave->request_no)->toStartWith('LV')
        ->and($leave->actions()->where('action', 'created')->exists())->toBeTrue();
});

test('each new request gets its own running number', function () {
    $employee = Employee::factory()->create();
    $type = LeaveType::factory()->create();
    $creator = userWith('leave.create');

    foreach (range(1, 3) as $ignored) {
        $this->actingAs($creator)->post(route('leave-requests.store'), leavePayload($employee, $type));
    }

    $numbers = LeaveRequest::pluck('request_no');

    expect($numbers)->toHaveCount(3)
        ->and($numbers->unique())->toHaveCount(3);
});

test('creating a leave request without permission is refused', function () {
    $employee = Employee::factory()->create();
    $type = LeaveType::factory()->create();

    $this->actingAs(userWith('leave.view'))
        ->post(route('leave-requests.store'), leavePayload($employee, $type))
        ->assertForbidden();

    expect(LeaveRequest::count())->toBe(0);
});

test('approving a pending request records the approver', function () {
    $leave = LeaveRequest::factory()->pending()->create();
    $approver = userWith('leave.approve');

    $this->actingAs($approver)
        ->patch(route('leave-requests.approve', $leave), ['approval_remark' => 'อนุมัติตามที่เสนอ'])
        ->assertRedirect();

    $leave->refresh();

    expect($leave->status)->toBe('approved')
        ->and($leave->approved_by)->toBe($approver->id)
        ->and($leave->approved_at)->not->toBeNull()
        ->and($leave->actions()->where('action', 'approved')->exists())->toBeTrue();
});

test('rejecting requires a remark', function () {
    $leave = LeaveRequest::factory()->pending()->create();

    $this->actingAs(userWith('leave.approve'))
        ->patch(route('leave-requests.reject', $leave), [])
        ->assertSessionHasErrors('approval_remark');

    expect($leave->refresh()->status)->toBe('pending');
});

test('rejecting a pending request records the rejecter', function () {
    $leave = LeaveRequest::factory()->pending()->create();
    $rejecter = userWith('leave.approve');

    $this->actingAs($rejecter)
        ->patch(route('leave-requests.reject', $leave), ['approval_remark' => 'ช่วงนี้คนไม่พอ']);

    $leave->refresh();

    expect($leave->status)->toBe('rejected')
        ->and($leave->rejected_by)->toBe($rejecter->id)
        ->and($leave->approval_remark)->toBe('ช่วงนี้คนไม่พอ');
});

test('a request that is not pending cannot be approved again', function () {
    $leave = LeaveRequest::factory()->approved()->create();
    $before = $leave->approved_by;

    $this->actingAs(userWith('leave.approve'))
        ->patch(route('leave-requests.approve', $leave), []);

    expect($leave->refresh()->approved_by)->toBe($before);
});

test('an approved request can still be cancelled', function () {
    $leave = LeaveRequest::factory()->approved()->create();

    $this->actingAs(userWith('leave.update'))
        ->patch(route('leave-requests.cancel', $leave), ['approval_remark' => 'ยกเลิกตามคำขอ']);

    $leave->refresh();

    expect($leave->status)->toBe('cancelled')
        ->and($leave->cancelled_at)->not->toBeNull();
});

test('a rejected request cannot be cancelled', function () {
    $leave = LeaveRequest::factory()->rejected()->create();

    $this->actingAs(userWith('leave.update'))
        ->patch(route('leave-requests.cancel', $leave), []);

    expect($leave->refresh()->status)->toBe('rejected');
});

test('only pending requests can be edited', function () {
    $leave = LeaveRequest::factory()->approved()->create();

    $this->actingAs(userWith('leave.update'))
        ->get(route('leave-requests.edit', $leave))
        ->assertRedirect(route('leave-requests.show', $leave));
});

test('only pending requests can be deleted', function () {
    $approved = LeaveRequest::factory()->approved()->create();
    $pending = LeaveRequest::factory()->pending()->create();
    $deleter = userWith('leave.delete');

    $this->actingAs($deleter)->delete(route('leave-requests.destroy', $approved));
    $this->actingAs($deleter)->delete(route('leave-requests.destroy', $pending));

    expect(LeaveRequest::whereKey($approved->id)->exists())->toBeTrue()
        ->and(LeaveRequest::whereKey($pending->id)->exists())->toBeFalse();
});

test('approvers are notified when a request is submitted', function () {
    $employee = Employee::factory()->create();
    $type = LeaveType::factory()->create();
    $approver = userWith('leave.approve');

    $this->actingAs(userWith('leave.create'))
        ->post(route('leave-requests.store'), leavePayload($employee, $type));

    expect($approver->appNotifications()->where('type', 'leave')->exists())->toBeTrue();
});

test('the creator is notified when the status changes', function () {
    $creator = userWith('leave.create');
    $leave = LeaveRequest::factory()->pending()->create(['created_by' => $creator->id]);

    $this->actingAs(userWith('leave.approve'))
        ->patch(route('leave-requests.approve', $leave), []);

    expect($creator->appNotifications()->where('type', 'leave')->exists())->toBeTrue();
});

test('the leave list can be filtered by status', function () {
    $department = Department::factory()->create();
    $employee = Employee::factory()->inDepartment($department)->create();

    LeaveRequest::factory()->forEmployee($employee)->pending()->create(['request_no' => 'LV202609010001']);
    LeaveRequest::factory()->forEmployee($employee)->approved()->create(['request_no' => 'LV202609010002']);

    $this->actingAs(userWith('leave.view', 'leave.view.all'))
        ->get(route('leave-requests.index', ['status' => 'approved']))
        ->assertOk()
        ->assertSee('LV202609010002')
        ->assertDontSee('LV202609010001');
});

test('submitting works even when no approver permission has been seeded', function () {
    $employee = Employee::factory()->create();
    $type = LeaveType::factory()->create();

    // Regression: the notify step looked up 'leave.approve' through Spatie's scope,
    // which throws when the permission row is absent, turning a valid submission
    // into a 500 on a install that has not been seeded yet.
    expect(Permission::where('name', 'leave.approve')->exists())->toBeFalse();

    $this->actingAs(userWith('leave.create'))
        ->post(route('leave-requests.store'), leavePayload($employee, $type))
        ->assertRedirect(route('leave-requests.index'));

    expect(LeaveRequest::count())->toBe(1);
});
