<?php

use App\Http\Controllers\AppNotificationController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\AssetCategoryController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AssetMovementController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\AttendanceDeviceController;
use App\Http\Controllers\AttendanceLogController;
use App\Http\Controllers\AttendanceSummaryController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\ComputerAgentController;
use App\Http\Controllers\ComputerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DutyScheduleController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeePersonnelProfileController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ItaDocumentController;
use App\Http\Controllers\ItaFiscalYearController;
use App\Http\Controllers\ItaMoitSubTopicController;
use App\Http\Controllers\ItaMoitTopicController;
use App\Http\Controllers\ItaPublicController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\LeaveTypeController;
use App\Http\Controllers\MeetingBookingController;
use App\Http\Controllers\MeetingRoomController;
use App\Http\Controllers\PayrollPeriodController;
use App\Http\Controllers\PayslipController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\RepairRequestController;
use App\Http\Controllers\SalaryProfileController;
use App\Http\Controllers\SelfServiceController;
use App\Http\Controllers\ShiftTypeController;
use App\Http\Controllers\Site\BannerController as SiteBannerController;
use App\Http\Controllers\Site\DocumentController as SiteDocumentAdminController;
use App\Http\Controllers\Site\ExecutiveController as SiteExecutiveController;
use App\Http\Controllers\Site\LinkController as SiteLinkController;
use App\Http\Controllers\Site\PageController as SitePageController;
use App\Http\Controllers\Site\PostController as SitePostAdminController;
use App\Http\Controllers\SiteDocumentController;
use App\Http\Controllers\SiteHomeController;
use App\Http\Controllers\SitePostController;
use App\Http\Controllers\SoftwareInventoryController;
use App\Http\Controllers\SoftwareLicenseController;
use App\Http\Controllers\SoftwareProductController;
use App\Http\Controllers\SystemSettingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Hospital public site
|--------------------------------------------------------------------------
|
| Open to visitors. Deliberately declared outside the auth group below — a
| public site behind a login is a failure nobody notices until someone from
| outside cannot open it.
|
*/
Route::get('/home', [SiteHomeController::class, 'index'])->name('site.home');
Route::get('/home/news', [SitePostController::class, 'index'])->name('site.news');
Route::get('/home/gallery', [SitePostController::class, 'gallery'])->name('site.gallery');
Route::get('/home/posts/{slug}', [SitePostController::class, 'show'])->name('site.post');
Route::get('/home/files/{file}', [SitePostController::class, 'download'])->name('site.post.file');
Route::get('/home/documents', [SiteDocumentController::class, 'index'])->name('site.documents');
Route::get('/home/documents/{siteDocument}', [SiteDocumentController::class, 'show'])->name('site.document');
Route::get('/home/documents/{siteDocument}/download', [SiteDocumentController::class, 'download'])->name('site.document.download');
Route::get('/home/{key}', [SiteHomeController::class, 'page'])->name('site.page');
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : view('welcome');
})->name('home');

Route::middleware(['auth'])->group(function () {
    /*
    |--------------------------------------------------------------------------
    | Staff self-service portal
    |--------------------------------------------------------------------------
    |
    | Scoped to the signed-in user's own employee record rather than to a
    | module permission, so staff can read back what they submitted.
    |
    */
    Route::prefix('me')->name('me.')->group(function () {
        Route::get('/', [SelfServiceController::class, 'index'])
            ->name('index');

        Route::get('/leaves', [SelfServiceController::class, 'leaves'])
            ->middleware('permission:leave.view.own')
            ->name('leaves');

        Route::get('/duties', [SelfServiceController::class, 'duties'])
            ->middleware('permission:duty.view.own')
            ->name('duties');

        Route::get('/attendance', [SelfServiceController::class, 'attendance'])
            ->middleware('permission:attendance.view.own')
            ->name('attendance');

        Route::get('/payslips', [SelfServiceController::class, 'payslips'])
            ->middleware('permission:payslip.view.own')
            ->name('payslips');

        Route::get('/repairs', [SelfServiceController::class, 'repairs'])
            ->middleware('permission:repair.view.own')
            ->name('repairs');

        Route::get('/meetings', [SelfServiceController::class, 'meetings'])
            ->middleware('permission:meeting.view.own')
            ->name('meetings');
    });

    Route::get('/dashboard', DashboardController::class)
        ->middleware('permission:dashboard.view')
        ->name('dashboard');

    Route::resource('departments', DepartmentController::class)
        ->except(['show']);

    Route::resource('positions', PositionController::class)
        ->except(['show']);

    Route::resource('employees', EmployeeController::class)
        ->except(['show']);

    Route::get('/employees/{employee}/personnel-profile', [EmployeePersonnelProfileController::class, 'edit'])
        ->middleware('permission:employee.sensitive.view|employee.update')
        ->name('employees.personnel-profile.edit');

    Route::put('/employees/{employee}/personnel-profile', [EmployeePersonnelProfileController::class, 'update'])
        ->middleware('permission:employee.sensitive.update|employee.update')
        ->name('employees.personnel-profile.update');

    Route::resource('users', UserController::class)
        ->except(['show']);

    Route::get('/system-settings', [SystemSettingController::class, 'index'])
        ->middleware('permission:setting.view')
        ->name('system-settings.index');

    Route::put('/system-settings', [SystemSettingController::class, 'update'])
        ->middleware('permission:setting.update')
        ->name('system-settings.update');

    Route::get('/audit-logs', [AuditLogController::class, 'index'])
        ->middleware('permission:audit.view')
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
        ->middleware('permission:approval.view')
        ->name('approvals.index');

    Route::patch('/approvals/{approval}/approve', [ApprovalController::class, 'approve'])
        ->middleware('permission:approval.approve')
        ->name('approvals.approve');

    Route::patch('/approvals/{approval}/reject', [ApprovalController::class, 'reject'])
        ->middleware('permission:approval.reject')
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
        ->middleware('permission:leave.view')
        ->name('leave-requests.dashboard');

    Route::get('/leave-calendar', [LeaveRequestController::class, 'calendar'])
        ->middleware('permission:leave.view')
        ->name('leave-requests.calendar');

    Route::resource('leave-requests', LeaveRequestController::class);

    Route::resource('attendance-devices', AttendanceDeviceController::class)
        ->except(['show']);

    Route::get('/attendance-logs/import', [AttendanceLogController::class, 'importForm'])
        ->middleware('permission:attendance.import')
        ->name('attendance-logs.import-form');

    Route::post('/attendance-logs/import', [AttendanceLogController::class, 'import'])
        ->middleware('permission:attendance.import')
        ->name('attendance-logs.import');

    Route::get('/attendance-logs', [AttendanceLogController::class, 'index'])
        ->name('attendance-logs.index');

    Route::get('/attendance-dashboard', [AttendanceSummaryController::class, 'dashboard'])
        ->middleware('permission:attendance.view')
        ->name('attendance-summaries.dashboard');

    Route::get('/attendance-summaries/print', [AttendanceSummaryController::class, 'print'])
        ->middleware('permission:attendance.view')
        ->name('attendance-summaries.print');

    Route::get('/attendance-summaries/generate', [AttendanceSummaryController::class, 'generateForm'])
        ->middleware('permission:attendance.import')
        ->name('attendance-summaries.generate-form');

    Route::post('/attendance-summaries/generate', [AttendanceSummaryController::class, 'generate'])
        ->middleware('permission:attendance.import')
        ->name('attendance-summaries.generate');

    Route::get('/attendance-summaries', [AttendanceSummaryController::class, 'index'])
        ->name('attendance-summaries.index');

    Route::resource('shift-types', ShiftTypeController::class)
        ->except(['show']);

    Route::get('/duty-schedules/bulk-create', [DutyScheduleController::class, 'bulkCreate'])
        ->middleware('permission:duty.create')
        ->name('duty-schedules.bulk-create');

    Route::post('/duty-schedules/bulk-create', [DutyScheduleController::class, 'bulkStore'])
        ->middleware('permission:duty.create')
        ->name('duty-schedules.bulk-store');

    Route::get('/duty-schedules/print', [DutyScheduleController::class, 'print'])
        ->middleware('permission:duty.view')
        ->name('duty-schedules.print');

    Route::get('/duty-schedules/calendar', [DutyScheduleController::class, 'calendar'])
        ->middleware('permission:duty.view')
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

    /*
    |--------------------------------------------------------------------------
    | Public site content
    |--------------------------------------------------------------------------
    |
    | Edits what visitors see at /home. The pages themselves are seeded and can
    | only be edited, never created or deleted, since the homepage places them
    | by a fixed key.
    |
    */
    Route::prefix('site')->name('site.')->group(function () {
        Route::resource('banners', SiteBannerController::class)
            ->except(['show'])
            ->names('banners')
            ->parameters(['banners' => 'banner']);

        Route::resource('links', SiteLinkController::class)
            ->except(['show'])
            ->names('links')
            ->parameters(['links' => 'link']);

        Route::get('/pages', [SitePageController::class, 'index'])->name('pages.index');
        Route::get('/pages/{page}/edit', [SitePageController::class, 'edit'])->name('pages.edit');
        Route::put('/pages/{page}', [SitePageController::class, 'update'])->name('pages.update');

        Route::resource('posts', SitePostAdminController::class)
            ->except(['show'])
            ->names('posts')
            ->parameters(['posts' => 'post']);

        Route::delete('/posts/{post}/images/{image}', [SitePostAdminController::class, 'destroyImage'])
            ->name('posts.images.destroy');

        Route::delete('/posts/{post}/files/{file}', [SitePostAdminController::class, 'destroyFile'])
            ->name('posts.files.destroy');

        Route::resource('documents', SiteDocumentAdminController::class)
            ->except(['show'])
            ->names('documents')
            ->parameters(['documents' => 'siteDocument']);

        Route::resource('executives', SiteExecutiveController::class)
            ->except(['show'])
            ->names('executives')
            ->parameters(['executives' => 'executive']);
    });

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
