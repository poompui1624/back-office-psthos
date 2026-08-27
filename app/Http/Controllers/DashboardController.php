<?php

namespace App\Http\Controllers;

use App\Models\ApprovalRequest;
use App\Models\Asset;
use App\Models\AttendanceDailySummary;
use App\Models\Computer;
use App\Models\Department;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\Position;
use App\Models\RepairRequest;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = auth()->user();

        return view('dashboard', [
            'user' => $user,
            'hospitalName' => function_exists('hospital_name') ? hospital_name() : config('app.name', 'Hospital Backoffice'),
            'overviewCards' => $this->overviewCards(),
            'priorityItems' => $this->priorityItems(),
            'quickActions' => $this->quickActions(),
            'moduleLinks' => $this->moduleLinks(),
            'supportingStats' => $this->supportingStats(),
            'canExportDashboard' => Route::has('exports.dashboard-summary') && $user?->can('dashboard.view'),
            'notificationsUrl' => Route::has('notifications.index') ? route('notifications.index') : null,
        ]);
    }

    /**
     * @return array<int, array{label: string, value: int, href: string|null, permission: string, helper: string}>
     */
    private function overviewCards(): array
    {
        return [
            [
                'label' => 'บุคลากร',
                'value' => Employee::count(),
                'href' => $this->routeUrl('employees.index'),
                'permission' => 'employee.view',
                'helper' => 'ทะเบียนบุคลากรทั้งหมด',
            ],
            [
                'label' => 'ผู้ใช้งาน',
                'value' => User::count(),
                'href' => $this->routeUrl('users.index'),
                'permission' => 'user.view',
                'helper' => 'บัญชีและสิทธิ์ในระบบ',
            ],
            [
                'label' => 'พัสดุ',
                'value' => Asset::count(),
                'href' => $this->routeUrl('assets.index'),
                'permission' => 'asset.view',
                'helper' => 'ทะเบียนพัสดุที่บันทึกไว้',
            ],
            [
                'label' => 'คอมพิวเตอร์',
                'value' => Computer::count(),
                'href' => $this->routeUrl('computers.index'),
                'permission' => 'computer.view',
                'helper' => 'เครื่องที่อยู่ในทะเบียน',
            ],
        ];
    }

    /**
     * @return array<int, array{label: string, value: int, href: string|null, permission: string, severity: string}>
     */
    private function priorityItems(): array
    {
        return [
            [
                'label' => 'รายการรออนุมัติ',
                'value' => ApprovalRequest::where('status', 'pending')->count(),
                'href' => $this->routeUrl('approvals.index'),
                'permission' => 'approval.view',
                'severity' => 'amber',
            ],
            [
                'label' => 'คำขอลารออนุมัติ',
                'value' => LeaveRequest::where('status', 'pending')->count(),
                'href' => $this->routeUrl('leave-requests.index', ['status' => 'pending']),
                'permission' => 'leave.view',
                'severity' => 'blue',
            ],
            [
                'label' => 'งานซ่อมที่ยังเปิดอยู่',
                'value' => RepairRequest::whereIn('status', ['new', 'in_progress'])->count(),
                'href' => $this->routeUrl('repair-requests.index'),
                'permission' => 'repair.view',
                'severity' => 'rose',
            ],
            [
                'label' => 'บุคลากรลาวันนี้',
                'value' => LeaveRequest::where('status', 'approved')
                    ->whereDate('start_date', '<=', now()->toDateString())
                    ->whereDate('end_date', '>=', now()->toDateString())
                    ->count(),
                'href' => $this->routeUrl('leave-requests.calendar'),
                'permission' => 'leave.view',
                'severity' => 'cyan',
            ],
        ];
    }

    /**
     * @return array<int, array{label: string, href: string|null, permission: string}>
     */
    private function quickActions(): array
    {
        return [
            ['label' => 'เพิ่มบุคลากร', 'href' => $this->routeUrl('employees.create'), 'permission' => 'employee.create'],
            ['label' => 'แจ้งซ่อมใหม่', 'href' => $this->routeUrl('repair-requests.create'), 'permission' => 'repair.create'],
            ['label' => 'ยื่นคำขอลา', 'href' => $this->routeUrl('leave-requests.create'), 'permission' => 'leave.create'],
            ['label' => 'จองห้องประชุม', 'href' => $this->routeUrl('meeting-bookings.create'), 'permission' => 'meeting.create'],
            ['label' => 'สร้างตารางเวร', 'href' => $this->routeUrl('duty-schedules.bulk-create'), 'permission' => 'duty.create'],
            ['label' => 'นำเข้าเวลา', 'href' => $this->routeUrl('attendance-logs.import-form'), 'permission' => 'attendance.import'],
        ];
    }

    /**
     * @return array<int, array{label: string, description: string, href: string|null, permission: string}>
     */
    private function moduleLinks(): array
    {
        return [
            ['label' => 'บุคลากร', 'description' => 'แผนก ตำแหน่ง บุคลากร และผู้ใช้งาน', 'href' => $this->routeUrl('employees.index'), 'permission' => 'employee.view'],
            ['label' => 'การลา', 'description' => 'คำขอลา ปฏิทินลา และการอนุมัติ', 'href' => $this->routeUrl('leave-requests.dashboard'), 'permission' => 'leave.view'],
            ['label' => 'แจ้งซ่อม', 'description' => 'รับงาน ติดตามสถานะ และดูบอร์ดงานซ่อม', 'href' => $this->routeUrl('repair-requests.kanban'), 'permission' => 'repair.view'],
            ['label' => 'เวลาเข้างาน', 'description' => 'นำเข้าเวลา สรุปเวลา และรายงานประจำเดือน', 'href' => $this->routeUrl('attendance-summaries.dashboard'), 'permission' => 'attendance.view'],
            ['label' => 'พัสดุ', 'description' => 'ทะเบียนพัสดุ หมวดหมู่ และการโอนย้าย', 'href' => $this->routeUrl('assets.index'), 'permission' => 'asset.view'],
            ['label' => 'คอมพิวเตอร์และซอฟต์แวร์', 'description' => 'ทะเบียนเครื่อง Agent และ License', 'href' => $this->routeUrl('software-inventory.index'), 'permission' => 'software.view'],
            ['label' => 'เงินเดือน', 'description' => 'รอบเงินเดือน โปรไฟล์เงินเดือน และสลิป', 'href' => $this->routeUrl('payroll-periods.index'), 'permission' => 'payroll.view'],
            ['label' => 'ห้องประชุม', 'description' => 'จองห้อง อนุมัติ และพิมพ์ใบจอง', 'href' => $this->routeUrl('meeting-bookings.index'), 'permission' => 'meeting.view'],
            ['label' => 'เอกสาร ITA', 'description' => 'จัดการเอกสาร MOIT และหน้าสาธารณะ', 'href' => $this->routeUrl('ita.documents.index'), 'permission' => 'ita.view'],
        ];
    }

    /**
     * @return array<string, array{label: string, value: int, href: string|null, permission: string}>
     */
    private function supportingStats(): array
    {
        return [
            'lateThisMonth' => [
                'label' => 'มาสายเดือนนี้',
                'value' => AttendanceDailySummary::where('status', 'late')
                    ->whereBetween('work_date', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])
                    ->count(),
                'href' => $this->routeUrl('attendance-summaries.index', ['status' => 'late']),
                'permission' => 'attendance.view',
            ],
            'departments' => [
                'label' => 'หน่วยงาน',
                'value' => Department::count(),
                'href' => $this->routeUrl('departments.index'),
                'permission' => 'department.view',
            ],
            'positions' => [
                'label' => 'ตำแหน่ง',
                'value' => Position::count(),
                'href' => $this->routeUrl('positions.index'),
                'permission' => 'position.view',
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    private function routeUrl(string $name, array $parameters = []): ?string
    {
        if (! Route::has($name)) {
            return null;
        }

        return route($name, $parameters);
    }
}
