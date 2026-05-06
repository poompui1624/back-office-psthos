<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DashboardSummaryExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles, WithTitle
{
    public function array(): array
    {
        return [
            ['Core System', 'หน่วยงาน', $this->countTable('departments')],
            ['Core System', 'ตำแหน่ง', $this->countTable('positions')],
            ['Core System', 'บุคลากร', $this->countTable('employees')],
            ['Core System', 'ผู้ใช้งานระบบ', $this->countTable('users')],

            ['ระบบการลา', 'คำขอลาทั้งหมด', $this->countTable('leave_requests')],
            ['งานแจ้งซ่อม', 'ใบแจ้งซ่อมทั้งหมด', $this->countTable('repair_requests')],
            ['ทะเบียนพัสดุ', 'พัสดุทั้งหมด', $this->countTable('assets')],
            ['ทะเบียนพัสดุ', 'หมวดหมู่พัสดุ', $this->countTable('asset_categories')],

            ['เวลาทำงาน', 'สรุปเวลาทำงาน', $this->countTable('attendance_daily_summaries')],
            ['ตารางเวร', 'ตารางเวรทั้งหมด', $this->countTable('duty_schedules')],
            ['ตารางเวร', 'ประเภทเวร', $this->countTable('shift_types')],

            ['เงินเดือน', 'รอบเงินเดือน', $this->countTable('payroll_periods')],
            ['เงินเดือน', 'สลิปเงินเดือน', $this->countTable('payslips')],
            ['เงินเดือน', 'ตั้งค่าเงินเดือน', $this->countTable('salary_profiles')],

            ['จองห้องประชุม', 'รายการจอง', $this->countTable('meeting_bookings')],
            ['จองห้องประชุม', 'ห้องประชุม', $this->countTable('meeting_rooms')],

            ['ระบบอนุมัติ', 'รายการอนุมัติ', $this->countTable('approval_requests')],
            ['แจ้งเตือน', 'รายการแจ้งเตือน', $this->countTable('app_notifications')],
        ];
    }

    public function headings(): array
    {
        return [
            'หมวดระบบ',
            'รายการ',
            'จำนวน',
        ];
    }

    public function title(): string
    {
        return 'Dashboard Summary';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true],
            ],
        ];
    }

    private function countTable(string $table): int
    {
        if (! Schema::hasTable($table)) {
            return 0;
        }

        return DB::table($table)->count();
    }
}
