<?php

namespace App\Services;

use App\Models\DutySchedule;
use App\Models\SalaryProfile;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class OtCalculationService
{
    /**
     * Duty statuses that count as worked overtime.
     *
     * A shift still sitting at `assigned` has not been confirmed by the ward,
     * so paying for it would be paying for a plan rather than for work done.
     *
     * @var array<int, string>
     */
    public const PAYABLE_STATUSES = ['confirmed'];

    /**
     * Hours worked on a single overtime shift.
     *
     * `end_at` is stored as a full datetime, so a shift crossing midnight
     * already spans two days and needs no special case. A row whose end lands
     * on or before its start is treated as a same-clock-time entry error and
     * contributes nothing rather than a negative.
     */
    public static function hoursFor(DutySchedule $schedule): float
    {
        $start = $schedule->start_at;
        $end = $schedule->end_at;

        if (! $start instanceof CarbonInterface || ! $end instanceof CarbonInterface) {
            return 0.0;
        }

        if ($end->lessThanOrEqualTo($start)) {
            return 0.0;
        }

        return round($start->diffInMinutes($end) / 60, 4);
    }

    /**
     * Pay for a single overtime shift.
     *
     * A shift carrying a flat rate pays that amount whatever its length;
     * otherwise the employee's hourly OT rate is scaled by the shift multiplier.
     */
    public static function amountFor(DutySchedule $schedule, ?SalaryProfile $profile): float
    {
        $shiftType = $schedule->shiftType;

        if (! $shiftType?->is_ot) {
            return 0.0;
        }

        if ($shiftType->ot_flat_rate !== null) {
            return round((float) $shiftType->ot_flat_rate, 2);
        }

        $hourlyRate = (float) ($profile?->ot_rate_per_hour ?? 0);

        if ($hourlyRate <= 0) {
            return 0.0;
        }

        $multiplier = (float) ($shiftType->ot_multiplier ?? 1);

        return round(self::hoursFor($schedule) * $hourlyRate * $multiplier, 2);
    }

    /**
     * Total overtime for one employee across a date range.
     *
     * @return array{hours: float, amount: float, shifts: int}
     */
    public static function forEmployee(
        int $employeeId,
        string $startDate,
        string $endDate,
        ?SalaryProfile $profile = null
    ): array {
        $schedules = DutySchedule::query()
            ->with('shiftType')
            ->where('employee_id', $employeeId)
            ->whereIn('status', self::PAYABLE_STATUSES)
            ->whereDate('work_date', '>=', $startDate)
            ->whereDate('work_date', '<=', $endDate)
            ->whereHas('shiftType', fn ($query) => $query->where('is_ot', true))
            ->get();

        return self::totalFor($schedules, $profile);
    }

    /**
     * @param  Collection<int, DutySchedule>  $schedules
     * @return array{hours: float, amount: float, shifts: int}
     */
    public static function totalFor(Collection $schedules, ?SalaryProfile $profile): array
    {
        $payable = $schedules->filter(
            fn (DutySchedule $schedule) => $schedule->shiftType?->is_ot
                && in_array($schedule->status, self::PAYABLE_STATUSES, true)
        );

        return [
            'shifts' => $payable->count(),
            'hours' => round($payable->sum(fn (DutySchedule $s) => self::hoursFor($s)), 2),
            'amount' => round($payable->sum(fn (DutySchedule $s) => self::amountFor($s, $profile)), 2),
        ];
    }
}
