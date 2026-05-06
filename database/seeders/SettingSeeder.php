<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'key' => 'hospital.name',
                'value' => 'โรงพยาบาล',
                'group' => 'hospital',
                'type' => 'text',
                'label' => 'ชื่อโรงพยาบาล',
                'description' => 'ชื่อโรงพยาบาลที่แสดงในระบบ',
                'is_public' => true,
            ],
            [
                'key' => 'hospital.address',
                'value' => '',
                'group' => 'hospital',
                'type' => 'textarea',
                'label' => 'ที่อยู่โรงพยาบาล',
                'description' => 'ใช้สำหรับแสดงในเอกสาร รายงาน และใบรับรอง',
                'is_public' => true,
            ],
            [
                'key' => 'hospital.phone',
                'value' => '',
                'group' => 'hospital',
                'type' => 'text',
                'label' => 'เบอร์โทรศัพท์',
                'description' => 'เบอร์โทรหลักของโรงพยาบาล',
                'is_public' => true,
            ],
            [
                'key' => 'hospital.email',
                'value' => '',
                'group' => 'hospital',
                'type' => 'text',
                'label' => 'อีเมลโรงพยาบาล',
                'description' => 'อีเมลสำหรับติดต่อ',
                'is_public' => true,
            ],
            [
                'key' => 'system.timezone',
                'value' => 'Asia/Bangkok',
                'group' => 'system',
                'type' => 'text',
                'label' => 'Timezone',
                'description' => 'เขตเวลาหลักของระบบ',
                'is_public' => false,
            ],
            [
                'key' => 'software.expire_notify_days',
                'value' => '30',
                'group' => 'software',
                'type' => 'number',
                'label' => 'แจ้งเตือน Software ก่อนหมดอายุ',
                'description' => 'จำนวนวันก่อนหมดอายุที่ระบบจะแจ้งเตือน',
                'is_public' => false,
            ],
            [
                'key' => 'attendance.late_after_minutes',
                'value' => '15',
                'group' => 'attendance',
                'type' => 'number',
                'label' => 'สายหลังจากกี่นาที',
                'description' => 'ใช้คำนวณเวลาทำงานและการมาสาย',
                'is_public' => false,
            ],
            [
                'key' => 'payroll.cutoff_day',
                'value' => '25',
                'group' => 'payroll',
                'type' => 'number',
                'label' => 'วันตัดรอบเงินเดือน',
                'description' => 'วันที่ใช้ตัดรอบคำนวณเงินเดือน',
                'is_public' => false,
            ],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
