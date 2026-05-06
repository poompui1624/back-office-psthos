<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class CoreSeeder extends Seeder
{
    public function run(): void
    {
        $hospital = Department::firstOrCreate(
            ['code' => 'HOSP'],
            [
                'name' => 'โรงพยาบาล',
                'type' => 'organization',
                'is_active' => true,
            ]
        );

        $nursing = Department::firstOrCreate(
            ['code' => 'NURSE'],
            [
                'parent_id' => $hospital->id,
                'name' => 'กลุ่มการพยาบาล',
                'type' => 'department_group',
                'is_active' => true,
            ]
        );

        $adminGroup = Department::firstOrCreate(
            ['code' => 'ADMIN'],
            [
                'parent_id' => $hospital->id,
                'name' => 'ฝ่ายบริหาร',
                'type' => 'department_group',
                'is_active' => true,
            ]
        );

        $it = Department::firstOrCreate(
            ['code' => 'IT'],
            [
                'parent_id' => $adminGroup->id,
                'name' => 'เทคโนโลยีสารสนเทศ',
                'type' => 'unit',
                'is_active' => true,
            ]
        );

        Department::firstOrCreate(
            ['code' => 'ER'],
            [
                'parent_id' => $nursing->id,
                'name' => 'ห้องฉุกเฉิน',
                'type' => 'unit',
                'is_active' => true,
            ]
        );

        Department::firstOrCreate(
            ['code' => 'OPD'],
            [
                'parent_id' => $nursing->id,
                'name' => 'ผู้ป่วยนอก',
                'type' => 'unit',
                'is_active' => true,
            ]
        );

        Department::firstOrCreate(
            ['code' => 'IPD'],
            [
                'parent_id' => $nursing->id,
                'name' => 'ผู้ป่วยใน',
                'type' => 'unit',
                'is_active' => true,
            ]
        );

        Department::firstOrCreate(
            ['code' => 'HR'],
            [
                'parent_id' => $adminGroup->id,
                'name' => 'งานทรัพยากรบุคคล',
                'type' => 'unit',
                'is_active' => true,
            ]
        );

        Department::firstOrCreate(
            ['code' => 'FINANCE'],
            [
                'parent_id' => $adminGroup->id,
                'name' => 'การเงิน',
                'type' => 'unit',
                'is_active' => true,
            ]
        );

        Department::firstOrCreate(
            ['code' => 'ASSET'],
            [
                'parent_id' => $adminGroup->id,
                'name' => 'พัสดุ',
                'type' => 'unit',
                'is_active' => true,
            ]
        );

        $systemAdminPosition = Position::firstOrCreate(
            ['name' => 'ผู้ดูแลระบบ'],
            [
                'level' => 'admin',
                'is_active' => true,
            ]
        );

        Position::firstOrCreate(['name' => 'พยาบาลวิชาชีพ'], ['level' => 'professional', 'is_active' => true]);
        Position::firstOrCreate(['name' => 'เจ้าพนักงานธุรการ'], ['level' => 'staff', 'is_active' => true]);
        Position::firstOrCreate(['name' => 'นักวิชาการคอมพิวเตอร์'], ['level' => 'professional', 'is_active' => true]);
        Position::firstOrCreate(['name' => 'เจ้าหน้าที่พัสดุ'], ['level' => 'staff', 'is_active' => true]);
        Position::firstOrCreate(['name' => 'พนักงานขับรถ'], ['level' => 'staff', 'is_active' => true]);
        Position::firstOrCreate(['name' => 'หัวหน้ากลุ่มงาน'], ['level' => 'head', 'is_active' => true]);

        $adminEmployee = Employee::firstOrCreate(
            ['employee_code' => 'ADMIN001'],
            [
                'prefix' => 'นาย',
                'first_name' => 'System',
                'last_name' => 'Admin',
                'email' => 'admin@hospital.local',
                'department_id' => $it->id,
                'position_id' => $systemAdminPosition->id,
                'employment_type' => 'system',
                'status' => 'active',
            ]
        );

        $adminUser = User::firstOrCreate(
            ['email' => 'admin@hospital.local'],
            [
                'employee_id' => $adminEmployee->id,
                'name' => 'System Admin',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );

        $adminUser->update([
            'employee_id' => $adminEmployee->id,
            'is_active' => true,
        ]);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

$superAdminRole = Role::firstOrCreate([
    'name' => 'super_admin',
    'guard_name' => 'web',
]);

$superAdminRole->syncPermissions(Permission::all());

if (! $adminUser->hasRole('super_admin')) {
    $adminUser->assignRole($superAdminRole);
}

app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
