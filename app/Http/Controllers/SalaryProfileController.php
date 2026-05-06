<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\SalaryProfile;
use Illuminate\Http\Request;

class SalaryProfileController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->can('payroll.view'), 403);

        $search = $request->string('search')->toString();

        $profiles = SalaryProfile::query()
            ->with(['employee.department'])
            ->when($search, function ($query) use ($search) {
                $query->whereHas('employee', function ($employeeQuery) use ($search) {
                    $employeeQuery->where('employee_code', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('salary-profiles.index', compact('profiles', 'search'));
    }

    public function create()
    {
        abort_unless(auth()->user()->can('payroll.create'), 403);

        return view('salary-profiles.create', [
            'employees' => Employee::where('status', 'active')
                ->whereDoesntHave('salaryProfile')
                ->orderBy('employee_code')
                ->get(),
        ]);
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->can('payroll.create'), 403);

        $validated = $this->validateProfile($request);

        SalaryProfile::create($validated);

        return redirect()
            ->route('salary-profiles.index')
            ->with('success', 'บันทึกข้อมูลเงินเดือนเรียบร้อยแล้ว');
    }

    public function edit(SalaryProfile $salaryProfile)
    {
        abort_unless(auth()->user()->can('payroll.update'), 403);

        return view('salary-profiles.edit', [
            'salaryProfile' => $salaryProfile,
            'employees' => Employee::where('status', 'active')
                ->orderBy('employee_code')
                ->get(),
        ]);
    }

    public function update(Request $request, SalaryProfile $salaryProfile)
    {
        abort_unless(auth()->user()->can('payroll.update'), 403);

        $validated = $this->validateProfile($request, $salaryProfile);

        $salaryProfile->update($validated);

        return redirect()
            ->route('salary-profiles.index')
            ->with('success', 'แก้ไขข้อมูลเงินเดือนเรียบร้อยแล้ว');
    }

    public function destroy(SalaryProfile $salaryProfile)
    {
        abort_unless(auth()->user()->can('payroll.delete'), 403);

        $salaryProfile->delete();

        return redirect()
            ->route('salary-profiles.index')
            ->with('success', 'ลบข้อมูลเงินเดือนเรียบร้อยแล้ว');
    }

    private function validateProfile(Request $request, ?SalaryProfile $salaryProfile = null): array
    {
        return $request->validate([
            'employee_id' => [
                'required',
                'exists:employees,id',
                $salaryProfile
                    ? 'unique:salary_profiles,employee_id,' . $salaryProfile->id
                    : 'unique:salary_profiles,employee_id',
            ],
            'base_salary' => ['required', 'numeric', 'min:0'],
            'position_allowance' => ['nullable', 'numeric', 'min:0'],
            'professional_allowance' => ['nullable', 'numeric', 'min:0'],
            'other_allowance' => ['nullable', 'numeric', 'min:0'],
            'social_security' => ['nullable', 'numeric', 'min:0'],
            'tax' => ['nullable', 'numeric', 'min:0'],
            'provident_fund' => ['nullable', 'numeric', 'min:0'],
            'other_deduction' => ['nullable', 'numeric', 'min:0'],
            'late_deduction_per_minute' => ['nullable', 'numeric', 'min:0'],
            'early_leave_deduction_per_minute' => ['nullable', 'numeric', 'min:0'],
            'absent_deduction_per_day' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'remark' => ['nullable', 'string'],
        ]);
    }
}
