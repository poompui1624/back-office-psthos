<?php

use App\Models\Department;
use App\Models\DutySchedule;
use App\Models\Employee;
use App\Models\ShiftType;
use App\Models\User;
use Spatie\Permission\Models\Permission;

function printUser(string ...$extra): User
{
    $user = User::factory()->create();

    foreach (array_merge(['duty.view', 'duty.view.all'], $extra) as $permission) {
        Permission::findOrCreate($permission);
        $user->givePermissionTo($permission);
    }

    return $user;
}

test('the roster prints one row per person with their shift codes', function () {
    $employee = Employee::factory()->create(['employee_code' => 'EMP00042']);
    $shift = ShiftType::factory()->morning()->create(['code' => 'M1', 'name' => 'เวรเช้า']);

    DutySchedule::factory()->forEmployee($employee)->for($shift, 'shiftType')
        ->window('2026-08-05 08:00', '2026-08-05 16:00')->create();

    $this->actingAs(printUser())
        ->get(route('duty-schedules.print', ['month' => 8, 'year' => 2569]))
        ->assertOk()
        ->assertSee('EMP00042')
        ->assertSee('M1')
        ->assertSee('เวรเช้า');
});

test('an empty month says so rather than printing a blank grid', function () {
    $this->actingAs(printUser())
        ->get(route('duty-schedules.print', ['month' => 8, 'year' => 2569]))
        ->assertOk()
        ->assertSee('ยังไม่มีการจัดเวรในเดือนนี้');
});

test('cancelled shifts are left off the printed roster', function () {
    $employee = Employee::factory()->create(['employee_code' => 'EMP00042']);
    $shift = ShiftType::factory()->morning()->create(['code' => 'M1']);

    DutySchedule::factory()->forEmployee($employee)->for($shift, 'shiftType')
        ->window('2026-08-05 08:00', '2026-08-05 16:00')->cancelled()->create();

    $this->actingAs(printUser())
        ->get(route('duty-schedules.print', ['month' => 8, 'year' => 2569]))
        ->assertOk()
        ->assertSee('ยังไม่มีการจัดเวรในเดือนนี้');
});

test('another month is not printed', function () {
    $employee = Employee::factory()->create(['employee_code' => 'EMP00042']);
    $shift = ShiftType::factory()->morning()->create(['code' => 'M1']);

    DutySchedule::factory()->forEmployee($employee)->for($shift, 'shiftType')
        ->window('2026-07-05 08:00', '2026-07-05 16:00')->create();

    $this->actingAs(printUser())
        ->get(route('duty-schedules.print', ['month' => 8, 'year' => 2569]))
        ->assertOk()
        ->assertDontSee('EMP00042');
});

test('the roster can be narrowed to one department', function () {
    $ward = Department::factory()->create();
    $office = Department::factory()->create();
    $shift = ShiftType::factory()->morning()->create(['code' => 'M1']);

    $inWard = Employee::factory()->inDepartment($ward)->create(['employee_code' => 'EMP-WARD']);
    $inOffice = Employee::factory()->inDepartment($office)->create(['employee_code' => 'EMP-OFFICE']);

    foreach ([$inWard, $inOffice] as $employee) {
        DutySchedule::factory()->forEmployee($employee)->for($shift, 'shiftType')
            ->window('2026-08-05 08:00', '2026-08-05 16:00')->create();
    }

    $this->actingAs(printUser())
        ->get(route('duty-schedules.print', ['month' => 8, 'year' => 2569, 'department_id' => $ward->id]))
        ->assertOk()
        ->assertSee('EMP-WARD')
        ->assertDontSee('EMP-OFFICE');
});

test('printing without hospital wide access shows only your own department', function () {
    $ward = Department::factory()->create();
    $office = Department::factory()->create();
    $shift = ShiftType::factory()->morning()->create(['code' => 'M1']);

    $mine = Employee::factory()->inDepartment($ward)->create(['employee_code' => 'EMP-WARD']);
    $theirs = Employee::factory()->inDepartment($office)->create(['employee_code' => 'EMP-OFFICE']);

    foreach ([$mine, $theirs] as $employee) {
        DutySchedule::factory()->forEmployee($employee)->for($shift, 'shiftType')
            ->window('2026-08-05 08:00', '2026-08-05 16:00')->create();
    }

    $supervisor = User::factory()->create(['employee_id' => $mine->id]);
    Permission::findOrCreate('duty.view');
    $supervisor->givePermissionTo('duty.view');

    $this->actingAs($supervisor)
        ->get(route('duty-schedules.print', ['month' => 8, 'year' => 2569]))
        ->assertOk()
        ->assertSee('EMP-WARD')
        ->assertDontSee('EMP-OFFICE');
});

test('printing needs the duty view permission', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('duty-schedules.print'))
        ->assertForbidden();
});
