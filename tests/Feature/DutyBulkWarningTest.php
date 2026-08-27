<?php

use App\Models\DutySchedule;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\ShiftType;
use App\Models\User;
use Spatie\Permission\Models\Permission;

function rosterUser(): User
{
    $user = User::factory()->create();

    foreach (['duty.create', 'duty.view', 'duty.view.all'] as $permission) {
        Permission::findOrCreate($permission);
        $user->givePermissionTo($permission);
    }

    return $user;
}

function bulkPayload(Employee $employee, ShiftType $shift, array $overrides = []): array
{
    return array_merge([
        'employee_ids' => [$employee->id],
        'shift_type_id' => $shift->id,
        'date_from' => '2026-08-05',
        'date_to' => '2026-08-05',
        'weekdays' => [0, 1, 2, 3, 4, 5, 6],
        'status' => 'assigned',
    ], $overrides);
}

test('a clash with approved leave is reported but the roster is still saved', function () {
    $employee = Employee::factory()->create();
    $shift = ShiftType::factory()->morning()->create();

    LeaveRequest::factory()->forEmployee($employee)->approved()
        ->between('2026-08-05', '2026-08-05')->create();

    $response = $this->actingAs(rosterUser())
        ->post(route('duty-schedules.bulk-store'), bulkPayload($employee, $shift));

    $response->assertRedirect(route('duty-schedules.index'))
        ->assertSessionHas('duty_warnings');

    // The warning does not block the write: the ward still gets its roster.
    expect(DutySchedule::count())->toBe(1)
        ->and(session('duty_warnings'))->toHaveCount(1)
        ->and(session('duty_warnings')[0])->toContain($employee->full_name);
});

test('a clean roster reports no warnings', function () {
    $employee = Employee::factory()->create();
    $shift = ShiftType::factory()->morning()->create();

    $this->actingAs(rosterUser())
        ->post(route('duty-schedules.bulk-store'), bulkPayload($employee, $shift));

    expect(DutySchedule::count())->toBe(1)
        ->and(session('duty_warnings'))->toBe([]);
});

test('the warnings are shown on the roster page', function () {
    $employee = Employee::factory()->create();
    $shift = ShiftType::factory()->morning()->create();

    LeaveRequest::factory()->forEmployee($employee)->approved()
        ->between('2026-08-05', '2026-08-05')->create();

    $this->actingAs(rosterUser())
        ->post(route('duty-schedules.bulk-store'), bulkPayload($employee, $shift))
        ->assertRedirect(route('duty-schedules.index'));

    $this->actingAs(rosterUser())
        ->get(route('duty-schedules.index'))
        ->assertOk();
});

test('warnings name every affected day', function () {
    $employee = Employee::factory()->create();
    $shift = ShiftType::factory()->morning()->create();

    LeaveRequest::factory()->forEmployee($employee)->approved()
        ->between('2026-08-05', '2026-08-07')->create();

    $this->actingAs(rosterUser())->post(
        route('duty-schedules.bulk-store'),
        bulkPayload($employee, $shift, ['date_from' => '2026-08-05', 'date_to' => '2026-08-07'])
    );

    expect(DutySchedule::count())->toBe(3)
        ->and(session('duty_warnings'))->toHaveCount(3);
});
