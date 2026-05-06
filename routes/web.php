<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SystemSettingController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\AppNotificationController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\AssetCategoryController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AssetMovementController;
use App\Http\Controllers\ComputerController;
use App\Http\Controllers\ComputerAgentController;
use App\Http\Controllers\SoftwareInventoryController;
use App\Http\Controllers\SoftwareProductController;
use App\Http\Controllers\SoftwareLicenseController;
use App\Http\Controllers\RepairRequestController;
use App\Http\Controllers\LeaveTypeController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\AttendanceDeviceController;
use App\Http\Controllers\AttendanceLogController;
use App\Http\Controllers\AttendanceSummaryController;
use App\Http\Controllers\ShiftTypeController;
use App\Http\Controllers\DutyScheduleController;
use App\Http\Controllers\SalaryProfileController;
use App\Http\Controllers\PayrollPeriodController;
use App\Http\Controllers\PayslipController;
use App\Http\Controllers\MeetingRoomController;
use App\Http\Controllers\MeetingBookingController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ItaDocumentController;
use App\Http\Controllers\ItaPublicController;
use App\Http\Controllers\ItaMoitTopicController;
use App\Http\Controllers\ItaMoitSubTopicController;
use App\Http\Controllers\ItaFiscalYearController;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : view('welcome');
})->name('home');

Route::middleware(['auth'])->group(function () {
    Route::view('/dashboard', 'dashboard')
        ->middleware('permission:dashboard.view')
        ->name('dashboard');

    Route::resource('departments', DepartmentController::class)
        ->except(['show']);

    Route::resource('positions', PositionController::class)
        ->except(['show']);

    Route::resource('employees', EmployeeController::class)
        ->except(['show']);

    Route::resource('users', UserController::class)
        ->except(['show']);

    Route::get('/system-settings', [SystemSettingController::class, 'index'])
        ->name('system-settings.index');

    Route::put('/system-settings', [SystemSettingController::class, 'update'])
        ->name('system-settings.update');

    Route::get('/audit-logs', [AuditLogController::class, 'index'])
        ->name('audit-logs.index');
    Route::post('/attachments', [AttachmentController::class, 'store'])
        ->name('attachments.store');

    Route::get('/attachments/{attachment}/download', [AttachmentController::class, 'download'])
        ->name('attachments.download');

    Route::delete('/attachments/{attachment}', [AttachmentController::class, 'destroy'])
        ->name('attachments.destroy');

    Route::get('/notifications', [AppNotificationController::class, 'index'])
        ->name('notifications.index');

    Route::patch('/notifications/{notification}/read', [AppNotificationController::class, 'markAsRead'])
        ->name('notifications.read');

    Route::patch('/notifications/read-all', [AppNotificationController::class, 'markAllAsRead'])
        ->name('notifications.read-all');

    Route::delete('/notifications/{notification}', [AppNotificationController::class, 'destroy'])
        ->name('notifications.destroy');
    Route::get('/approvals', [ApprovalController::class, 'index'])
        ->name('approvals.index');

    Route::patch('/approvals/{approval}/approve', [ApprovalController::class, 'approve'])
        ->name('approvals.approve');

    Route::patch('/approvals/{approval}/reject', [ApprovalController::class, 'reject'])
        ->name('approvals.reject');
    Route::resource('asset-categories', AssetCategoryController::class)
        ->except(['show']);

    Route::resource('assets', AssetController::class)
        ->except(['show']);
    Route::get('/asset-movements', [AssetMovementController::class, 'index'])
        ->name('asset-movements.index');

    Route::get('/asset-movements/create', [AssetMovementController::class, 'create'])
        ->name('asset-movements.create');

    Route::post('/asset-movements', [AssetMovementController::class, 'store'])
        ->name('asset-movements.store');

    Route::resource('computers', ComputerController::class);

    Route::resource('computer-agents', ComputerAgentController::class)
        ->except(['show']);

    Route::post('/computer-agents/{computerAgent}/regenerate-token', [ComputerAgentController::class, 'regenerateToken'])
        ->name('computer-agents.regenerate-token');

    Route::get('/software-inventory', [SoftwareInventoryController::class, 'index'])
        ->name('software-inventory.index');

    Route::get('/software-inventory/computers', [SoftwareInventoryController::class, 'computers'])
        ->name('software-inventory.computers');
    Route::resource('software-products', SoftwareProductController::class)
        ->except(['show']);

    Route::resource('software-licenses', SoftwareLicenseController::class)
        ->except(['show']);

    Route::get('/software-licenses/{softwareLicense}/renew', [SoftwareLicenseController::class, 'renewForm'])
        ->name('software-licenses.renew-form');

    Route::post('/software-licenses/{softwareLicense}/renew', [SoftwareLicenseController::class, 'renew'])
        ->name('software-licenses.renew');

    Route::get('/software-licenses/{softwareLicense}/cancel', [SoftwareLicenseController::class, 'cancelForm'])
        ->name('software-licenses.cancel-form');

    Route::post('/software-licenses/{softwareLicense}/cancel', [SoftwareLicenseController::class, 'cancel'])
        ->name('software-licenses.cancel');

    Route::get('/repair-requests/kanban', [RepairRequestController::class, 'kanban'])
        ->name('repair-requests.kanban');

    Route::patch('/repair-requests/{repairRequest}/status', [RepairRequestController::class, 'updateStatus'])
        ->name('repair-requests.update-status');

    Route::resource('repair-requests', RepairRequestController::class);

    Route::resource('leave-types', LeaveTypeController::class)
        ->except(['show']);

    Route::patch('/leave-requests/{leaveRequest}/approve', [LeaveRequestController::class, 'approve'])
        ->name('leave-requests.approve');

    Route::patch('/leave-requests/{leaveRequest}/reject', [LeaveRequestController::class, 'reject'])
        ->name('leave-requests.reject');

    Route::patch('/leave-requests/{leaveRequest}/cancel', [LeaveRequestController::class, 'cancel'])
        ->name('leave-requests.cancel');

    Route::get('/leave-dashboard', [LeaveRequestController::class, 'dashboard'])
        ->name('leave-requests.dashboard');

    Route::get('/leave-calendar', [LeaveRequestController::class, 'calendar'])
        ->name('leave-requests.calendar');

    Route::resource('leave-requests', LeaveRequestController::class);

    Route::resource('attendance-devices', AttendanceDeviceController::class)
        ->except(['show']);

    Route::get('/attendance-logs/import', [AttendanceLogController::class, 'importForm'])
        ->name('attendance-logs.import-form');

    Route::post('/attendance-logs/import', [AttendanceLogController::class, 'import'])
        ->name('attendance-logs.import');

    Route::get('/attendance-logs', [AttendanceLogController::class, 'index'])
        ->name('attendance-logs.index');

    Route::get('/attendance-dashboard', [AttendanceSummaryController::class, 'dashboard'])
    ->name('attendance-summaries.dashboard');

    Route::get('/attendance-summaries/print', [AttendanceSummaryController::class, 'print'])
    ->name('attendance-summaries.print');

    Route::get('/attendance-summaries/generate', [AttendanceSummaryController::class, 'generateForm'])
        ->name('attendance-summaries.generate-form');

    Route::post('/attendance-summaries/generate', [AttendanceSummaryController::class, 'generate'])
        ->name('attendance-summaries.generate');

    Route::get('/attendance-summaries', [AttendanceSummaryController::class, 'index'])
        ->name('attendance-summaries.index');

    Route::resource('shift-types', ShiftTypeController::class)
        ->except(['show']);

    Route::get('/duty-schedules/bulk-create', [DutyScheduleController::class, 'bulkCreate'])
        ->name('duty-schedules.bulk-create');

    Route::post('/duty-schedules/bulk-create', [DutyScheduleController::class, 'bulkStore'])
        ->name('duty-schedules.bulk-store');

    Route::get('/duty-schedules/calendar', [DutyScheduleController::class, 'calendar'])
        ->name('duty-schedules.calendar');

    Route::resource('duty-schedules', DutyScheduleController::class)
        ->except(['show']);

    Route::resource('salary-profiles', SalaryProfileController::class)
        ->except(['show']);

    Route::post('/payroll-periods/{payrollPeriod}/generate', [PayrollPeriodController::class, 'generate'])
        ->name('payroll-periods.generate');

    Route::post('/payroll-periods/{payrollPeriod}/close', [PayrollPeriodController::class, 'close'])
        ->name('payroll-periods.close');

    Route::resource('payroll-periods', PayrollPeriodController::class)
        ->except(['edit', 'update', 'destroy']);

    Route::get('/payslips/{payslip}', [PayslipController::class, 'show'])
        ->name('payslips.show');

    Route::get('/payslips/{payslip}/print', [PayslipController::class, 'print'])
        ->name('payslips.print');

    Route::resource('meeting-rooms', MeetingRoomController::class)
        ->except(['show']);

    Route::patch('/meeting-bookings/{meetingBooking}/approve', [MeetingBookingController::class, 'approve'])
        ->name('meeting-bookings.approve');

    Route::patch('/meeting-bookings/{meetingBooking}/reject', [MeetingBookingController::class, 'reject'])
        ->name('meeting-bookings.reject');

    Route::patch('/meeting-bookings/{meetingBooking}/cancel', [MeetingBookingController::class, 'cancel'])
        ->name('meeting-bookings.cancel');

    Route::get('/meeting-bookings/{meetingBooking}/print', [MeetingBookingController::class, 'printView'])
        ->name('meeting-bookings.print');

    Route::resource('meeting-bookings', MeetingBookingController::class);

    Route::get('/exports/dashboard-summary', [ExportController::class, 'dashboardSummary'])
        ->name('exports.dashboard-summary');

    Route::get('/exports/{key}', [ExportController::class, 'table'])
        ->name('exports.table');
});

Route::middleware(['auth'])->prefix('ita')->name('ita.')->group(function () {
    /*
    |--------------------------------------------------------------------------
    | ITA Documents
    |--------------------------------------------------------------------------
    */
    Route::get('/documents', [ItaDocumentController::class, 'index'])
        ->name('documents.index')
        ->middleware('permission:ita.view');

    Route::get('/documents/create', [ItaDocumentController::class, 'create'])
        ->name('documents.create')
        ->middleware('permission:ita.create');

    Route::post('/documents', [ItaDocumentController::class, 'store'])
        ->name('documents.store')
        ->middleware('permission:ita.create');

    Route::get('/documents/{document}/edit', [ItaDocumentController::class, 'edit'])
        ->name('documents.edit')
        ->middleware('permission:ita.edit');

    Route::put('/documents/{document}', [ItaDocumentController::class, 'update'])
        ->name('documents.update')
        ->middleware('permission:ita.edit');

    Route::delete('/documents/{document}', [ItaDocumentController::class, 'destroy'])
        ->name('documents.destroy')
        ->middleware('permission:ita.delete');

    Route::get('/sub-topics', [ItaDocumentController::class, 'subTopics'])
        ->name('sub-topics')
        ->middleware('permission:ita.view');

    /*
    |--------------------------------------------------------------------------
    | ITA Fiscal Years
    |--------------------------------------------------------------------------
    */
    Route::resource('fiscal-years', ItaFiscalYearController::class)
        ->except(['show'])
        ->names('fiscal-years')
        ->middleware('permission:ita.topic.manage');

    /*
    |--------------------------------------------------------------------------
    | ITA MOIT Main Topics
    |--------------------------------------------------------------------------
    */
    Route::resource('moit-topics', ItaMoitTopicController::class)
        ->except(['show'])
        ->names('moit-topics')
        ->middleware('permission:ita.topic.manage');

    /*
    |--------------------------------------------------------------------------
    | ITA MOIT Sub Topics
    |--------------------------------------------------------------------------
    */
    Route::resource('moit-sub-topics', ItaMoitSubTopicController::class)
        ->except(['show'])
        ->names('moit-sub-topics')
        ->middleware('permission:ita.topic.manage');
});

    /*
    |--------------------------------------------------------------------------
    | ITA Public Page
    |--------------------------------------------------------------------------
    */
    Route::get('/ita-public/{year?}', [ItaPublicController::class, 'index'])
        ->name('ita.public.index');

require __DIR__.'/settings.php';
