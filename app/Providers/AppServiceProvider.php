<?php

namespace App\Providers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Setting;
use App\Models\User;
use App\Observers\CoreAuditObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Department::observe(CoreAuditObserver::class);
        Position::observe(CoreAuditObserver::class);
        Employee::observe(CoreAuditObserver::class);
        User::observe(CoreAuditObserver::class);
        Paginator::useBootstrapFive();

        if (class_exists(Setting::class)) {
            Setting::observe(CoreAuditObserver::class);
        }
    }
}