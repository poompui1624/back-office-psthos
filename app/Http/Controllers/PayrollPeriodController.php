<?php

namespace App\Http\Controllers;

use App\Models\AttendanceDailySummary;
use App\Models\PayrollPeriod;
use App\Models\Payslip;
use App\Models\SalaryProfile;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayrollPeriodController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()->can('payroll.view'), 403);

        $periods = PayrollPeriod::query()
            ->withCount('payslips')
            ->latest('year')
            ->latest('month')
            ->paginate(20);

        return view('payroll-periods.index', compact('periods'));
    }

    public function create()
    {
        abort_unless(auth()->user()->can('payroll.create'), 403);

        return view('payroll-periods.create');
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->can('payroll.create'), 403);

        $validated = $request->validate([
            'year' => ['required', 'integer', 'min:2000'],
            'month' => ['required', 'integer', 'between:1,12'],
            'remark' => ['nullable', 'string'],
        ]);

        $existingPeriod = PayrollPeriod::withTrashed()
            ->where('year', $validated['year'])
            ->where('month', $validated['month'])
            ->first();

        if ($existingPeriod) {
            if ($existingPeriod->trashed()) {
                $existingPeriod->restore();

                return redirect()
                    ->route('payroll-periods.show', $existingPeriod)
                    ->with('success', 'พบรอบเงินเดือนนี้ที่เคยถูกลบ จึงกู้คืนกลับมาแล้ว');
            }

            return redirect()
                ->route('payroll-periods.show', $existingPeriod)
                ->with('error', 'มีรอบเงินเดือนนี้อยู่แล้ว ไม่สามารถสร้างซ้ำได้');
        }

        $startDate = Carbon::create($validated['year'], $validated['month'], 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $payrollPeriod = PayrollPeriod::create([
            'year' => $validated['year'],
            'month' => $validated['month'],
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'name' => 'เงินเดือน ' . $startDate->format('m/Y'),
            'status' => 'draft',
            'created_by' => auth()->id(),
            'remark' => $validated['remark'] ?? null,
        ]);

        return redirect()
            ->route('payroll-periods.show', $payrollPeriod)
            ->with('success', 'สร้างรอบเงินเดือนเรียบร้อยแล้ว');
    }

    public function show(PayrollPeriod $payrollPeriod)
    {
        abort_unless(auth()->user()->can('payroll.view'), 403);

        $payrollPeriod->load('creator');

        $payslips = Payslip::query()
            ->with(['employee.department'])
            ->where('payroll_period_id', $payrollPeriod->id)
            ->orderBy('employee_id')
            ->paginate(50);

        return view('payroll-periods.show', compact('payrollPeriod', 'payslips'));
    }

    public function generate(PayrollPeriod $payrollPeriod)
    {
        abort_unless(auth()->user()->can('payroll.generate'), 403);

        if ($payrollPeriod->status === 'closed') {
            return back()->with('error', 'รอบเงินเดือนนี้ปิดแล้ว ไม่สามารถสร้างใหม่ได้');
        }

        $created = 0;
        $updated = 0;
        $skipped = 0;

        $profiles = SalaryProfile::query()
            ->with('employee')
            ->where('is_active', true)
            ->get();

        DB::transaction(function () use (
            $profiles,
            $payrollPeriod,
            &$created,
            &$updated,
            &$skipped
        ) {
            foreach ($profiles as $profile) {
                if (! $profile->employee) {
                    $skipped++;
                    continue;
                }

                $attendance = AttendanceDailySummary::query()
                    ->where('employee_id', $profile->employee_id)
                    ->whereDate('work_date', '>=', $payrollPeriod->start_date)
                    ->whereDate('work_date', '<=', $payrollPeriod->end_date)
                    ->get();

                $lateMinutes = (int) $attendance->sum('late_minutes');
                $earlyLeaveMinutes = (int) $attendance->sum('early_leave_minutes');
                $absentDays = (int) $attendance->where('status', 'absent')->count();

                $incomeItems = [
                    [
                        'code' => 'BASE',
                        'name' => 'เงินเดือน',
                        'amount' => (float) $profile->base_salary,
                    ],
                    [
                        'code' => 'POSITION',
                        'name' => 'เงินประจำตำแหน่ง',
                        'amount' => (float) $profile->position_allowance,
                    ],
                    [
                        'code' => 'PROFESSIONAL',
                        'name' => 'เงินวิชาชีพ',
                        'amount' => (float) $profile->professional_allowance,
                    ],
                    [
                        'code' => 'OTHER_ALLOWANCE',
                        'name' => 'รายได้อื่น',
                        'amount' => (float) $profile->other_allowance,
                    ],
                ];

                $lateDeduction = $lateMinutes * (float) $profile->late_deduction_per_minute;
                $earlyLeaveDeduction = $earlyLeaveMinutes * (float) $profile->early_leave_deduction_per_minute;
                $absentDeduction = $absentDays * (float) $profile->absent_deduction_per_day;

                $deductionItems = [
                    [
                        'code' => 'SOCIAL_SECURITY',
                        'name' => 'ประกันสังคม',
                        'amount' => (float) $profile->social_security,
                    ],
                    [
                        'code' => 'TAX',
                        'name' => 'ภาษี',
                        'amount' => (float) $profile->tax,
                    ],
                    [
                        'code' => 'PROVIDENT_FUND',
                        'name' => 'กองทุนสำรองเลี้ยงชีพ',
                        'amount' => (float) $profile->provident_fund,
                    ],
                    [
                        'code' => 'LATE',
                        'name' => 'หักมาสาย',
                        'quantity' => $lateMinutes,
                        'unit_amount' => (float) $profile->late_deduction_per_minute,
                        'amount' => $lateDeduction,
                    ],
                    [
                        'code' => 'EARLY_LEAVE',
                        'name' => 'หักกลับก่อน',
                        'quantity' => $earlyLeaveMinutes,
                        'unit_amount' => (float) $profile->early_leave_deduction_per_minute,
                        'amount' => $earlyLeaveDeduction,
                    ],
                    [
                        'code' => 'ABSENT',
                        'name' => 'หักขาดงาน',
                        'quantity' => $absentDays,
                        'unit_amount' => (float) $profile->absent_deduction_per_day,
                        'amount' => $absentDeduction,
                    ],
                    [
                        'code' => 'OTHER_DEDUCTION',
                        'name' => 'รายการหักอื่น',
                        'amount' => (float) $profile->other_deduction,
                    ],
                ];

                $grossIncome = collect($incomeItems)->sum('amount');
                $totalDeduction = collect($deductionItems)->sum('amount');
                $netPay = $grossIncome - $totalDeduction;

                $payslip = Payslip::updateOrCreate(
                    [
                        'payroll_period_id' => $payrollPeriod->id,
                        'employee_id' => $profile->employee_id,
                    ],
                    [
                        'salary_profile_id' => $profile->id,
                        'gross_income' => $grossIncome,
                        'total_deduction' => $totalDeduction,
                        'net_pay' => $netPay,
                        'late_minutes' => $lateMinutes,
                        'early_leave_minutes' => $earlyLeaveMinutes,
                        'absent_days' => $absentDays,
                        'status' => 'draft',
                        'generated_at' => now(),
                    ]
                );

                $payslip->items()->delete();

                $sort = 1;

                foreach ($incomeItems as $item) {
                    if (($item['amount'] ?? 0) <= 0) {
                        continue;
                    }

                    $payslip->items()->create([
                        'type' => 'income',
                        'code' => $item['code'],
                        'name' => $item['name'],
                        'quantity' => $item['quantity'] ?? 1,
                        'unit_amount' => $item['unit_amount'] ?? $item['amount'],
                        'amount' => $item['amount'],
                        'sort_order' => $sort++,
                    ]);
                }

                foreach ($deductionItems as $item) {
                    if (($item['amount'] ?? 0) <= 0) {
                        continue;
                    }

                    $payslip->items()->create([
                        'type' => 'deduction',
                        'code' => $item['code'],
                        'name' => $item['name'],
                        'quantity' => $item['quantity'] ?? 1,
                        'unit_amount' => $item['unit_amount'] ?? $item['amount'],
                        'amount' => $item['amount'],
                        'sort_order' => $sort++,
                    ]);
                }

                if ($payslip->wasRecentlyCreated) {
                    $created++;
                } else {
                    $updated++;
                }
            }

            $payrollPeriod->update([
                'status' => 'generated',
                'generated_at' => now(),
            ]);
        });

        return redirect()
            ->route('payroll-periods.show', $payrollPeriod)
            ->with('success', "สร้างสลิปเงินเดือนสำเร็จ เพิ่มใหม่ {$created} รายการ, อัปเดต {$updated} รายการ, ข้าม {$skipped} รายการ");
    }

    public function close(PayrollPeriod $payrollPeriod)
    {
        abort_unless(auth()->user()->can('payroll.update'), 403);

        $payrollPeriod->update([
            'status' => 'closed',
            'closed_at' => now(),
        ]);

        return back()->with('success', 'ปิดรอบเงินเดือนเรียบร้อยแล้ว');
    }
}