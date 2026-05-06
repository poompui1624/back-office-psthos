<?php

namespace App\Http\Controllers;

use App\Models\Payslip;

class PayslipController extends Controller
{
    public function show(Payslip $payslip)
    {
        abort_unless(auth()->user()->can('payroll.view'), 403);

        $payslip->load([
            'payrollPeriod',
            'employee.department',
            'employee.position',
            'salaryProfile',
            'items' => function ($query) {
                $query->orderBy('type')->orderBy('sort_order');
            },
        ]);

        return view('payslips.show', compact('payslip'));
    }

    public function print(Payslip $payslip)
    {
        abort_unless(auth()->user()->can('payroll.view'), 403);

        $payslip->load([
            'payrollPeriod',
            'employee.department',
            'employee.position',
            'salaryProfile',
            'items' => function ($query) {
                $query->orderBy('type')->orderBy('sort_order');
            },
        ]);

        return view('payslips.print', compact('payslip'));
    }
}
