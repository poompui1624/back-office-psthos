<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SystemSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // GENERAL
            [
                'group' => 'general',
                'key' => 'app_name',
                'label' => 'ชื่อระบบ',
                'value' => 'Hospital Backoffice',
                'type' => 'text',
                'description' => 'ชื่อระบบที่แสดงในหน้าเว็บ',
            ],
            [
                'group' => 'general',
                'key' => 'app_timezone',
                'label' => 'Timezone',
                'value' => 'Asia/Bangkok',
                'type' => 'text',
                'description' => 'เขตเวลาของระบบ',
            ],

            // HOSPITAL
            [
                'group' => 'hospital',
                'key' => 'hospital_name',
                'label' => 'ชื่อโรงพยาบาล',
                'value' => 'โรงพยาบาล',
                'type' => 'text',
                'description' => 'ชื่อหน่วยงานหรือโรงพยาบาล',
            ],
            [
                'group' => 'hospital',
                'key' => 'hospital_address',
                'label' => 'ที่อยู่โรงพยาบาล',
                'value' => '',
                'type' => 'textarea',
                'description' => 'ที่อยู่สำหรับเอกสารและรายงาน',
            ],
            [
                'group' => 'hospital',
                'key' => 'hospital_phone',
                'label' => 'เบอร์โทรศัพท์',
                'value' => '',
                'type' => 'text',
                'description' => 'เบอร์โทรกลางของโรงพยาบาล',
            ],
            [
                'group' => 'hospital',
                'key' => 'hospital_logo',
                'label' => 'โลโก้โรงพยาบาล',
                'value' => '',
                'type' => 'image',
                'description' => 'อัปโหลดไฟล์โลโก้โรงพยาบาล เช่น PNG, JPG',
            ],

            // LEAVE
            [
                'group' => 'leave',
                'key' => 'leave_fiscal_year_start_month',
                'label' => 'เดือนเริ่มปีงบประมาณการลา',
                'value' => '10',
                'type' => 'number',
                'description' => 'เช่น 10 = ตุลาคม',
            ],
            [
                'group' => 'leave',
                'key' => 'leave_require_approval',
                'label' => 'การลาต้องอนุมัติ',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'เปิด/ปิด การอนุมัติคำขอลา',
            ],
            [
                'group' => 'leave',
                'key' => 'leave_allow_attachment',
                'label' => 'อนุญาตแนบไฟล์การลา',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'ใช้สำหรับใบรับรองแพทย์หรือเอกสารประกอบ',
            ],

            // ATTENDANCE
            [
                'group' => 'attendance',
                'key' => 'attendance_default_in_time',
                'label' => 'เวลาเข้างานปกติ',
                'value' => '08:30',
                'type' => 'time',
                'description' => 'ใช้กรณีไม่ได้คำนวณจากตารางเวร',
            ],
            [
                'group' => 'attendance',
                'key' => 'attendance_default_out_time',
                'label' => 'เวลาเลิกงานปกติ',
                'value' => '16:30',
                'type' => 'time',
                'description' => 'ใช้กรณีไม่ได้คำนวณจากตารางเวร',
            ],
            [
                'group' => 'attendance',
                'key' => 'attendance_grace_minutes',
                'label' => 'ผ่อนผันมาสายกี่นาที',
                'value' => '0',
                'type' => 'number',
                'description' => 'เช่น 5 หมายถึงสายเกิน 5 นาทีจึงนับ',
            ],
            [
                'group' => 'attendance',
                'key' => 'attendance_use_duty_schedule',
                'label' => 'คำนวณเวลาจากตารางเวรเป็นค่าเริ่มต้น',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'ใช้เวลาเริ่ม-สิ้นสุดจากตารางเวร',
            ],

            // DUTY
            [
                'group' => 'duty',
                'key' => 'duty_allow_cross_midnight',
                'label' => 'อนุญาตเวรข้ามวัน',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'รองรับเวรบ่าย/เวรดึกที่ข้ามวัน',
            ],
            [
                'group' => 'duty',
                'key' => 'duty_default_status',
                'label' => 'สถานะเริ่มต้นของตารางเวร',
                'value' => 'assigned',
                'type' => 'text',
                'description' => 'เช่น assigned, confirmed',
            ],

            // PAYROLL
            [
                'group' => 'payroll',
                'key' => 'payroll_late_deduction_enabled',
                'label' => 'เปิดใช้การหักเงินกรณีมาสาย',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'ใช้ข้อมูลมาสายจาก Attendance',
            ],
            [
                'group' => 'payroll',
                'key' => 'payroll_early_leave_deduction_enabled',
                'label' => 'เปิดใช้การหักเงินกรณีกลับก่อน',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'ใช้ข้อมูลกลับก่อนจาก Attendance',
            ],
            [
                'group' => 'payroll',
                'key' => 'payroll_absent_deduction_enabled',
                'label' => 'เปิดใช้การหักเงินกรณีขาดงาน',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'ใช้สถานะไม่พบสแกน/ขาดงาน',
            ],
            [
                'group' => 'payroll',
                'key' => 'payroll_default_pay_day',
                'label' => 'วันที่จ่ายเงินเดือน',
                'value' => '25',
                'type' => 'number',
                'description' => 'วันที่จ่ายเงินเดือนประจำเดือน',
            ],

            // MEETING
            [
                'group' => 'meeting',
                'key' => 'meeting_require_approval',
                'label' => 'การจองห้องต้องอนุมัติ',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'เปิด/ปิด ขั้นตอนอนุมัติการจองห้องประชุม',
            ],
            [
                'group' => 'meeting',
                'key' => 'meeting_min_booking_minutes',
                'label' => 'ระยะเวลาจองขั้นต่ำ / นาที',
                'value' => '30',
                'type' => 'number',
                'description' => 'ระยะเวลาขั้นต่ำของการจองห้อง',
            ],
            [
                'group' => 'meeting',
                'key' => 'meeting_allow_cancel_approved',
                'label' => 'อนุญาตยกเลิกรายการที่อนุมัติแล้ว',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'เปิดให้ยกเลิกรายการที่อนุมัติแล้วได้',
            ],

            // REPAIR
            [
                'group' => 'repair',
                'key' => 'repair_auto_assign_enabled',
                'label' => 'มอบหมายงานซ่อมอัตโนมัติ',
                'value' => '0',
                'type' => 'boolean',
                'description' => 'เปิดใช้ภายหลังเมื่อมีระบบช่าง/ทีมซ่อม',
            ],
            [
                'group' => 'repair',
                'key' => 'repair_default_priority',
                'label' => 'ความสำคัญเริ่มต้น',
                'value' => 'normal',
                'type' => 'text',
                'description' => 'เช่น low, normal, high, urgent',
            ],

            // ASSET
            [
                'group' => 'asset',
                'key' => 'asset_auto_code_enabled',
                'label' => 'สร้างเลขครุภัณฑ์อัตโนมัติ',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'ใช้สำหรับทะเบียนพัสดุ',
            ],
            [
                'group' => 'asset',
                'key' => 'asset_depreciation_enabled',
                'label' => 'เปิดใช้ค่าเสื่อมราคา',
                'value' => '0',
                'type' => 'boolean',
                'description' => 'เตรียมไว้สำหรับรายงานค่าเสื่อม',
            ],

            // VEHICLE
            [
                'group' => 'vehicle',
                'key' => 'vehicle_require_approval',
                'label' => 'การขอใช้รถต้องอนุมัติ',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'ใช้กับระบบขอใช้รถโรงพยาบาล',
            ],
            [
                'group' => 'vehicle',
                'key' => 'vehicle_allow_return_date',
                'label' => 'ระบุวันกลับ',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'รองรับการเดินทางไป-กลับต่างวัน',
            ],

            // NOTIFICATION
            [
                'group' => 'notification',
                'key' => 'notification_enabled',
                'label' => 'เปิดใช้การแจ้งเตือน',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'เปิด/ปิดระบบแจ้งเตือนกลาง',
            ],
            [
                'group' => 'notification',
                'key' => 'notification_email_enabled',
                'label' => 'แจ้งเตือนทางอีเมล',
                'value' => '0',
                'type' => 'boolean',
                'description' => 'เปิดใช้เมื่อกำหนดค่า mail แล้ว',
            ],

            // SECURITY
            [
                'group' => 'security',
                'key' => 'security_password_expire_days',
                'label' => 'อายุรหัสผ่าน / วัน',
                'value' => '0',
                'type' => 'number',
                'description' => '0 = ไม่บังคับหมดอายุ',
            ],
            [
                'group' => 'security',
                'key' => 'security_audit_log_enabled',
                'label' => 'เปิดใช้ Audit Log',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'บันทึกประวัติการทำรายการสำคัญ',
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('system_settings')->updateOrInsert(
                ['key' => $setting['key']],
                [
                    'group' => $setting['group'],
                    'label' => $setting['label'],
                    'value' => $setting['value'],
                    'type' => $setting['type'],
                    'description' => $setting['description'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}