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
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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

        $this->configureRateLimiting();
    }

    /**
     * Rate limits for the inventory endpoint that reporting agents post to.
     *
     * Every accepted report writes a row to computer_snapshots, so an
     * unthrottled endpoint grows that table as fast as anything can post to it.
     */
    private function configureRateLimiting(): void
    {
        RateLimiter::for('agent-inventory', function (Request $request) {
            $token = $request->bearerToken();

            // No credential at all. Nothing legitimate arrives this way, so
            // this is the path worth keeping tight, and it is the only one
            // that can be keyed on the address alone.
            if (! $token) {
                return Limit::perMinute(10)
                    ->by('agent-anon:'.$request->ip());
            }

            // One token is normally deployed across a whole fleet, and those
            // machines tend to report at the same time of day. Keying the
            // per-minute limit on the machine rather than the token means a
            // rollout of several hundred does not throttle itself, while a
            // single machine still cannot flood.
            $machine = $request->input('machine_uuid')
                ?: $request->input('mac_address')
                ?: $request->input('hostname')
                ?: $request->ip();

            return [
                Limit::perMinute(6)
                    ->by('agent-machine:'.hash('sha256', $token.'|'.$machine)),

                // A machine identity is read from the body, so a stolen token
                // could rotate it to dodge the limit above. This ceiling is
                // what actually caps a leaked token: generous enough for a
                // large fleet, far from unlimited.
                Limit::perHour(2000)
                    ->by('agent-token:'.hash('sha256', $token)),
            ];
        });
    }
}
