<?php

namespace App\Http\Controllers;

use App\Models\ApprovalRequest;
use App\Models\Asset;
use App\Models\AttendanceDailySummary;
use App\Models\AuditLog;
use App\Models\DutySchedule;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\MeetingBooking;
use App\Models\RepairRequest;
use App\Models\SoftwareLicense;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class DashboardController extends Controller
{
    /**
     * Palette shared by every chart on the page, so a category keeps its colour
     * whichever widget it appears in.
     *
     * @var array<int, string>
     */
    private const PALETTE = [
        '#02abff', '#10b981', '#f59e0b', '#8b5cf6', '#f43f5e',
        '#06b6d4', '#84cc16', '#ec4899', '#64748b', '#0ea5e9',
    ];

    public function __invoke(): View
    {
        $user = auth()->user();

        return view('dashboard', [
            'user' => $user,
            'hospitalName' => function_exists('hospital_name') ? hospital_name() : config('app.name', 'Hospital Backoffice'),
            'greeting' => $this->greeting(),
            'scopeLabel' => $this->scopeLabel($user),
            'heroStats' => $this->heroStats($user),
            'statCards' => $this->statCards($user),
            'alertCards' => $this->alertCards($user),
            'employeesByDepartment' => $this->employeesByDepartment($user),
            'leaveByStatus' => $this->leaveByStatus($user),
            'leaveTrend' => $this->leaveTrend($user),
            'assetsByDepartment' => $this->assetsByDepartment($user),
            'upcomingExpirations' => $this->upcomingExpirations($user),
            'recentActivities' => $this->recentActivities(),
            'quickActions' => $this->quickActions(),
            'canExportDashboard' => Route::has('exports.dashboard-summary') && $user?->can('dashboard.view'),
        ]);
    }

    private function greeting(): string
    {
        $hour = (int) now()->format('G');

        return match (true) {
            $hour < 12 => 'สวัสดีตอนเช้า',
            $hour < 17 => 'สวัสดีตอนบ่าย',
            default => 'สวัสดีตอนเย็น',
        };
    }

    /**
     * What the numbers on this page cover, so a supervisor is not left assuming
     * they are looking at the whole hospital.
     */
    private function scopeLabel(?User $user): string
    {
        if ($user?->can('employee.view.all') || $user?->can('leave.view.all')) {
            return 'ทั้งโรงพยาบาล';
        }

        $department = $user?->employee?->department;

        return $department ? $department->name : 'เฉพาะข้อมูลของคุณ';
    }

    /**
     * The four figures in the hero banner.
     *
     * @return array<int, array{label: string, value: string}>
     */
    private function heroStats(?User $user): array
    {
        $today = now()->toDateString();

        return [
            [
                'label' => 'บุคลากร',
                'value' => number_format(Employee::query()->visibleTo($user)->where('status', 'active')->count()),
            ],
            [
                'label' => 'ลาวันนี้',
                'value' => number_format(
                    LeaveRequest::query()
                        ->visibleTo($user)
                        ->where('status', 'approved')
                        ->whereDate('start_date', '<=', $today)
                        ->whereDate('end_date', '>=', $today)
                        ->count()
                ),
            ],
            [
                'label' => 'เวรวันนี้',
                'value' => number_format(
                    DutySchedule::query()
                        ->visibleTo($user)
                        ->whereDate('work_date', $today)
                        ->where('status', '!=', 'cancelled')
                        ->count()
                ),
            ],
            [
                'label' => 'งานซ่อมค้าง',
                'value' => number_format(
                    RepairRequest::query()
                        ->visibleTo($user)
                        ->whereIn('status', ['pending', 'new', 'in_progress'])
                        ->count()
                ),
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function statCards(?User $user): array
    {
        $startOfMonth = now()->startOfMonth()->toDateString();
        $endOfMonth = now()->endOfMonth()->toDateString();

        $pendingLeave = LeaveRequest::query()->visibleTo($user)->where('status', 'pending')->count();
        $pendingApproval = ApprovalRequest::where('status', 'pending')->count();

        $lateThisMonth = AttendanceDailySummary::query()
            ->whereBetween('work_date', [$startOfMonth, $endOfMonth])
            ->where('status', 'late')
            ->count();

        $dutyThisMonth = DutySchedule::query()
            ->visibleTo($user)
            ->whereBetween('work_date', [$startOfMonth, $endOfMonth])
            ->where('status', '!=', 'cancelled')
            ->count();

        $openRepairs = RepairRequest::query()
            ->visibleTo($user)
            ->whereIn('status', ['pending', 'new', 'in_progress'])
            ->count();

        $assetValue = (float) Asset::query()->visibleTo($user)->sum('purchase_price');

        return [
            [
                'label' => 'บุคลากรทั้งหมด',
                'value' => number_format(Employee::query()->visibleTo($user)->count()),
                'icon' => 'users',
                'tone' => 'brand',
                'helper' => 'ในทะเบียนที่คุณดูได้',
                'href' => $this->routeUrl('employees.index'),
                'permission' => 'employee.view',
            ],
            [
                'label' => 'ใบลารออนุมัติ',
                'value' => number_format($pendingLeave),
                'icon' => 'calendar',
                'tone' => $pendingLeave > 0 ? 'amber' : 'emerald',
                'delta' => $pendingLeave > 0
                    ? ['text' => 'รอดำเนินการ', 'direction' => 'down']
                    : ['text' => 'ไม่มีค้าง', 'direction' => 'up'],
                'href' => $this->routeUrl('leave-requests.index', ['status' => 'pending']),
                'permission' => 'leave.view',
            ],
            [
                'label' => 'รายการรออนุมัติ',
                'value' => number_format($pendingApproval),
                'icon' => 'approvals',
                'tone' => $pendingApproval > 0 ? 'amber' : 'emerald',
                'helper' => 'คำขอทุกโมดูล',
                'href' => $this->routeUrl('approvals.index'),
                'permission' => 'approval.view',
            ],
            [
                'label' => 'เวรเดือนนี้',
                'value' => number_format($dutyThisMonth),
                'icon' => 'clock',
                'tone' => 'violet',
                'helper' => 'ไม่รวมเวรที่ยกเลิก',
                'href' => $this->routeUrl('duty-schedules.index'),
                'permission' => 'duty.view',
            ],
            [
                'label' => 'งานซ่อมค้าง',
                'value' => number_format($openRepairs),
                'icon' => 'wrench',
                'tone' => $openRepairs > 0 ? 'rose' : 'emerald',
                'helper' => 'ยังไม่ปิดงาน',
                'href' => $this->routeUrl('repair-requests.kanban'),
                'permission' => 'repair.view',
            ],
            [
                'label' => 'มาสายเดือนนี้',
                'value' => number_format($lateThisMonth),
                'icon' => 'device',
                'tone' => 'slate',
                'helper' => 'จากสรุปเวลาทำงาน',
                'href' => $this->routeUrl('attendance-summaries.index', ['status' => 'late']),
                'permission' => 'attendance.view',
            ],
            [
                'label' => 'มูลค่าพัสดุ',
                'value' => '฿'.number_format($assetValue / 1000000, 2).'M',
                'icon' => 'money',
                'tone' => 'brand',
                'helper' => 'ราคาซื้อรวม',
                'href' => $this->routeUrl('assets.index'),
                'permission' => 'asset.view',
                'featured' => true,
            ],
        ];
    }

    /**
     * The alert strip: things with a deadline attached.
     *
     * @return array<int, array<string, mixed>>
     */
    private function alertCards(?User $user): array
    {
        $horizon = now()->addDays(60)->toDateString();
        $today = now()->toDateString();

        $expiringLicenses = SoftwareLicense::query()
            ->whereNotNull('expire_date')
            ->whereDate('expire_date', '>=', $today)
            ->whereDate('expire_date', '<=', $horizon)
            ->count();

        $overdueLeave = LeaveRequest::query()
            ->visibleTo($user)
            ->where('status', 'pending')
            ->whereDate('start_date', '<=', $today)
            ->count();

        $openMaintenance = RepairRequest::query()
            ->visibleTo($user)
            ->whereIn('status', ['pending', 'new', 'in_progress'])
            ->whereDate('created_at', '<=', now()->subDays(3)->toDateString())
            ->count();

        $pendingMeetings = MeetingBooking::query()
            ->where('status', 'pending')
            ->whereDate('start_at', '>=', $today)
            ->count();

        return [
            [
                'label' => 'License ใกล้หมดอายุ',
                'count' => $expiringLicenses,
                'unit' => 'รายการ',
                'note' => 'ภายใน 60 วัน',
                'icon' => 'key',
                'accent' => '#ec4899',
                'href' => $this->routeUrl('software-licenses.index'),
                'permission' => 'software.view',
            ],
            [
                'label' => 'ใบลาเลยวันเริ่มแล้ว',
                'count' => $overdueLeave,
                'unit' => 'รายการ',
                'note' => 'ยังไม่อนุมัติ',
                'icon' => 'calendar',
                'accent' => '#f59e0b',
                'href' => $this->routeUrl('leave-requests.index', ['status' => 'pending']),
                'permission' => 'leave.view',
            ],
            [
                'label' => 'งานซ่อมค้างเกิน 3 วัน',
                'count' => $openMaintenance,
                'unit' => 'งาน',
                'note' => 'ยังไม่ปิดงาน',
                'icon' => 'wrench',
                'accent' => '#f43f5e',
                'href' => $this->routeUrl('repair-requests.kanban'),
                'permission' => 'repair.view',
            ],
            [
                'label' => 'จองห้องรออนุมัติ',
                'count' => $pendingMeetings,
                'unit' => 'รายการ',
                'note' => 'นับเฉพาะที่ยังไม่ถึงวัน',
                'icon' => 'building',
                'accent' => '#8b5cf6',
                'href' => $this->routeUrl('meeting-bookings.index'),
                'permission' => 'meeting.view',
            ],
        ];
    }

    /**
     * @return array<int, array{label: string, value: int, color: string}>
     */
    private function employeesByDepartment(?User $user): array
    {
        return Employee::query()
            ->visibleTo($user)
            ->where('status', 'active')
            ->leftJoin('departments', 'departments.id', '=', 'employees.department_id')
            ->select([
                DB::raw("coalesce(departments.name, 'ไม่ระบุหน่วยงาน') as label"),
                DB::raw('count(*) as value'),
            ])
            ->groupBy('departments.id', 'departments.name')
            ->orderByDesc('value')
            ->limit(8)
            ->get()
            ->values()
            ->map(fn ($row, $i) => [
                'label' => $row->label,
                'value' => (int) $row->value,
                'color' => self::PALETTE[$i % count(self::PALETTE)],
            ])
            ->all();
    }

    /**
     * @return array<int, array{label: string, value: int, color: string}>
     */
    private function leaveByStatus(?User $user): array
    {
        $labels = [
            'pending' => ['รออนุมัติ', '#f59e0b'],
            'approved' => ['อนุมัติแล้ว', '#10b981'],
            'rejected' => ['ไม่อนุมัติ', '#f43f5e'],
            'cancelled' => ['ยกเลิก', '#94a3b8'],
        ];

        $counts = LeaveRequest::query()
            ->visibleTo($user)
            ->whereYear('start_date', now()->year)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $rows = [];

        foreach ($labels as $status => [$label, $color]) {
            $rows[] = [
                'label' => $label,
                'value' => (int) ($counts[$status] ?? 0),
                'color' => $color,
            ];
        }

        return $rows;
    }

    /**
     * Twelve months of leave days and duty shifts, oldest first.
     *
     * @return array{labels: array<int, string>, series: array<int, array<string, mixed>>}
     */
    private function leaveTrend(?User $user): array
    {
        $months = collect(range(11, 0))
            ->map(fn (int $back) => now()->copy()->startOfMonth()->subMonths($back));

        $start = $months->first()->toDateString();
        $end = now()->endOfMonth()->toDateString();

        $leaveDays = LeaveRequest::query()
            ->visibleTo($user)
            ->where('status', 'approved')
            ->whereDate('start_date', '>=', $start)
            ->whereDate('start_date', '<=', $end)
            ->get(['start_date', 'total_days'])
            ->groupBy(fn ($row) => Carbon::parse($row->start_date)->format('Y-m'))
            ->map(fn (Collection $rows) => (float) $rows->sum('total_days'));

        $dutyShifts = DutySchedule::query()
            ->visibleTo($user)
            ->where('status', '!=', 'cancelled')
            ->whereDate('work_date', '>=', $start)
            ->whereDate('work_date', '<=', $end)
            ->get(['work_date'])
            ->groupBy(fn ($row) => Carbon::parse($row->work_date)->format('Y-m'))
            ->map(fn (Collection $rows) => $rows->count());

        return [
            'labels' => $months->map(fn (Carbon $m) => $m->format('m/').substr((string) thai_year($m->year), -2))->all(),
            'series' => [
                [
                    'label' => 'วันลาที่อนุมัติ',
                    'color' => '#02abff',
                    'points' => $months->map(fn (Carbon $m) => $leaveDays[$m->format('Y-m')] ?? 0)->all(),
                ],
                [
                    'label' => 'จำนวนเวร',
                    'color' => '#8b5cf6',
                    'points' => $months->map(fn (Carbon $m) => $dutyShifts[$m->format('Y-m')] ?? 0)->all(),
                ],
            ],
        ];
    }

    /**
     * @return array<int, array{label: string, value: float, color: string}>
     */
    private function assetsByDepartment(?User $user): array
    {
        return Asset::query()
            ->visibleTo($user)
            ->leftJoin('departments', 'departments.id', '=', 'assets.department_id')
            ->select([
                DB::raw("coalesce(departments.name, 'ไม่ระบุหน่วยงาน') as label"),
                DB::raw('sum(assets.purchase_price) as value'),
            ])
            ->groupBy('departments.id', 'departments.name')
            ->orderByDesc('value')
            ->limit(6)
            ->get()
            ->values()
            ->map(fn ($row, $i) => [
                'label' => $row->label,
                'value' => (float) $row->value,
                'color' => self::PALETTE[$i % count(self::PALETTE)],
            ])
            ->all();
    }

    /**
     * Licences running out, soonest first.
     *
     * @return Collection<int, array<string, mixed>>
     */
    private function upcomingExpirations(?User $user): Collection
    {
        if (! $user?->can('software.view')) {
            return collect();
        }

        return SoftwareLicense::query()
            ->with('product')
            ->whereNotNull('expire_date')
            ->whereDate('expire_date', '<=', now()->addDays(90)->toDateString())
            ->orderBy('expire_date')
            ->limit(6)
            ->get()
            ->map(function (SoftwareLicense $license) {
                $expiry = Carbon::parse($license->expire_date);
                $days = (int) now()->startOfDay()->diffInDays($expiry->startOfDay(), false);

                return [
                    'name' => $license->product?->name ?? $license->license_name ?? 'License',
                    'expiry' => $expiry,
                    'days' => $days,
                    'tone' => match (true) {
                        $days < 0 => 'rose',
                        $days <= 14 => 'amber',
                        default => 'slate',
                    },
                    'label' => match (true) {
                        $days < 0 => 'เกิน '.abs($days).' วัน',
                        $days === 0 => 'หมดอายุวันนี้',
                        default => 'เหลือ '.$days.' วัน',
                    },
                ];
            });
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function recentActivities(): Collection
    {
        if (! auth()->user()?->can('audit.view')) {
            return collect();
        }

        return AuditLog::query()
            ->with('user')
            ->latest()
            ->limit(8)
            ->get()
            ->map(fn (AuditLog $log) => [
                'action' => match ($log->action) {
                    'created' => 'สร้าง',
                    'updated' => 'แก้ไข',
                    'deleted' => 'ลบ',
                    default => $log->action,
                },
                'module' => $log->module,
                'user' => $log->user?->name ?? 'ระบบ',
                'at' => $log->created_at,
                'tone' => match ($log->action) {
                    'created' => '#10b981',
                    'deleted' => '#f43f5e',
                    default => '#02abff',
                },
            ]);
    }

    /**
     * @return array<int, array{label: string, href: string|null, permission: string, icon: string}>
     */
    private function quickActions(): array
    {
        return [
            ['label' => 'ยื่นคำขอลา', 'href' => $this->routeUrl('leave-requests.create'), 'permission' => 'leave.create', 'icon' => 'calendar'],
            ['label' => 'แจ้งซ่อมใหม่', 'href' => $this->routeUrl('repair-requests.create'), 'permission' => 'repair.create', 'icon' => 'wrench'],
            ['label' => 'จองห้องประชุม', 'href' => $this->routeUrl('meeting-bookings.create'), 'permission' => 'meeting.create', 'icon' => 'building'],
            ['label' => 'สร้างตารางเวร', 'href' => $this->routeUrl('duty-schedules.bulk-create'), 'permission' => 'duty.create', 'icon' => 'clock'],
            ['label' => 'เพิ่มบุคลากร', 'href' => $this->routeUrl('employees.create'), 'permission' => 'employee.create', 'icon' => 'users'],
            ['label' => 'นำเข้าเวลา', 'href' => $this->routeUrl('attendance-logs.import-form'), 'permission' => 'attendance.import', 'icon' => 'device'],
        ];
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    private function routeUrl(string $name, array $parameters = []): ?string
    {
        return Route::has($name) ? route($name, $parameters) : null;
    }
}
