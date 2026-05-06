<?php

namespace Database\Seeders;

use App\Models\ShiftType;
use Illuminate\Database\Seeder;

class ShiftTypeSeeder extends Seeder
{
    public function run(): void
    {
        $shiftTypes = [
            [
                'code' => 'MORNING',
                'name' => 'เวรเช้า',
                'start_time' => '08:00',
                'end_time' => '16:00',
                'crosses_midnight' => false,
                'color' => 'blue',
                'description' => 'เวรเช้าทั่วไป',
            ],
            [
                'code' => 'AFTERNOON',
                'name' => 'เวรบ่าย',
                'start_time' => '16:00',
                'end_time' => '00:00',
                'crosses_midnight' => true,
                'color' => 'yellow',
                'description' => 'เวรบ่าย',
            ],
            [
                'code' => 'NIGHT',
                'name' => 'เวรดึก',
                'start_time' => '00:00',
                'end_time' => '08:00',
                'crosses_midnight' => false,
                'color' => 'purple',
                'description' => 'เวรดึก',
            ],
            [
                'code' => 'OFFICE',
                'name' => 'เวลาราชการ',
                'start_time' => '08:30',
                'end_time' => '16:30',
                'crosses_midnight' => false,
                'color' => 'green',
                'description' => 'เวลาทำงานปกติ',
            ],
            [
                'code' => 'OFF',
                'name' => 'วันหยุด',
                'start_time' => '00:00',
                'end_time' => '00:00',
                'crosses_midnight' => false,
                'color' => 'gray',
                'description' => 'วันหยุดหรือไม่เข้าเวร',
            ],
        ];

        foreach ($shiftTypes as $shiftType) {
            ShiftType::firstOrCreate(
                ['code' => $shiftType['code']],
                array_merge($shiftType, [
                    'is_active' => true,
                ])
            );
        }
    }
}
