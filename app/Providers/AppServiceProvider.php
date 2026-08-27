<?php

namespace App\Providers;

use App\Models\Asset;
use App\Models\AssetMovement;
use App\Models\Department;
use App\Models\DutySchedule;
use App\Models\Employee;
use App\Models\EmployeePersonnelProfile;
use App\Models\LeaveRequest;
use App\Models\MeetingBooking;
use App\Models\PayrollPeriod;
use App\Models\Payslip;
use App\Models\Position;
use App\Models\RepairRequest;
use App\Models\SalaryProfile;
use App\Models\Setting;
use App\Models\SoftwareLicense;
use App\Models\User;
use App\Observers\CoreAuditObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Models whose writes are recorded in the audit log.
     *
     * Anything that carries money, staffing, or a person's record belongs here.
     *
     * @var array<int, class-string>
     */
    private const AUDITED_MODELS = [
        Department::class,
        Position::class,
        Employee::class,
        EmployeePersonnelProfile::class,
        User::class,

        LeaveRequest::class,
        DutySchedule::class,

        SalaryProfile::class,
        PayrollPeriod::class,
        Payslip::class,

        Asset::class,
        AssetMovement::class,
        RepairRequest::class,
        SoftwareLicense::class,
        MeetingBooking::class,
    ];

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        foreach (self::AUDITED_MODELS as $model) {
            $model::observe(CoreAuditObserver::class);
        }

        if (class_exists(Setting::class)) {
            Setting::observe(CoreAuditObserver::class);
        }
    }
}
