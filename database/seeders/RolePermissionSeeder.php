<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'dashboard.view',

            'approval.view',
            'approval.approve',
            'approval.reject',

            'attachment.upload',
            'attachment.download',
            'attachment.delete',

            'audit.view',

            'department.view',
            'department.create',
            'department.update',
            'department.delete',

            'position.view',
            'position.create',
            'position.update',
            'position.delete',

            'employee.view',
            'employee.create',
            'employee.update',
            'employee.delete',
            'employee.sensitive.view',
            'employee.sensitive.update',

            'user.view',
            'user.create',
            'user.update',
            'user.delete',

            'setting.view',
            'setting.update',

            'asset.view',
            'asset.create',
            'asset.update',
            'asset.delete',
            'asset.movement',

            'computer.view',
            'computer.create',
            'computer.update',
            'computer.delete',
            'computer.agent_manage',

            'software.view',
            'software.create',
            'software.update',
            'software.delete',

            'repair.view',
            'repair.create',
            'repair.update',
            'repair.delete',

            'leave.view',
            'leave.create',
            'leave.update',
            'leave.delete',
            'leave.approve',
            'leave.create.any',

            'attendance.view',
            'attendance.create',
            'attendance.update',
            'attendance.delete',
            'attendance.import',

            'duty.view',
            'duty.create',
            'duty.update',
            'duty.delete',

            'payroll.view',
            'payroll.create',
            'payroll.update',
            'payroll.delete',
            'payroll.generate',

            'meeting.view',
            'meeting.create',
            'meeting.update',
            'meeting.delete',
            'meeting.approve',

            'ita.view',
            'ita.create',
            'ita.edit',
            'ita.delete',
            'ita.topic.manage',

            'site.view',
            'site.manage',

            'leave.view.own',
            'duty.view.own',
            'attendance.view.own',
            'repair.view.own',
            'meeting.view.own',
            'payslip.view.own',

            'leave.view.all',
            'duty.view.all',
            'attendance.view.all',
            'repair.view.all',
            'asset.view.all',
            'payroll.view.all',
            'employee.view.all',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $superAdmin = Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => 'web',
        ]);

        $legacySuperAdmin = Role::firstOrCreate([
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

        $supervisor = Role::firstOrCreate([
            'name' => 'supervisor',
            'guard_name' => 'web',
        ]);

        $publicRelations = Role::firstOrCreate([
            'name' => 'pr',
            'guard_name' => 'web',
        ]);

        $staff = Role::firstOrCreate([
            'name' => 'staff',
            'guard_name' => 'web',
        ]);

        $superAdmin->syncPermissions($permissions);
        $legacySuperAdmin->syncPermissions($permissions);

        $admin->syncPermissions([
            'dashboard.view',

            'approval.view',
            'approval.approve',
            'approval.reject',

            'attachment.upload',
            'attachment.download',
            'attachment.delete',

            'audit.view',

            'user.view',
            'user.create',
            'user.update',

            'department.view',
            'department.create',
            'department.update',

            'position.view',
            'position.create',
            'position.update',

            'employee.view',
            'employee.create',
            'employee.update',
            'employee.sensitive.view',
            'employee.sensitive.update',

            'setting.view',
            'setting.update',

            'asset.view',
            'asset.create',
            'asset.update',
            'asset.movement',

            'computer.view',
            'computer.create',
            'computer.update',
            'computer.agent_manage',

            'software.view',
            'software.create',
            'software.update',

            'repair.view',
            'repair.create',
            'repair.update',

            'meeting.view',
            'meeting.create',
            'meeting.update',
            'meeting.approve',

            'leave.view',
            'leave.create',
            'leave.update',
            'leave.approve',

            'attendance.view',
            'attendance.import',

            'duty.view',
            'duty.create',
            'duty.update',

            'payroll.view',
            'payroll.generate',

            'ita.view',
            'ita.create',
            'ita.edit',
            'ita.topic.manage',

            'site.view',
            'site.manage',
            'leave.view.all',
            'duty.view.all',
            'attendance.view.all',
            'repair.view.all',
            'asset.view.all',
            'payroll.view.all',
            'employee.view.all',

            'leave.create.any',
        ]);

        $it->syncPermissions([
            'dashboard.view',

            'attachment.upload',
            'attachment.download',
            'attachment.delete',

            'computer.view',
            'computer.create',
            'computer.update',
            'computer.agent_manage',

            'software.view',
            'software.create',
            'software.update',

            'repair.view',
            'repair.create',
            'repair.update',
        ]);

        $hr->syncPermissions([
            'dashboard.view',

            'approval.view',
            'approval.approve',
            'approval.reject',

            'attachment.upload',
            'attachment.download',

            'department.view',
            'position.view',

            'employee.view',
            'employee.create',
            'employee.update',
            'employee.sensitive.view',
            'employee.sensitive.update',

            'attendance.view',
            'attendance.import',
            'attendance.update',

            'payroll.view',
            'payroll.generate',

            'duty.view',
            'duty.create',
            'duty.update',

            'leave.view',
            'leave.create',
            'leave.update',
            'leave.approve',
            'leave.view.all',
            'duty.view.all',
            'attendance.view.all',
            'repair.view.all',
            'asset.view.all',
            'payroll.view.all',
            'employee.view.all',

            'leave.create.any',

        ]);

        // A head of department approves for their own unit. Withholding the
        // .view.all permissions is what keeps them scoped to it.
        $supervisor->syncPermissions([
            'dashboard.view',

            'attachment.upload',
            'attachment.download',

            'employee.view',

            'leave.view',
            'leave.create',
            'leave.update',
            'leave.approve',

            'duty.view',
            'duty.create',
            'duty.update',

            'attendance.view',

            'repair.view',
            'repair.create',

            'asset.view',

            'meeting.view',
            'meeting.create',

            'leave.view.own',
            'duty.view.own',
            'attendance.view.own',
            'repair.view.own',
            'meeting.view.own',
            'payslip.view.own',
            'leave.create.any',

        ]);

        // Keeps the public site current without reaching the rest of the system.
        $publicRelations->syncPermissions([
            'dashboard.view',
            'attachment.upload',
            'attachment.download',
            'site.view',
            'site.manage',
        ]);

        $staff->syncPermissions([
            'dashboard.view',
            'attachment.upload',
            'attachment.download',
            'repair.create',
            'meeting.create',
            'leave.create',

            'leave.view.own',
            'duty.view.own',
            'attendance.view.own',
            'repair.view.own',
            'meeting.view.own',
            'payslip.view.own',
        ]);

        $user = User::first();

        if ($user) {
            $user->assignRole('super_admin');
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
