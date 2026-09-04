<?php

use App\Models\SystemSetting;
use Carbon\Carbon;
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

        return asset('storage/'.$logo);
    }
}

if (! function_exists('hospital_name')) {
    function hospital_name(): string
    {
        return setting('hospital.name', setting('hospital_name', config('app.name', 'Hospital Backoffice')));
    }
}

if (! function_exists('thai_year')) {
    /**
     * Convert a Gregorian (CE) year to the Buddhist (BE) year shown throughout the UI.
     */
    function thai_year(int $christianYear): int
    {
        return $christianYear + 543;
    }
}

if (! function_exists('christian_year')) {
    /**
     * Convert a Buddhist (BE) year back to the Gregorian (CE) year the database stores.
     */
    function christian_year(int $thaiYear): int
    {
        return $thaiYear - 543;
    }
}

if (! function_exists('normalize_thai_year')) {
    /**
     * Coerce a year submitted from a filter into a Buddhist year.
     *
     * Accepts either calendar (2026 arrives as 2569) and falls back to the current
     * year when the value is missing or outside a plausible range.
     */
    function normalize_thai_year(mixed $year): int
    {
        $year = (int) $year;

        if ($year > 0 && $year < 2400) {
            $year = thai_year($year);
        }

        if ($year < 2500 || $year > 2700) {
            $year = thai_year((int) now()->year);
        }

        return $year;
    }
}

if (! function_exists('normalize_month')) {
    /**
     * Coerce a month submitted from a filter into 1–12, falling back to the current month.
     */
    function normalize_month(mixed $month): int
    {
        $month = (int) $month;

        return ($month >= 1 && $month <= 12) ? $month : (int) now()->month;
    }
}

if (! function_exists('resolve_month_filter')) {
    /**
     * Resolve the month/year filter every dashboard shares into one date range.
     *
     * `month` and `year` come back in the form the UI displays (year is Buddhist),
     * while `start_date` / `end_date` are Gregorian `Y-m-d` strings ready for a query.
     *
     * @return array{month: int, year: int, selected_month: Carbon, start_date: string, end_date: string}
     */
    function resolve_month_filter(mixed $month, mixed $year): array
    {
        $month = normalize_month($month);
        $year = normalize_thai_year($year);

        $selectedMonth = Carbon::create(christian_year($year), $month, 1)->startOfMonth();

        return [
            'month' => $month,
            'year' => $year,
            'selected_month' => $selectedMonth,
            'start_date' => $selectedMonth->copy()->startOfMonth()->toDateString(),
            'end_date' => $selectedMonth->copy()->endOfMonth()->toDateString(),
        ];
    }
}

if (! function_exists('human_file_size')) {
    /**
     * A file size in the largest unit that keeps it readable.
     *
     * Shared because three models display file sizes and each had grown its own
     * copy, which had already drifted: one reported a small file as "0.50 KB"
     * where the other said "512 B".
     */
    function human_file_size(int|float|null $bytes): string
    {
        $bytes = (int) $bytes;

        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2).' MB';
        }

        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 2).' KB';
        }

        return $bytes.' B';
    }
}
