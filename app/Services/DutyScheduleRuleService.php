<?php

namespace App\Services;

use App\Models\DutySchedule;
use App\Models\LeaveRequest;
use App\Models\ShiftType;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Staffing rules checked when a roster is drawn up.
 *
 * These are advisory. A ward short of people has to break them sometimes, so
 * the caller shows what was found and still saves; blocking the save would
 * only push the roster back into a spreadsheet.
 */
class DutyScheduleRuleService
{
    /**
     * Hours in a week beyond which the roster is flagged.
     */
    public const WEEKLY_HOUR_LIMIT = 60;

    /**
     * Check one proposed assignment against the roster already stored.
     *
     * @return array<int, string> Human-readable warnings, empty when nothing is wrong.
     */
    public static function warningsFor(
        int $employeeId,
        ShiftType $shiftType,
        string $workDate,
        ?int $ignoreScheduleId = null
    ): array {
        $date = Carbon::parse($workDate);

        $warnings = [];

        if ($clash = self::approvedLeaveOn($employeeId, $date)) {
            $warnings[] = "ตรงกับวันลาที่อนุมัติแล้ว ({$clash})";
        }

        if ($shiftType->crosses_midnight && self::hasAdjacentNightShift($employeeId, $date, $ignoreScheduleId)) {
            $warnings[] = 'มีเวรดึกติดกันกับวันก่อนหน้าหรือวันถัดไป';
        }

        $weeklyHours = self::weeklyHours($employeeId, $date, $ignoreScheduleId) + self::shiftHours($shiftType);

        if ($weeklyHours > self::WEEKLY_HOUR_LIMIT) {
            $rounded = rtrim(rtrim(number_format($weeklyHours, 1), '0'), '.');
            $warnings[] = "ชั่วโมงรวมในสัปดาห์นี้จะเป็น {$rounded} ชม. เกินเกณฑ์ ".self::WEEKLY_HOUR_LIMIT.' ชม.';
        }

        return $warnings;
    }

    /**
     * The leave request number covering this date, when there is one.
     */
    private static function approvedLeaveOn(int $employeeId, Carbon $date): ?string
    {
        return LeaveRequest::query()
            ->where('employee_id', $employeeId)
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $date->toDateString())
            ->whereDate('end_date', '>=', $date->toDateString())
            ->value('request_no');
    }

    private static function hasAdjacentNightShift(int $employeeId, Carbon $date, ?int $ignoreScheduleId): bool
    {
        // whereDate rather than whereIn: a date column comes back as a bare
        // date on MySQL but as a midnight datetime on SQLite, and an equality
        // match on the raw value only lines up on one of them.
        return self::rosterQuery($employeeId, $ignoreScheduleId)
            ->where(function ($query) use ($date) {
                $query->whereDate('work_date', $date->copy()->subDay()->toDateString())
                    ->orWhereDate('work_date', $date->copy()->addDay()->toDateString());
            })
            ->whereHas('shiftType', fn ($query) => $query->where('crosses_midnight', true))
            ->exists();
    }

    private static function weeklyHours(int $employeeId, Carbon $date, ?int $ignoreScheduleId): float
    {
        $schedules = self::rosterQuery($employeeId, $ignoreScheduleId)
            ->whereDate('work_date', '>=', $date->copy()->startOfWeek(Carbon::MONDAY)->toDateString())
            ->whereDate('work_date', '<=', $date->copy()->endOfWeek(Carbon::SUNDAY)->toDateString())
            ->get();

        return round($schedules->sum(fn (DutySchedule $s) => OtCalculationService::hoursFor($s)), 2);
    }

    /**
     * Shifts that occupy the employee. Cancelled ones do not.
     */
    private static function rosterQuery(int $employeeId, ?int $ignoreScheduleId)
    {
        return DutySchedule::query()
            ->where('employee_id', $employeeId)
            ->whereIn('status', ['assigned', 'confirmed'])
            ->when($ignoreScheduleId, fn ($query) => $query->whereKeyNot($ignoreScheduleId));
    }

    private static function shiftHours(ShiftType $shiftType): float
    {
        $start = Carbon::parse('2000-01-01 '.$shiftType->start_time);
        $end = Carbon::parse('2000-01-01 '.$shiftType->end_time);

        if ($shiftType->crosses_midnight || $end->lessThanOrEqualTo($start)) {
            $end->addDay();
        }

        return round($start->diffInMinutes($end) / 60, 2);
    }

    /**
     * Warnings for a batch, keyed by the input index the caller supplied.
     *
     * @param  Collection<int, array{employee_id: int, shift_type: ShiftType, work_date: string}>  $assignments
     * @return array<int, array<int, string>>
     */
    public static function warningsForBatch(Collection $assignments): array
    {
        $found = [];

        foreach ($assignments as $index => $assignment) {
            $warnings = self::warningsFor(
                $assignment['employee_id'],
                $assignment['shift_type'],
                $assignment['work_date']
            );

            if ($warnings !== []) {
                $found[$index] = $warnings;
            }
        }

        return $found;
    }
}
