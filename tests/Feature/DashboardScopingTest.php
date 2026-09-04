<?php

use App\Models\AttendanceDailySummary;
use App\Models\Department;
use App\Models\DutySchedule;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\ShiftType;
use App\Models\User;
use Spatie\Permission\Models\Permission;

function scopedUser(?Employee $employee, string ...$permissions): User
{
    $user = User::factory()->create(['employee_id' => $employee?->id]);

    foreach ($permissions as $permission) {
        Permission::findOrCreate($permission);
        $user->givePermissionTo($permission);
    }

    return $user;
}

test('the leave dashboard counts only the viewers department', function () {
    $ward = Department::factory()->create();
    $office = Department::factory()->create();

    $mine = Employee::factory()->inDepartment($ward)->create();

    LeaveRequest::factory()->forEmployee($mine)->approved()
        ->between(now()->toDateString(), now()->toDateString())
        ->create(['request_no' => 'LV-WARD-0001']);

    LeaveRequest::factory()
        ->forEmployee(Employee::factory()->inDepartment($office)->create())
        ->approved()
        ->between(now()->toDateString(), now()->toDateString())
        ->create(['request_no' => 'LV-OFFICE-0001']);

    $this->actingAs(scopedUser($mine, 'leave.view'))
        ->get(route('leave-requests.dashboard'))
        ->assertOk()
        ->assertSee('LV-WARD-0001')
        ->assertDontSee('LV-OFFICE-0001');
});

test('hospital wide access restores the whole leave dashboard', function () {
    $ward = Department::factory()->create();
    $office = Department::factory()->create();

    $mine = Employee::factory()->inDepartment($ward)->create();

    LeaveRequest::factory()->forEmployee($mine)->approved()
        ->between(now()->toDateString(), now()->toDateString())
        ->create(['request_no' => 'LV-WARD-0001']);

    LeaveRequest::factory()
        ->forEmployee(Employee::factory()->inDepartment($office)->create())
        ->approved()
        ->between(now()->toDateString(), now()->toDateString())
        ->create(['request_no' => 'LV-OFFICE-0001']);

    $this->actingAs(scopedUser($mine, 'leave.view', 'leave.view.all'))
        ->get(route('leave-requests.dashboard'))
        ->assertOk()
        ->assertSee('LV-WARD-0001')
        ->assertSee('LV-OFFICE-0001');
});

test('the leave calendar is scoped too', function () {
    $ward = Department::factory()->create();
    $office = Department::factory()->create();

    $mine = Employee::factory()->inDepartment($ward)->create(['first_name' => 'วอร์ด', 'last_name' => 'ทดสอบ']);
    $theirs = Employee::factory()->inDepartment($office)->create(['first_name' => 'ออฟฟิศ', 'last_name' => 'ทดสอบ']);

    foreach ([$mine, $theirs] as $employee) {
        LeaveRequest::factory()->forEmployee($employee)->approved()
            ->between(now()->toDateString(), now()->toDateString())->create();
    }

    $this->actingAs(scopedUser($mine, 'leave.view'))
        ->get(route('leave-requests.calendar'))
        ->assertOk()
        ->assertSee('วอร์ด')
        ->assertDontSee('ออฟฟิศ');
});

test('the duty dashboard counts only the viewers department', function () {
    $ward = Department::factory()->create();
    $office = Department::factory()->create();
    $shift = ShiftType::factory()->morning()->create();

    $mine = Employee::factory()->inDepartment($ward)->create(['employee_code' => 'EMP-WARD']);

    DutySchedule::factory()->forEmployee($mine)->for($shift, 'shiftType')
        ->onDate(now()->toDateString())->create();

    DutySchedule::factory()
        ->forEmployee(Employee::factory()->inDepartment($office)->create(['employee_code' => 'EMP-OFFICE']))
        ->for($shift, 'shiftType')
        ->onDate(now()->toDateString())
        ->create();

    $this->actingAs(scopedUser($mine, 'duty.view'))
        ->get(route('duty-schedules.index'))
        ->assertOk()
        ->assertSee('EMP-WARD')
        ->assertDontSee('EMP-OFFICE');
});

test('the attendance dashboard is scoped through the employee', function () {
    $ward = Department::factory()->create();
    $office = Department::factory()->create();

    $mine = Employee::factory()->inDepartment($ward)->create();
    $theirs = Employee::factory()->inDepartment($office)->create();

    AttendanceDailySummary::factory()->forEmployee($mine)->onDate(now()->toDateString())->late(30)->create();

    // one row per employee per day is enforced by a unique index
    foreach (range(1, 4) as $daysAgo) {
        AttendanceDailySummary::factory()->forEmployee($theirs)
            ->onDate(now()->subDays($daysAgo)->toDateString())->late(30)->create();
    }

    // attendance_daily_summaries carries no department of its own, so the scope
    // has to reach it through the employee.
    $scoped = AttendanceDailySummary::query()
        ->visibleTo(scopedUser($mine, 'attendance.view'))
        ->count();

    $wide = AttendanceDailySummary::query()
        ->visibleTo(scopedUser($mine, 'attendance.view', 'attendance.view.all'))
        ->count();

    expect($scoped)->toBe(1)->and($wide)->toBe(5);

    $this->actingAs(scopedUser($mine, 'attendance.view'))
        ->get(route('attendance-summaries.dashboard'))
        ->assertOk();
});

test('an unlinked account sees nothing on the attendance dashboard', function () {
    AttendanceDailySummary::factory()->count(3)->create();

    expect(AttendanceDailySummary::query()->visibleTo(scopedUser(null, 'attendance.view'))->count())->toBe(0);
});
