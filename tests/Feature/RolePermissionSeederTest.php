<?php

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

test('role permission seeder creates permissions used by routes and views', function () {
    $this->seed(RolePermissionSeeder::class);

    expect(Permission::where('name', 'leave.view')->exists())->toBeTrue()
        ->and(Permission::where('name', 'duty.create')->exists())->toBeTrue()
        ->and(Permission::where('name', 'setting.update')->exists())->toBeTrue()
        ->and(Role::where('name', 'super_admin')->exists())->toBeTrue();
});

test('cannot delete the last protected admin role account', function () {
    $this->seed(RolePermissionSeeder::class);

    $admin = User::factory()->create();
    $admin->assignRole('super_admin');

    $response = $this
        ->actingAs($admin)
        ->delete(route('users.destroy', $admin));

    $response->assertRedirect(route('users.index'));
    expect(User::whereKey($admin->id)->exists())->toBeTrue();
});

test('staff can see the requests it submits', function () {
    $this->seed(RolePermissionSeeder::class);

    $staff = User::factory()->create();
    $staff->assignRole('staff');

    // Submitting without being able to read back what you submitted is a dead end,
    // so every .create granted to staff has its .own counterpart.
    expect($staff->can('leave.create'))->toBeTrue()
        ->and($staff->can('leave.view.own'))->toBeTrue()
        ->and($staff->can('repair.view.own'))->toBeTrue()
        ->and($staff->can('meeting.view.own'))->toBeTrue()
        ->and($staff->can('payslip.view.own'))->toBeTrue()
        ->and($staff->can('duty.view.own'))->toBeTrue()
        ->and($staff->can('attendance.view.own'))->toBeTrue();
});

test('staff still cannot read other peoples records', function () {
    $this->seed(RolePermissionSeeder::class);

    $staff = User::factory()->create();
    $staff->assignRole('staff');

    expect($staff->can('leave.view'))->toBeFalse()
        ->and($staff->can('leave.approve'))->toBeFalse()
        ->and($staff->can('payroll.view'))->toBeFalse()
        ->and($staff->can('employee.view'))->toBeFalse();
});

test('a supervisor is scoped to their own department by omission', function () {
    $this->seed(RolePermissionSeeder::class);

    $supervisor = User::factory()->create();
    $supervisor->assignRole('supervisor');

    // Withholding .view.all is the whole mechanism — the supervisor has the
    // module permissions but not the hospital-wide ones.
    expect($supervisor->can('leave.view'))->toBeTrue()
        ->and($supervisor->can('leave.approve'))->toBeTrue()
        ->and($supervisor->can('leave.view.all'))->toBeFalse()
        ->and($supervisor->can('duty.view'))->toBeTrue()
        ->and($supervisor->can('duty.view.all'))->toBeFalse()
        ->and($supervisor->can('employee.view.all'))->toBeFalse();
});

test('hr and admin see the whole hospital', function () {
    $this->seed(RolePermissionSeeder::class);

    foreach (['hr', 'admin'] as $role) {
        $user = User::factory()->create();
        $user->assignRole($role);

        expect($user->can('leave.view.all'))->toBeTrue()
            ->and($user->can('employee.view.all'))->toBeTrue();
    }
});
