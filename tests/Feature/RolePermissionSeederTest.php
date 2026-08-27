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
