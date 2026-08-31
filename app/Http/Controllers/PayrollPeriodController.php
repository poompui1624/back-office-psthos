<?php

namespace App\Http\Controllers;

use App\Models\PayrollPeriod;
use App\Models\Payslip;
use App\Services\PayrollGenerationService;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
            'name' => 'เงินเดือน '.$startDate->format('m/Y'),
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
            // items is eager loaded because the view lists each slip's income and
            // deduction lines inline; without it the page runs two queries per row.
            ->with(['employee.department', 'items'])
            ->where('payroll_period_id', $payrollPeriod->id)
            ->orderBy('employee_id')
            ->paginate(50);

        $totals = Payslip::query()
            ->where('payroll_period_id', $payrollPeriod->id)
            ->selectRaw('coalesce(sum(gross_income), 0) as gross_income')
            ->selectRaw('coalesce(sum(total_deduction), 0) as total_deduction')
            ->selectRaw('coalesce(sum(net_pay), 0) as net_pay')
            ->first();

        return view('payroll-periods.show', compact('payrollPeriod', 'payslips', 'totals'));
    }

    public function generate(PayrollPeriod $payrollPeriod)
    {
        abort_unless(auth()->user()->can('payroll.generate'), 403);

        if ($payrollPeriod->status === 'closed') {
            return back()->with('error', 'รอบเงินเดือนนี้ปิดแล้ว ไม่สามารถสร้างใหม่ได้');
        }

        ['created' => $created, 'updated' => $updated, 'skipped' => $skipped]
            = PayrollGenerationService::generate($payrollPeriod);

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
