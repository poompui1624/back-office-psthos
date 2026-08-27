<?php

namespace App\Http\Controllers;

use App\Models\Payslip;
use Illuminate\Contracts\View\View;

class PayslipController extends Controller
{
    public function show(Payslip $payslip): View
    {
        $this->authorize('view', $payslip);

        return view('payslips.show', ['payslip' => $this->loaded($payslip)]);
    }

    public function print(Payslip $payslip): View
    {
        $this->authorize('view', $payslip);

        return view('payslips.print', ['payslip' => $this->loaded($payslip)]);
    }

    private function loaded(Payslip $payslip): Payslip
    {
        return $payslip->load([
            'payrollPeriod',
            'employee.department',
            'employee.position',
            'salaryProfile',
            'items' => fn ($query) => $query->orderBy('type')->orderBy('sort_order'),
        ]);
    }
}
