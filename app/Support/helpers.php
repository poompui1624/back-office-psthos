<?php

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Cache;

if (! function_exists('setting')) {
    function setting(string $key, mixed $default = null): mixed
    {
        if (! class_exists(SystemSetting::class)) {
            return $default;
        }

        $settings = Cache::rememberForever('system_settings_all', function () {
            return SystemSetting::query()
                ->where('is_active', true)
                ->pluck('value', 'key')
                ->toArray();
        });

        return $settings[$key] ?? $default;
    }
}

if (! function_exists('hospital_logo_url')) {
    function hospital_logo_url(): ?string
    {
        $logo = setting('hospital.logo');

        if (! $logo) {
            return null;
        }

        return asset('storage/' . $logo);
    }
}

if (! function_exists('hospital_name')) {
    function hospital_name(): string
    {
        return setting('hospital.name', setting('hospital_name', config('app.name', 'Hospital Backoffice')));
    }
}
