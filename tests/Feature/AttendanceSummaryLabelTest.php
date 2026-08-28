<?php

use App\Models\AttendanceDailySummary;
use App\Models\Employee;
use App\Models\User;
use Spatie\Permission\Models\Permission;

test('the summary list shows the Thai status label, not the raw value', function () {
    $employee = Employee::factory()->create();

    AttendanceDailySummary::factory()
        ->forEmployee($employee)
        ->onDate(now()->toDateString())
        ->late(15)
        ->create();

    $user = User::factory()->create(['employee_id' => $employee->id]);

    foreach (['attendance.view', 'attendance.view.all'] as $permission) {
        Permission::findOrCreate($permission);
        $user->givePermissionTo($permission);
    }

    // The label map lives in a @php block; if it were declared inside a
    // component slot it would fall out of scope here and the badge would
    // silently print "late" instead.
    $this->actingAs($user)
        ->get(route('attendance-summaries.index'))
        ->assertOk()
        ->assertSee('มาสาย')
        ->assertDontSee('>late<', false);
});
