<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Computer;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ComputerController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->can('computer.view'), 403);

        $search = $request->string('search')->toString();
        $status = $request->string('status')->toString();

        $computers = Computer::query()
            ->with(['asset', 'department', 'responsibleEmployee'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('hostname', 'like', "%{$search}%")
                        ->orWhere('ip_address', 'like', "%{$search}%")
                        ->orWhere('mac_address', 'like', "%{$search}%")
                        ->orWhere('serial_number', 'like', "%{$search}%")
                        ->orWhere('os_name', 'like', "%{$search}%")
                        ->orWhereHas('asset', function ($assetQuery) use ($search) {
                            $assetQuery->where('asset_code', 'like', "%{$search}%")
                                ->orWhere('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('department', function ($departmentQuery) use ($search) {
                            $departmentQuery->where('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('responsibleEmployee', function ($employeeQuery) use ($search) {
                            $employeeQuery->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('employee_code', 'like', "%{$search}%");
                        });
                });
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->orderBy('hostname')
            ->paginate(20)
            ->withQueryString();

        return view('computers.index', compact('computers', 'search', 'status'));
    }

    public function show(Computer $computer)
    {
        abort_unless(auth()->user()->can('computer.view'), 403);

        $computer->load([
            'asset',
            'department',
            'responsibleEmployee',
        ]);

        $snapshots = $computer->snapshots()
            ->latest('reported_at')
            ->latest()
            ->paginate(10);

        return view('computers.show', compact('computer', 'snapshots'));
    }

    public function create()
    {
        abort_unless(auth()->user()->can('computer.create'), 403);

        return view('computers.create', $this->formData());
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->can('computer.create'), 403);

        $validated = $this->validateComputer($request);

        Computer::create($validated);

        return redirect()
            ->route('computers.index')
            ->with('success', 'เพิ่มคอมพิวเตอร์เรียบร้อยแล้ว');
    }

    public function edit(Computer $computer)
    {
        abort_unless(auth()->user()->can('computer.update'), 403);

        return view('computers.edit', array_merge(
            $this->formData(),
            compact('computer')
        ));
    }

    public function update(Request $request, Computer $computer)
    {
        abort_unless(auth()->user()->can('computer.update'), 403);

        $validated = $this->validateComputer($request, $computer);

        $computer->update($validated);

        return redirect()
            ->route('computers.index')
            ->with('success', 'แก้ไขคอมพิวเตอร์เรียบร้อยแล้ว');
    }

    public function destroy(Computer $computer)
    {
        abort_unless(auth()->user()->can('computer.delete'), 403);

        $computer->delete();

        return redirect()
            ->route('computers.index')
            ->with('success', 'ลบคอมพิวเตอร์เรียบร้อยแล้ว');
    }

    private function formData(): array
    {
        return [
            'assets' => Asset::query()
                ->whereNull('deleted_at')
                ->orderBy('asset_code')
                ->get(),

            'departments' => Department::query()
                ->where('is_active', true)
                ->orderBy('code')
                ->get(),

            'employees' => Employee::query()
                ->where('status', 'active')
                ->orderBy('employee_code')
                ->get(),
        ];
    }

    private function validateComputer(Request $request, ?Computer $computer = null): array
    {
        return $request->validate([
            'asset_id' => [
                'nullable',
                'exists:assets,id',
                Rule::unique('computers', 'asset_id')->ignore($computer?->id),
            ],
            'department_id' => ['nullable', 'exists:departments,id'],
            'responsible_employee_id' => ['nullable', 'exists:employees,id'],

            'machine_uuid' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('computers', 'machine_uuid')->ignore($computer?->id),
            ],

            'hostname' => [
                'required',
                'string',
                'max:255',
                Rule::unique('computers', 'hostname')->ignore($computer?->id),
            ],

            'ip_address' => ['nullable', 'string', 'max:50'],

            'mac_address' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('computers', 'mac_address')->ignore($computer?->id),
            ],

            'manufacturer' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'serial_number' => ['nullable', 'string', 'max:255'],

            'os_name' => ['nullable', 'string', 'max:255'],
            'os_version' => ['nullable', 'string', 'max:255'],

            'cpu_name' => ['nullable', 'string', 'max:255'],
            'ram_gb' => ['nullable', 'integer', 'min:0'],
            'storage_gb' => ['nullable', 'integer', 'min:0'],

            'status' => ['required', 'string', 'max:50'],
            'remark' => ['nullable', 'string'],
        ]);
    }
}