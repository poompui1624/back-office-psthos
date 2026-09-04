<?php

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\AttendanceDailySummary;
use App\Models\Department;
use App\Models\DutySchedule;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\MeetingBooking;
use App\Models\MeetingRoom;
use App\Models\PayrollPeriod;
use App\Models\Position;
use App\Models\RepairRequest;
use App\Models\SalaryProfile;
use App\Models\ShiftType;

test('every factory builds a persistable model', function (string $model) {
    $record = $model::factory()->create();

    expect($record->exists)->toBeTrue()
        ->and($model::whereKey($record->getKey())->exists())->toBeTrue();
})->with([
    Department::class,
    Position::class,
    Employee::class,
    LeaveType::class,
    LeaveRequest::class,
    ShiftType::class,
    DutySchedule::class,
    AssetCategory::class,
    Asset::class,
    MeetingRoom::class,
    MeetingBooking::class,
    RepairRequest::class,
    SalaryProfile::class,
    PayrollPeriod::class,
    AttendanceDailySummary::class,
]);

test('factories can build several records without unique collisions', function (string $model) {
    expect($model::factory()->count(3)->create())->toHaveCount(3);
})->with([
    Department::class,
    Employee::class,
    LeaveType::class,
    ShiftType::class,
    LeaveRequest::class,
    Asset::class,
    MeetingBooking::class,
    RepairRequest::class,
]);

test('an employee factory nests its own department and position', function () {
    $employee = Employee::factory()->create();

    expect($employee->department)->not->toBeNull()
        ->and($employee->position)->not->toBeNull();
});

test('a leave request inherits the department of its employee', function () {
    $department = Department::factory()->create();
    $employee = Employee::factory()->inDepartment($department)->create();

    $leave = LeaveRequest::factory()->forEmployee($employee)->create();

    expect($leave->department_id)->toBe($department->id)
        ->and($leave->employee_id)->toBe($employee->id);
});

test('leave request states set the matching status columns', function () {
    expect(LeaveRequest::factory()->pending()->create()->status)->toBe('pending')
        ->and(LeaveRequest::factory()->approved()->create()->approved_at)->not->toBeNull()
        ->and(LeaveRequest::factory()->rejected()->create()->rejected_at)->not->toBeNull()
        ->and(LeaveRequest::factory()->cancelled()->create()->cancelled_at)->not->toBeNull();
});

test('the leave between state pins an exact range', function () {
    $leave = LeaveRequest::factory()->between('2026-08-03', '2026-08-05')->create();

    expect($leave->start_date->toDateString())->toBe('2026-08-03')
        ->and($leave->end_date->toDateString())->toBe('2026-08-05')
        ->and((float) $leave->total_days)->toBe(3.0);
});

test('the night shift state crosses midnight', function () {
    $shift = ShiftType::factory()->night()->create();

    expect($shift->crosses_midnight)->toBeTrue()
        ->and($shift->start_time)->toBe('00:00:00');
});

test('attendance states drive the payroll deduction inputs', function () {
    $late = AttendanceDailySummary::factory()->late(45)->create();
    $absent = AttendanceDailySummary::factory()->absent()->create();

    expect($late->late_minutes)->toBe(45)
        ->and($late->status)->toBe('late')
        ->and($absent->status)->toBe('absent')
        ->and($absent->work_minutes)->toBe(0);
});

test('payroll period states move the period through its lifecycle', function () {
    expect(PayrollPeriod::factory()->create()->status)->toBe('draft')
        ->and(PayrollPeriod::factory()->generated()->create()->generated_at)->not->toBeNull()
        ->and(PayrollPeriod::factory()->closed()->create()->status)->toBe('closed');
});

test('a payroll period can be pinned to a given month', function () {
    $period = PayrollPeriod::factory()->forMonth(2026, 2)->create();

    expect($period->start_date->toDateString())->toBe('2026-02-01')
        ->and($period->end_date->toDateString())->toBe('2026-02-28');
});
