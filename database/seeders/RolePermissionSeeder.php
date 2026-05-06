<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'dashboard.view',

            'users.view',
            'users.create',
            'users.edit',
            'users.delete',

            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',

            'departments.view',
            'departments.create',
            'departments.edit',
            'departments.delete',

            'employees.view',
            'employees.create',
            'employees.edit',
            'employees.delete',

            'assets.view',
            'assets.create',
            'assets.edit',
            'assets.delete',

            'computers.view',
            'computers.create',
            'computers.edit',
            'computers.delete',

            'software.view',
            'software.create',
            'software.edit',
            'software.delete',

            'repairs.view',
            'repairs.create',
            'repairs.edit',
            'repairs.delete',

            'attendance.view',
            'attendance.import',
            'attendance.edit',

            'payroll.view',
            'payroll.calculate',
            'payroll.slip',

            'shifts.view',
            'shifts.create',
            'shifts.edit',
            'shifts.delete',

            'rooms.view',
            'rooms.book',
            'rooms.approve',

            'vehicles.view',
            'vehicles.request',
            'vehicles.approve',

            'leaves.view',
            'leaves.request',
            'leaves.approve',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $superAdmin = Role::firstOrCreate([
            'name' => 'super-admin',
            'guard_name' => 'web',
        ]);

        $admin = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        $it = Role::firstOrCreate([
            'name' => 'it',
            'guard_name' => 'web',
        ]);

        $hr = Role::firstOrCreate([
            'name' => 'hr',
            'guard_name' => 'web',
        ]);

        $staff = Role::firstOrCreate([
            'name' => 'staff',
            'guard_name' => 'web',
        ]);

        $superAdmin->syncPermissions($permissions);

        $admin->syncPermissions([
            'dashboard.view',

            'users.view',
            'departments.view',

            'employees.view',
            'employees.create',
            'employees.edit',

            'assets.view',
            'assets.create',
            'assets.edit',

            'computers.view',
            'computers.create',
            'computers.edit',

            'software.view',
            'software.create',
            'software.edit',

            'repairs.view',
            'repairs.create',
            'repairs.edit',

            'rooms.view',
            'rooms.book',
            'rooms.approve',

            'vehicles.view',
            'vehicles.request',
            'vehicles.approve',

            'leaves.view',
            'leaves.request',
            'leaves.approve',
        ]);

        $it->syncPermissions([
            'dashboard.view',

            'computers.view',
            'computers.create',
            'computers.edit',

            'software.view',
            'software.create',
            'software.edit',

            'repairs.view',
            'repairs.create',
            'repairs.edit',
        ]);

        $hr->syncPermissions([
            'dashboard.view',

            'employees.view',
            'employees.create',
            'employees.edit',

            'attendance.view',
            'attendance.import',
            'attendance.edit',

            'payroll.view',
            'payroll.calculate',
            'payroll.slip',

            'shifts.view',
            'shifts.create',
            'shifts.edit',

            'leaves.view',
            'leaves.approve',
        ]);

        $staff->syncPermissions([
            'dashboard.view',
            'repairs.create',
            'rooms.book',
            'vehicles.request',
            'leaves.request',
        ]);

        $user = User::first();

        if ($user) {
            $user->assignRole('super-admin');
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
