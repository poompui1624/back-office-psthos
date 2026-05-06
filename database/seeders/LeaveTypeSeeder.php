<?php

namespace Database\Seeders;

use App\Models\LeaveType;
use Illuminate\Database\Seeder;

class LeaveTypeSeeder extends Seeder
{
    public function run(): void
    {
        $leaveTypes = [
            [
                'code' => 'SICK',
                'name' => 'ลาป่วย',
                'default_days_per_year' => 30,
                'requires_document' => false,
                'description' => 'ลาป่วยทั่วไป',
            ],
            [
                'code' => 'PERSONAL',
                'name' => 'ลากิจ',
                'default_days_per_year' => 10,
                'requires_document' => false,
                'description' => 'ลากิจส่วนตัว',
            ],
            [
                'code' => 'VACATION',
                'name' => 'ลาพักผ่อน',
                'default_days_per_year' => 10,
                'requires_document' => false,
                'description' => 'ลาพักผ่อนประจำปี',
            ],
            [
                'code' => 'MATERNITY',
                'name' => 'ลาคลอด',
                'default_days_per_year' => 90,
                'requires_document' => true,
                'description' => 'ลาคลอดบุตร',
            ],
            [
                'code' => 'TRAINING',
                'name' => 'ลาอบรม / ประชุม',
                'default_days_per_year' => null,
                'requires_document' => true,
                'description' => 'ลาไปอบรม ประชุม หรือราชการ',
            ],
        ];

        foreach ($leaveTypes as $leaveType) {
            LeaveType::firstOrCreate(
                ['code' => $leaveType['code']],
                array_merge($leaveType, [
                    'is_active' => true,
                ])
            );
        }
    }
}
