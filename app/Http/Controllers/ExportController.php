<?php

namespace App\Http\Controllers;

use App\Exports\DashboardSummaryExport;
use App\Exports\DatabaseTableExport;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
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
            'table' => 'employees',
            'title' => 'Employees',
            'filename' => 'employees',
            'permission' => 'employee.view',
        ],
        'users' => [
            'table' => 'users',
            'title' => 'Users',
            'filename' => 'users',
            'permission' => 'user.view',
        ],

        'leave_requests' => [
            'table' => 'leave_requests',
            'title' => 'Leave Requests',
            'filename' => 'leave_requests',
            'permission' => 'leave.view',
        ],
        'repair_requests' => [
            'table' => 'repair_requests',
            'title' => 'Repair Requests',
            'filename' => 'repair_requests',
            'permission' => 'repair.view',
        ],

        'assets' => [
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
            'table' => 'attendance_logs',
            'title' => 'Attendance Logs',
            'filename' => 'attendance_logs',
            'permission' => 'attendance.view',
        ],
        'attendance_daily_summaries' => [
            'table' => 'attendance_daily_summaries',
            'title' => 'Attendance Daily Summaries',
            'filename' => 'attendance_daily_summaries',
            'permission' => 'attendance.view',
        ],

        'duty_schedules' => [
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
            'table' => 'salary_profiles',
            'title' => 'Salary Profiles',
            'filename' => 'salary_profiles',
            'permission' => 'payroll.view',
        ],
        'payslips' => [
            'table' => 'payslips',
            'title' => 'Payslips',
            'filename' => 'payslips',
            'permission' => 'payroll.view',
        ],
        'payslip_items' => [
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

        $filename = 'dashboard_summary_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new DashboardSummaryExport(), $filename);
    }

    public function table(string $key): BinaryFileResponse
    {
        abort_unless(array_key_exists($key, $this->allowedExports), 404);

        $export = $this->allowedExports[$key];

        abort_unless(auth()->user()?->can($export['permission']), 403);

        abort_unless(Schema::hasTable($export['table']), 404);

        $filename = $export['filename'] . '_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(
            new DatabaseTableExport($export['table'], $export['title']),
            $filename
        );
    }
}
