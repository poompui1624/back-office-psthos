<?php

namespace App\Http\Controllers;

use App\Exports\DashboardSummaryExport;
use App\Exports\DatabaseTableExport;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    /**
     * Tables that may be exported, keyed by route slug.
     *
     * An entry without 'columns' exports every column except
     * {@see DatabaseTableExport::DENIED_COLUMNS}.
     *
     * @var array<string, array{table: string, title: string, filename: string, permission: string, columns?: array<int, string>, scope?: string}>
     */
    private array $allowedExports = [
        'departments' => [
            'table' => 'departments',
            'title' => 'Departments',
            'filename' => 'departments',
            'permission' => 'department.view',
        ],
        'positions' => [
            'table' => 'positions',
            'title' => 'Positions',
            'filename' => 'positions',
            'permission' => 'position.view',
        ],
        'employees' => [
            'scope' => 'employee',
            'table' => 'employees',
            'title' => 'Employees',
            'filename' => 'employees',
            'permission' => 'employee.view',
            'columns' => [
                'id', 'employee_code', 'prefix', 'first_name', 'last_name',
                'gender', 'phone', 'email', 'department_id', 'position_id',
                'employment_type', 'start_work_date', 'status', 'created_at', 'updated_at',
            ],
        ],
        'users' => [
            'table' => 'users',
            'title' => 'Users',
            'filename' => 'users',
            'permission' => 'user.view',
            'columns' => [
                'id', 'employee_id', 'name', 'email', 'email_verified_at',
                'is_active', 'last_login_at', 'created_at', 'updated_at',
            ],
        ],

        'leave_requests' => [
            'scope' => 'leave',
            'table' => 'leave_requests',
            'title' => 'Leave Requests',
            'filename' => 'leave_requests',
            'permission' => 'leave.view',
        ],
        'repair_requests' => [
            'scope' => 'repair',
            'table' => 'repair_requests',
            'title' => 'Repair Requests',
            'filename' => 'repair_requests',
            'permission' => 'repair.view',
        ],

        'assets' => [
            'scope' => 'asset',
            'table' => 'assets',
            'title' => 'Assets',
            'filename' => 'assets',
            'permission' => 'asset.view',
        ],
        'asset_categories' => [
            'table' => 'asset_categories',
            'title' => 'Asset Categories',
            'filename' => 'asset_categories',
            'permission' => 'asset.view',
        ],
        'asset_movements' => [
            'scope' => 'asset',
            'table' => 'asset_movements',
            'title' => 'Asset Movements',
            'filename' => 'asset_movements',
            'permission' => 'asset.view',
        ],

        'computers' => [
            'table' => 'computers',
            'title' => 'Computers',
            'filename' => 'computers',
            'permission' => 'computer.view',
        ],
        'software_licenses' => [
            'table' => 'software_licenses',
            'title' => 'Software Licenses',
            'filename' => 'software_licenses',
            'permission' => 'software.view',
        ],
        'software_products' => [
            'table' => 'software_products',
            'title' => 'Software Products',
            'filename' => 'software_products',
            'permission' => 'software.view',
        ],

        'attendance_logs' => [
            'scope' => 'attendance',
            'table' => 'attendance_logs',
            'title' => 'Attendance Logs',
            'filename' => 'attendance_logs',
            'permission' => 'attendance.view',
        ],
        'attendance_daily_summaries' => [
            'scope' => 'attendance',
            'table' => 'attendance_daily_summaries',
            'title' => 'Attendance Daily Summaries',
            'filename' => 'attendance_daily_summaries',
            'permission' => 'attendance.view',
        ],

        'duty_schedules' => [
            'scope' => 'duty',
            'table' => 'duty_schedules',
            'title' => 'Duty Schedules',
            'filename' => 'duty_schedules',
            'permission' => 'duty.view',
        ],
        'shift_types' => [
            'table' => 'shift_types',
            'title' => 'Shift Types',
            'filename' => 'shift_types',
            'permission' => 'duty.view',
        ],

        'payroll_periods' => [
            'table' => 'payroll_periods',
            'title' => 'Payroll Periods',
            'filename' => 'payroll_periods',
            'permission' => 'payroll.view',
        ],
        'salary_profiles' => [
            'scope' => 'payroll',
            'table' => 'salary_profiles',
            'title' => 'Salary Profiles',
            'filename' => 'salary_profiles',
            'permission' => 'payroll.view',
        ],
        'payslips' => [
            'scope' => 'payroll',
            'table' => 'payslips',
            'title' => 'Payslips',
            'filename' => 'payslips',
            'permission' => 'payroll.view',
        ],
        'payslip_items' => [
            'scope' => 'payroll',
            'table' => 'payslip_items',
            'title' => 'Payslip Items',
            'filename' => 'payslip_items',
            'permission' => 'payroll.view',
        ],

        'meeting_bookings' => [
            'table' => 'meeting_bookings',
            'title' => 'Meeting Bookings',
            'filename' => 'meeting_bookings',
            'permission' => 'meeting.view',
        ],
        'meeting_rooms' => [
            'table' => 'meeting_rooms',
            'title' => 'Meeting Rooms',
            'filename' => 'meeting_rooms',
            'permission' => 'meeting.view',
        ],

        'approval_requests' => [
            'table' => 'approval_requests',
            'title' => 'Approval Requests',
            'filename' => 'approval_requests',
            'permission' => 'approval.view',
        ],
    ];

    public function dashboardSummary(): BinaryFileResponse
    {
        abort_unless(auth()->user()?->can('dashboard.view'), 403);

        $filename = 'dashboard_summary_'.now()->format('Ymd_His').'.xlsx';

        return Excel::download(new DashboardSummaryExport, $filename);
    }

    public function table(string $key): BinaryFileResponse
    {
        abort_unless(array_key_exists($key, $this->allowedExports), 404);

        $export = $this->allowedExports[$key];

        abort_unless(auth()->user()?->can($export['permission']), 403);

        // A department-scoped table is dumped in full by DatabaseTableExport, so
        // exporting one is only offered to users who may see the whole hospital.
        if (isset($export['scope'])) {
            abort_unless(auth()->user()?->can($export['scope'].'.view.all'), 403);
        }

        abort_unless(Schema::hasTable($export['table']), 404);

        $filename = $export['filename'].'_'.now()->format('Ymd_His').'.xlsx';

        return Excel::download(
            new DatabaseTableExport($export['table'], $export['title'], $export['columns'] ?? null),
            $filename
        );
    }
}
