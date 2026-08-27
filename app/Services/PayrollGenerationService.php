<?php

namespace App\Services;

use App\Models\AttendanceDailySummary;
use App\Models\DutySchedule;
use App\Models\LeaveRequest;
use App\Models\PayrollPeriod;
use App\Models\Payslip;
use App\Models\SalaryProfile;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PayrollGenerationService
{
    /**
     * Build (or rebuild) the payslips for a period from the active salary profiles.
     *
     * Regenerating is safe: each employee's payslip is matched on
     * (period, employee) and its line items are rewritten in place.
     *
     * @return array{created: int, updated: int, skipped: int}
     */
    public static function generate(PayrollPeriod $payrollPeriod): array
    {
        $profiles = SalaryProfile::query()
            ->with('employee')
            ->where('is_active', true)
            ->get();

        $employeeIds = $profiles->pluck('employee_id');
        $attendance = self::attendanceByEmployee($payrollPeriod, $employeeIds);
        $overtime = self::overtimeByEmployee($payrollPeriod, $employeeIds);
        $leaveDates = self::approvedLeaveDatesByEmployee($payrollPeriod, $employeeIds);

        $created = 0;
        $updated = 0;
        $skipped = 0;

        DB::transaction(function () use ($profiles, $attendance, $overtime, $leaveDates, $payrollPeriod, &$created, &$updated, &$skipped) {
            foreach ($profiles as $profile) {
                if (! $profile->employee) {
                    $skipped++;

                    continue;
                }

                $summaries = $attendance->get($profile->employee_id) ?? collect();
                $otShifts = $overtime->get($profile->employee_id) ?? collect();

                $onLeave = $leaveDates->get($profile->employee_id) ?? collect();

                $payslip = self::writePayslip($payrollPeriod, $profile, $summaries, $otShifts, $onLeave);

                $payslip->wasRecentlyCreated ? $created++ : $updated++;
            }

            $payrollPeriod->update([
                'status' => 'generated',
                'generated_at' => now(),
            ]);
        });

        return ['created' => $created, 'updated' => $updated, 'skipped' => $skipped];
    }

    /**
     * Every daily summary in the period, grouped by employee.
     *
     * Fetched in one query rather than one per employee, which is what the loop
     * in PayrollPeriodController used to do.
     *
     * @param  Collection<int, int>  $employeeIds
     * @return Collection<int, Collection<int, AttendanceDailySummary>>
     */
    private static function attendanceByEmployee(PayrollPeriod $payrollPeriod, Collection $employeeIds): Collection
    {
        return AttendanceDailySummary::query()
            ->whereIn('employee_id', $employeeIds)
            ->whereDate('work_date', '>=', $payrollPeriod->start_date)
            ->whereDate('work_date', '<=', $payrollPeriod->end_date)
            ->get()
            ->groupBy('employee_id');
    }

    /**
     * @param  Collection<int, AttendanceDailySummary>  $summaries
     * @param  Collection<int, DutySchedule>  $otShifts
     * @param  Collection<int, string>  $onLeave  Y-m-d dates covered by approved leave
     */
    private static function writePayslip(
        PayrollPeriod $payrollPeriod,
        SalaryProfile $profile,
        Collection $summaries,
        Collection $otShifts,
        Collection $onLeave
    ): Payslip {
        $lateMinutes = (int) $summaries->sum('late_minutes');
        $earlyLeaveMinutes = (int) $summaries->sum('early_leave_minutes');
        // A day covered by approved leave is not absence, so it must not be
        // deducted even when the clock never recorded an entry for it.
        $absentDays = $summaries
            ->where('status', 'absent')
            ->reject(fn ($summary) => $onLeave->contains(
                $summary->work_date instanceof CarbonInterface
                    ? $summary->work_date->toDateString()
                    : (string) $summary->work_date
            ))
            ->count();

        $overtime = OtCalculationService::totalFor($otShifts, $profile);

        $incomeItems = self::incomeItems($profile, $overtime);
        $deductionItems = self::deductionItems($profile, $lateMinutes, $earlyLeaveMinutes, $absentDays);

        $grossIncome = collect($incomeItems)->sum('amount');
        $totalDeduction = collect($deductionItems)->sum('amount');

        $payslip = Payslip::updateOrCreate(
            [
                'payroll_period_id' => $payrollPeriod->id,
                'employee_id' => $profile->employee_id,
            ],
            [
                'salary_profile_id' => $profile->id,
                'gross_income' => $grossIncome,
                'total_deduction' => $totalDeduction,
                'net_pay' => $grossIncome - $totalDeduction,
                'late_minutes' => $lateMinutes,
                'early_leave_minutes' => $earlyLeaveMinutes,
                'absent_days' => $absentDays,
                'status' => 'draft',
                'generated_at' => now(),
            ]
        );

        self::writeItems($payslip, $incomeItems, $deductionItems);

        return $payslip;
    }

    /**
     * @param  array{hours: float, amount: float, shifts: int}  $overtime
     * @return array<int, array<string, mixed>>
     */
    private static function incomeItems(SalaryProfile $profile, array $overtime): array
    {
        return [
            ['code' => 'BASE', 'name' => 'เงินเดือน', 'amount' => (float) $profile->base_salary],
            ['code' => 'POSITION', 'name' => 'เงินประจำตำแหน่ง', 'amount' => (float) $profile->position_allowance],
            ['code' => 'PROFESSIONAL', 'name' => 'เงินวิชาชีพ', 'amount' => (float) $profile->professional_allowance],
            ['code' => 'OTHER_ALLOWANCE', 'name' => 'รายได้อื่น', 'amount' => (float) $profile->other_allowance],
            [
                'code' => 'OT',
                'name' => 'ค่าล่วงเวลา',
                'quantity' => $overtime['hours'],
                'unit_amount' => (float) $profile->ot_rate_per_hour,
                'amount' => $overtime['amount'],
            ],
        ];
    }

    /**
     * @return array<int, array{code: string, name: string, amount: float, quantity?: int, unit_amount?: float}>
     */
    private static function deductionItems(
        SalaryProfile $profile,
        int $lateMinutes,
        int $earlyLeaveMinutes,
        int $absentDays
    ): array {
        return [
            ['code' => 'SOCIAL_SECURITY', 'name' => 'ประกันสังคม', 'amount' => (float) $profile->social_security],
            ['code' => 'TAX', 'name' => 'ภาษี', 'amount' => (float) $profile->tax],
            ['code' => 'PROVIDENT_FUND', 'name' => 'กองทุนสำรองเลี้ยงชีพ', 'amount' => (float) $profile->provident_fund],
            [
                'code' => 'LATE',
                'name' => 'หักมาสาย',
                'quantity' => $lateMinutes,
                'unit_amount' => (float) $profile->late_deduction_per_minute,
                'amount' => $lateMinutes * (float) $profile->late_deduction_per_minute,
            ],
            [
                'code' => 'EARLY_LEAVE',
                'name' => 'หักกลับก่อน',
                'quantity' => $earlyLeaveMinutes,
                'unit_amount' => (float) $profile->early_leave_deduction_per_minute,
                'amount' => $earlyLeaveMinutes * (float) $profile->early_leave_deduction_per_minute,
            ],
            [
                'code' => 'ABSENT',
                'name' => 'หักขาดงาน',
                'quantity' => $absentDays,
                'unit_amount' => (float) $profile->absent_deduction_per_day,
                'amount' => $absentDays * (float) $profile->absent_deduction_per_day,
            ],
            ['code' => 'OTHER_DEDUCTION', 'name' => 'รายการหักอื่น', 'amount' => (float) $profile->other_deduction],
        ];
    }

    /**
     * Rewrite the payslip lines, dropping anything that came out as zero.
     *
     * @param  array<int, array<string, mixed>>  $incomeItems
     * @param  array<int, array<string, mixed>>  $deductionItems
     */
    private static function writeItems(Payslip $payslip, array $incomeItems, array $deductionItems): void
    {
        $payslip->items()->delete();

        $sortOrder = 1;

        foreach (['income' => $incomeItems, 'deduction' => $deductionItems] as $type => $items) {
            foreach ($items as $item) {
                if (($item['amount'] ?? 0) <= 0) {
                    continue;
                }

                $payslip->items()->create([
                    'type' => $type,
                    'code' => $item['code'],
                    'name' => $item['name'],
                    'quantity' => $item['quantity'] ?? 1,
                    'unit_amount' => $item['unit_amount'] ?? $item['amount'],
                    'amount' => $item['amount'],
                    'sort_order' => $sortOrder++,
                ]);
            }
        }
    }

    /**
     * Confirmed overtime shifts in the period, grouped by employee.
     *
     * @param  Collection<int, int>  $employeeIds
     * @return Collection<int, Collection<int, DutySchedule>>
     */
    private static function overtimeByEmployee(PayrollPeriod $payrollPeriod, Collection $employeeIds): Collection
    {
        return DutySchedule::query()
            ->with('shiftType')
            ->whereIn('employee_id', $employeeIds)
            ->whereIn('status', OtCalculationService::PAYABLE_STATUSES)
            ->whereDate('work_date', '>=', $payrollPeriod->start_date)
            ->whereDate('work_date', '<=', $payrollPeriod->end_date)
            ->whereHas('shiftType', fn ($query) => $query->where('is_ot', true))
            ->get()
            ->groupBy('employee_id');
    }

    /**
     * Dates inside the period covered by an approved leave request, by employee.
     *
     * @param  Collection<int, int>  $employeeIds
     * @return Collection<int, Collection<int, string>>
     */
    private static function approvedLeaveDatesByEmployee(PayrollPeriod $payrollPeriod, Collection $employeeIds): Collection
    {
        $start = Carbon::parse($payrollPeriod->start_date);
        $end = Carbon::parse($payrollPeriod->end_date);

        return LeaveRequest::query()
            ->whereIn('employee_id', $employeeIds)
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $end->toDateString())
            ->whereDate('end_date', '>=', $start->toDateString())
            ->get()
            ->groupBy('employee_id')
            ->map(function (Collection $requests) use ($start, $end) {
                return $requests
                    ->flatMap(function (LeaveRequest $request) use ($start, $end) {
                        $from = Carbon::parse($request->start_date)->max($start);
                        $to = Carbon::parse($request->end_date)->min($end);

                        return collect(CarbonPeriod::create($from, $to))
                            ->map(fn (Carbon $date) => $date->toDateString());
                    })
                    ->unique()
                    ->values();
            });
    }
}
