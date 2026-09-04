<?php

use App\Models\DutySchedule;
use App\Models\Employee;
use App\Models\SalaryProfile;
use App\Models\ShiftType;
use App\Services\OtCalculationService;

test('hours come from the stored window', function () {
    $duty = DutySchedule::factory()->window('2026-08-03 08:00', '2026-08-03 16:00')->create();

    expect(OtCalculationService::hoursFor($duty))->toBe(8.0);
});

test('a shift crossing midnight is measured across both days', function () {
    // end_at is a full datetime, so the night shift spans 3rd -> 4th.
    $duty = DutySchedule::factory()->window('2026-08-03 22:00', '2026-08-04 06:00')->create();

    expect(OtCalculationService::hoursFor($duty))->toBe(8.0);
});

test('half hours are kept', function () {
    $duty = DutySchedule::factory()->window('2026-08-03 08:00', '2026-08-03 12:30')->create();

    expect(OtCalculationService::hoursFor($duty))->toBe(4.5);
});

test('a window that does not move forward pays nothing rather than a negative', function () {
    $duty = DutySchedule::factory()->window('2026-08-03 08:00', '2026-08-03 08:00')->create();

    expect(OtCalculationService::hoursFor($duty))->toBe(0.0);
});

test('a shift not marked as overtime pays nothing', function () {
    $profile = SalaryProfile::factory()->withOtRate(100)->create();

    $duty = DutySchedule::factory()
        ->for(ShiftType::factory()->morning(), 'shiftType')
        ->window('2026-08-03 08:00', '2026-08-03 16:00')
        ->confirmed()
        ->create();

    expect(OtCalculationService::amountFor($duty, $profile))->toBe(0.0);
});

test('overtime pays hours times rate times multiplier', function () {
    $profile = SalaryProfile::factory()->withOtRate(100)->create();

    $duty = DutySchedule::factory()
        ->for(ShiftType::factory()->overtime(1.5), 'shiftType')
        ->window('2026-08-03 18:00', '2026-08-03 22:00')
        ->confirmed()
        ->create();

    // 4 hours x 100 x 1.5
    expect(OtCalculationService::amountFor($duty, $profile))->toBe(600.0);
});

test('a flat rate shift ignores both hours and multiplier', function () {
    $profile = SalaryProfile::factory()->withOtRate(100)->create();

    $duty = DutySchedule::factory()
        ->for(ShiftType::factory()->overtimeFlat(750), 'shiftType')
        ->window('2026-08-03 18:00', '2026-08-04 02:00')
        ->confirmed()
        ->create();

    expect(OtCalculationService::amountFor($duty, $profile))->toBe(750.0);
});

test('an employee with no hourly rate earns nothing from a multiplier shift', function () {
    $profile = SalaryProfile::factory()->create(['ot_rate_per_hour' => 0]);

    $duty = DutySchedule::factory()
        ->for(ShiftType::factory()->overtime(2), 'shiftType')
        ->window('2026-08-03 18:00', '2026-08-03 22:00')
        ->confirmed()
        ->create();

    expect(OtCalculationService::amountFor($duty, $profile))->toBe(0.0);
});

test('only confirmed shifts are paid', function () {
    $employee = Employee::factory()->create();
    $profile = SalaryProfile::factory()->forEmployee($employee)->withOtRate(100)->create();
    $otShift = ShiftType::factory()->overtime(1)->create();

    // Assigned is still a plan; cancelled did not happen.
    foreach (['assigned', 'confirmed', 'cancelled'] as $status) {
        DutySchedule::factory()
            ->forEmployee($employee)
            ->for($otShift, 'shiftType')
            ->window('2026-08-0'.(['assigned' => 3, 'confirmed' => 4, 'cancelled' => 5][$status]).' 18:00',
                '2026-08-0'.(['assigned' => 3, 'confirmed' => 4, 'cancelled' => 5][$status]).' 22:00')
            ->create(['status' => $status]);
    }

    $total = OtCalculationService::forEmployee($employee->id, '2026-08-01', '2026-08-31', $profile);

    expect($total['shifts'])->toBe(1)
        ->and($total['hours'])->toBe(4.0)
        ->and($total['amount'])->toBe(400.0);
});

test('shifts outside the range are not counted', function () {
    $employee = Employee::factory()->create();
    $profile = SalaryProfile::factory()->forEmployee($employee)->withOtRate(100)->create();
    $otShift = ShiftType::factory()->overtime(1)->create();

    foreach (['2026-07-31', '2026-08-15', '2026-09-01'] as $date) {
        DutySchedule::factory()
            ->forEmployee($employee)
            ->for($otShift, 'shiftType')
            ->window("{$date} 18:00", "{$date} 22:00")
            ->confirmed()
            ->create();
    }

    $total = OtCalculationService::forEmployee($employee->id, '2026-08-01', '2026-08-31', $profile);

    expect($total['shifts'])->toBe(1)
        ->and($total['amount'])->toBe(400.0);
});

test('one employees overtime is not another employees', function () {
    $mine = Employee::factory()->create();
    $theirs = Employee::factory()->create();
    $profile = SalaryProfile::factory()->forEmployee($mine)->withOtRate(100)->create();
    $otShift = ShiftType::factory()->overtime(1)->create();

    DutySchedule::factory()->forEmployee($mine)->for($otShift, 'shiftType')
        ->window('2026-08-04 18:00', '2026-08-04 22:00')->confirmed()->create();

    DutySchedule::factory()->forEmployee($theirs)->for($otShift, 'shiftType')
        ->window('2026-08-05 18:00', '2026-08-05 22:00')->confirmed()->create();

    expect(OtCalculationService::forEmployee($mine->id, '2026-08-01', '2026-08-31', $profile)['shifts'])->toBe(1);
});

test('an employee with no overtime totals zero', function () {
    $employee = Employee::factory()->create();

    $total = OtCalculationService::forEmployee($employee->id, '2026-08-01', '2026-08-31', null);

    expect($total)->toBe(['shifts' => 0, 'hours' => 0.0, 'amount' => 0.0]);
});
