<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->can('employee.view'), 403);

        $search = $request->string('search')->toString();

        $employees = Employee::query()
            ->visibleTo(auth()->user())
            ->with(['department', 'position'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('employee_code', 'like', "%{$search}%")
                        ->orWhere('citizen_id', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhereHas('department', function ($departmentQuery) use ($search) {
                            $departmentQuery->where('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('position', function ($positionQuery) use ($search) {
                            $positionQuery->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->orderBy('employee_code')
            ->paginate(20)
            ->withQueryString();

        return view('employees.index', compact('employees', 'search'));
    }

    public function create()
    {
        abort_unless(auth()->user()->can('employee.create'), 403);

        $departments = Department::query()
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        $positions = Position::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('employees.create', compact('departments', 'positions'));
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->can('employee.create'), 403);

        $validated = $request->validate([
            'employee_code' => ['required', 'string', 'max:50', 'unique:employees,employee_code'],
            'citizen_id' => ['nullable', 'string', 'max:20', 'unique:employees,citizen_id'],
            'prefix' => ['nullable', 'string', 'max:50'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'gender' => ['nullable', 'string', 'max:20'],
            'birth_date' => ['nullable', 'date'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'position_id' => ['nullable', 'exists:positions,id'],
            'employment_type' => ['nullable', 'string', 'max:100'],
            'start_work_date' => ['nullable', 'date'],
            'status' => ['required', 'string', 'max:50'],
        ]);

        Employee::create($validated);

        return redirect()
            ->route('employees.index')
            ->with('success', 'เพิ่มบุคลากรเรียบร้อยแล้ว');
    }

    public function edit(Employee $employee)
    {
        abort_unless(auth()->user()->can('employee.update'), 403);

        $departments = Department::query()
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        $positions = Position::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $attachments = $employee->attachments()
            ->with('uploader')
            ->latest()
            ->get();

        return view('employees.edit', compact('employee', 'departments', 'positions', 'attachments'));
    }

    public function update(Request $request, Employee $employee)
    {
        abort_unless(auth()->user()->can('employee.update'), 403);

        $validated = $request->validate([
            'employee_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('employees', 'employee_code')->ignore($employee->id),
            ],
            'citizen_id' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('employees', 'citizen_id')->ignore($employee->id),
            ],
            'prefix' => ['nullable', 'string', 'max:50'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'gender' => ['nullable', 'string', 'max:20'],
            'birth_date' => ['nullable', 'date'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'position_id' => ['nullable', 'exists:positions,id'],
            'employment_type' => ['nullable', 'string', 'max:100'],
            'start_work_date' => ['nullable', 'date'],
            'status' => ['required', 'string', 'max:50'],
        ]);

        $employee->update($validated);

        return redirect()
            ->route('employees.index')
            ->with('success', 'แก้ไขบุคลากรเรียบร้อยแล้ว');
    }

    public function destroy(Employee $employee)
    {
        abort_unless(auth()->user()->can('employee.delete'), 403);

        if ($employee->user()->exists()) {
            return redirect()
                ->route('employees.index')
                ->with('error', 'ไม่สามารถลบได้ เพราะบุคลากรนี้มีบัญชีผู้ใช้งานอยู่');
        }

        $employee->delete();

        return redirect()
            ->route('employees.index')
            ->with('success', 'ลบบุคลากรเรียบร้อยแล้ว');
    }
}
