<?php

use App\Models\Department;
use App\Models\DutySchedule;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\MeetingBooking;
use App\Models\PayrollPeriod;
use App\Models\Payslip;
use App\Models\RepairRequest;
use App\Models\User;
use Spatie\Permission\Models\Permission;

/**
 * A user linked to their own employee record, holding the given permissions.
 */
function portalUser(?Employee $employee, string ...$permissions): User
{
    $user = User::factory()->create(['employee_id' => $employee?->id]);

    foreach ($permissions as $permission) {
        Permission::findOrCreate($permission);
        $user->givePermissionTo($permission);
    }

    return $user;
}

test('the portal overview renders for a linked account', function () {
    $employee = Employee::factory()->create();

    $this->actingAs(portalUser($employee))
        ->get(route('me.index'))
        ->assertOk()
        ->assertSee('ภาพรวมของฉัน')
        ->assertSee($employee->employee_code);
});

test('an unlinked account is told to contact an admin rather than refused', function () {
    $this->actingAs(portalUser(null))
        ->get(route('me.index'))
        ->assertOk()
        ->assertSee('ยังไม่ได้เชื่อมกับทะเบียนบุคลากร');
});

test('an unlinked account reaching a section is sent to that explanation', function () {
    $this->actingAs(portalUser(null, 'leave.view.own'))
        ->get(route('me.leaves'))
        ->assertRedirect(route('me.index'));
});

test('my leave list shows only my own requests', function () {
    $mine = Employee::factory()->create();
    $theirs = Employee::factory()->create();

    LeaveRequest::factory()->forEmployee($mine)->create(['request_no' => 'LV202609010001']);
    LeaveRequest::factory()->forEmployee($theirs)->create(['request_no' => 'LV202609010002']);

    $this->actingAs(portalUser($mine, 'leave.view.own'))
        ->get(route('me.leaves'))
        ->assertOk()
        ->assertSee('LV202609010001')
        ->assertDontSee('LV202609010002');
});

test('my leave list ignores the department, only the person', function () {
    $ward = Department::factory()->create();

    $mine = Employee::factory()->inDepartment($ward)->create();
    $colleague = Employee::factory()->inDepartment($ward)->create();

    LeaveRequest::factory()->forEmployee($mine)->create(['request_no' => 'LV202609010001']);
    LeaveRequest::factory()->forEmployee($colleague)->create(['request_no' => 'LV202609010002']);

    // Same department, so department scoping would let this through.
    // The portal is narrower than that.
    $this->actingAs(portalUser($mine, 'leave.view.own'))
        ->get(route('me.leaves'))
        ->assertOk()
        ->assertSee('LV202609010001')
        ->assertDontSee('LV202609010002');
});

test('the portal does not grant the admin pages', function () {
    $employee = Employee::factory()->create();

    $this->actingAs(portalUser($employee, 'leave.view.own'))
        ->get(route('leave-requests.index'))
        ->assertForbidden();
});

test('a section without its own permission is refused', function () {
    $employee = Employee::factory()->create();

    $this->actingAs(portalUser($employee, 'leave.view.own'))
        ->get(route('me.payslips'))
        ->assertForbidden();
});

test('my duties, attendance, payslips and repairs each show only mine', function () {
    $mine = Employee::factory()->create();
    $theirs = Employee::factory()->create();

    DutySchedule::factory()->forEmployee($mine)->onDate(now()->toDateString())->create(['remark' => 'MINE-DUTY']);
    DutySchedule::factory()->forEmployee($theirs)->onDate(now()->toDateString())->create(['remark' => 'THEIR-DUTY']);

    RepairRequest::factory()->create(['requester_employee_id' => $mine->id, 'ticket_no' => 'RP202609010001']);
    RepairRequest::factory()->create(['requester_employee_id' => $theirs->id, 'ticket_no' => 'RP202609010002']);

    $period = PayrollPeriod::factory()->create();
    Payslip::create(['payroll_period_id' => $period->id, 'employee_id' => $mine->id, 'net_pay' => 11111, 'generated_at' => now()]);
    Payslip::create(['payroll_period_id' => $period->id, 'employee_id' => $theirs->id, 'net_pay' => 22222, 'generated_at' => now()]);

    $user = portalUser($mine, 'duty.view.own', 'attendance.view.own', 'repair.view.own', 'payslip.view.own');

    $this->actingAs($user)->get(route('me.repairs'))
        ->assertOk()->assertSee('RP202609010001')->assertDontSee('RP202609010002');

    $this->actingAs($user)->get(route('me.payslips'))
        ->assertOk()->assertSee('11,111.00')->assertDontSee('22,222.00');

    $this->actingAs($user)->get(route('me.attendance'))->assertOk();
    $this->actingAs($user)->get(route('me.duties'))->assertOk();
});

test('staff filing leave can only file for themselves', function () {
    $mine = Employee::factory()->create();
    $someoneElse = Employee::factory()->create();
    $type = LeaveType::factory()->create();

    $staff = portalUser($mine, 'leave.create');

    // The form is posted with another employee's id; the server must ignore it.
    $this->actingAs($staff)->post(route('leave-requests.store'), [
        'employee_id' => $someoneElse->id,
        'leave_type_id' => $type->id,
        'start_date' => '2026-09-01',
        'end_date' => '2026-09-01',
        'start_period' => 'full',
        'end_period' => 'full',
        'total_days' => 1,
    ])->assertRedirect(route('leave-requests.index'));

    expect(LeaveRequest::firstOrFail()->employee_id)->toBe($mine->id);
});

test('hr can still file on behalf of anyone', function () {
    $hrEmployee = Employee::factory()->create();
    $someoneElse = Employee::factory()->create();
    $type = LeaveType::factory()->create();

    $hr = portalUser($hrEmployee, 'leave.create', 'leave.create.any');

    $this->actingAs($hr)->post(route('leave-requests.store'), [
        'employee_id' => $someoneElse->id,
        'leave_type_id' => $type->id,
        'start_date' => '2026-09-01',
        'end_date' => '2026-09-01',
        'start_period' => 'full',
        'end_period' => 'full',
        'total_days' => 1,
    ])->assertRedirect(route('leave-requests.index'));

    expect(LeaveRequest::firstOrFail()->employee_id)->toBe($someoneElse->id);
});

test('the leave form offers staff only their own name', function () {
    $mine = Employee::factory()->create();
    Employee::factory()->count(3)->create();

    $this->actingAs(portalUser($mine, 'leave.create'))
        ->get(route('leave-requests.create'))
        ->assertOk()
        ->assertViewHas('employees', fn ($employees) => $employees->count() === 1
            && $employees->first()->id === $mine->id);
});

test('an unlinked account cannot file leave at all', function () {
    $type = LeaveType::factory()->create();
    $employee = Employee::factory()->create();

    $this->actingAs(portalUser(null, 'leave.create'))->post(route('leave-requests.store'), [
        'employee_id' => $employee->id,
        'leave_type_id' => $type->id,
        'start_date' => '2026-09-01',
        'end_date' => '2026-09-01',
        'start_period' => 'full',
        'end_period' => 'full',
        'total_days' => 1,
    ])->assertForbidden();

    expect(LeaveRequest::count())->toBe(0);
});

test('my meetings shows only bookings I requested', function () {
    $mine = Employee::factory()->create();
    $theirs = Employee::factory()->create();

    MeetingBooking::factory()->create(['employee_id' => $mine->id, 'booking_no' => 'MR202609010001']);
    MeetingBooking::factory()->create(['employee_id' => $theirs->id, 'booking_no' => 'MR202609010002']);

    $this->actingAs(portalUser($mine, 'meeting.view.own'))
        ->get(route('me.meetings'))
        ->assertOk()
        ->assertSee('MR202609010001')
        ->assertDontSee('MR202609010002');
});

test('the sidebar offers only the sections the user holds', function () {
    $employee = Employee::factory()->create();

    $this->actingAs(portalUser($employee, 'leave.view.own'))
        ->get(route('me.index'))
        ->assertOk()
        ->assertSee('ใบลาของฉัน')
        ->assertDontSee('สลิปของฉัน');
});

test('the portal overview is reachable without any own permission', function () {
    $employee = Employee::factory()->create();

    // The overview is the landing page for the link in the sidebar, so it must
    // not itself require a section permission.
    $this->actingAs(portalUser($employee))
        ->get(route('me.index'))
        ->assertOk();
});

test('a guest is sent to login', function () {
    $this->get(route('me.index'))->assertRedirect(route('login'));
});
