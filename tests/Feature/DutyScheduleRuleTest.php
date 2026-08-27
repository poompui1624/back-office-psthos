<?php

use App\Models\DutySchedule;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\ShiftType;
use App\Services\DutyScheduleRuleService;

test('a clean assignment raises nothing', function () {
    $employee = Employee::factory()->create();
    $shift = ShiftType::factory()->morning()->create();

    expect(DutyScheduleRuleService::warningsFor($employee->id, $shift, '2026-08-05'))->toBe([]);
});

test('assigning over approved leave is flagged with the request number', function () {
    $employee = Employee::factory()->create();
    $shift = ShiftType::factory()->morning()->create();

    LeaveRequest::factory()->forEmployee($employee)->approved()
        ->between('2026-08-04', '2026-08-06')
        ->create(['request_no' => 'LV202608040001']);

    $warnings = DutyScheduleRuleService::warningsFor($employee->id, $shift, '2026-08-05');

    expect($warnings)->toHaveCount(1)
        ->and($warnings[0])->toContain('LV202608040001');
});

test('leave that is not approved is not flagged', function () {
    $employee = Employee::factory()->create();
    $shift = ShiftType::factory()->morning()->create();

    LeaveRequest::factory()->forEmployee($employee)->pending()
        ->between('2026-08-04', '2026-08-06')->create();

    expect(DutyScheduleRuleService::warningsFor($employee->id, $shift, '2026-08-05'))->toBe([]);
});

test('back to back night shifts are flagged', function () {
    $employee = Employee::factory()->create();
    $night = ShiftType::factory()->night()->create();

    DutySchedule::factory()->forEmployee($employee)->for($night, 'shiftType')
        ->window('2026-08-04 00:00', '2026-08-04 08:00')->create();

    $warnings = DutyScheduleRuleService::warningsFor($employee->id, $night, '2026-08-05');

    expect($warnings)->toHaveCount(1)
        ->and($warnings[0])->toContain('เวรดึกติดกัน');
});

test('a night shift with a gap is fine', function () {
    $employee = Employee::factory()->create();
    $night = ShiftType::factory()->night()->create();

    DutySchedule::factory()->forEmployee($employee)->for($night, 'shiftType')
        ->window('2026-08-01 00:00', '2026-08-01 08:00')->create();

    expect(DutyScheduleRuleService::warningsFor($employee->id, $night, '2026-08-05'))->toBe([]);
});

test('a cancelled night shift does not count as adjacent', function () {
    $employee = Employee::factory()->create();
    $night = ShiftType::factory()->night()->create();

    DutySchedule::factory()->forEmployee($employee)->for($night, 'shiftType')
        ->window('2026-08-04 00:00', '2026-08-04 08:00')->cancelled()->create();

    expect(DutyScheduleRuleService::warningsFor($employee->id, $night, '2026-08-05'))->toBe([]);
});

test('a day shift next to a night shift is not flagged for adjacency', function () {
    $employee = Employee::factory()->create();
    $night = ShiftType::factory()->night()->create();
    $morning = ShiftType::factory()->morning()->create();

    DutySchedule::factory()->forEmployee($employee)->for($night, 'shiftType')
        ->window('2026-08-04 00:00', '2026-08-04 08:00')->create();

    // The rule is about consecutive nights, not about working the next day.
    expect(DutyScheduleRuleService::warningsFor($employee->id, $morning, '2026-08-05'))->toBe([]);
});

test('a heavy week is flagged once the new shift tips it over', function () {
    $employee = Employee::factory()->create();
    $shift = ShiftType::factory()->morning()->create();

    // Seven 8-hour shifts in the same week is 56, plus the proposed 8 makes 64.
    foreach (['2026-08-03', '2026-08-04', '2026-08-05', '2026-08-06', '2026-08-07', '2026-08-08', '2026-08-09'] as $date) {
        DutySchedule::factory()->forEmployee($employee)->for($shift, 'shiftType')
            ->window("{$date} 08:00", "{$date} 16:00")->create();
    }

    $warnings = DutyScheduleRuleService::warningsFor($employee->id, $shift, '2026-08-05');

    expect($warnings)->toHaveCount(1)
        ->and($warnings[0])->toContain('เกินเกณฑ์');
});

test('editing an existing shift does not count it against itself', function () {
    $employee = Employee::factory()->create();
    $night = ShiftType::factory()->night()->create();

    $existing = DutySchedule::factory()->forEmployee($employee)->for($night, 'shiftType')
        ->window('2026-08-04 00:00', '2026-08-04 08:00')->create();

    DutySchedule::factory()->forEmployee($employee)->for($night, 'shiftType')
        ->window('2026-08-05 00:00', '2026-08-05 08:00')->create();

    // Re-checking the 5th while ignoring itself still sees the 4th.
    $warnings = DutyScheduleRuleService::warningsFor($employee->id, $night, '2026-08-05', $existing->id + 1);

    expect($warnings)->not->toBeEmpty();
});

test('several problems are reported together', function () {
    $employee = Employee::factory()->create();
    $night = ShiftType::factory()->night()->create();

    LeaveRequest::factory()->forEmployee($employee)->approved()
        ->between('2026-08-05', '2026-08-05')->create();

    DutySchedule::factory()->forEmployee($employee)->for($night, 'shiftType')
        ->window('2026-08-04 00:00', '2026-08-04 08:00')->create();

    expect(DutyScheduleRuleService::warningsFor($employee->id, $night, '2026-08-05'))->toHaveCount(2);
});
