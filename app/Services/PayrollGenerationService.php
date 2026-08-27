<?php

namespace App\Services;

use App\Models\AttendanceDailySummary;
use App\Models\PayrollPeriod;
use App\Models\Payslip;
use App\Models\SalaryProfile;
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

        $attendance = self::attendanceByEmployee($payrollPeriod, $profiles->pluck('employee_id'));

        $created = 0;
        $updated = 0;
        $skipped = 0;

        DB::transaction(function () use ($profiles, $attendance, $payrollPeriod, &$created, &$updated, &$skipped) {
            foreach ($profiles as $profile) {
                if (! $profile->employee) {
                    $skipped++;

                    continue;
                }

                $summaries = $attendance->get($profile->employee_id) ?? collect();

                $payslip = self::writePayslip($payrollPeriod, $profile, $summaries);

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
     */
    private static function writePayslip(
        PayrollPeriod $payrollPeriod,
        SalaryProfile $profile,
        Collection $summaries
    ): Payslip {
        $lateMinutes = (int) $summaries->sum('late_minutes');
        $earlyLeaveMinutes = (int) $summaries->sum('early_leave_minutes');
        $absentDays = $summaries->where('status', 'absent')->count();

        $incomeItems = self::incomeItems($profile);
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
     * @return array<int, array{code: string, name: string, amount: float}>
     */
    private static function incomeItems(SalaryProfile $profile): array
    {
        return [
            ['code' => 'BASE', 'name' => 'เงินเดือน', 'amount' => (float) $profile->base_salary],
            ['code' => 'POSITION', 'name' => 'เงินประจำตำแหน่ง', 'amount' => (float) $profile->position_allowance],
            ['code' => 'PROFESSIONAL', 'name' => 'เงินวิชาชีพ', 'amount' => (float) $profile->professional_allowance],
            ['code' => 'OTHER_ALLOWANCE', 'name' => 'รายได้อื่น', 'amount' => (float) $profile->other_allowance],
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
}
