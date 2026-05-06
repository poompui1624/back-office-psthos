<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetMovement;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssetMovementController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->can('asset.view'), 403);

        $search = $request->string('search')->toString();

        $movements = AssetMovement::query()
            ->with([
                'asset',
                'fromDepartment',
                'toDepartment',
                'fromEmployee',
                'toEmployee',
                'movedBy',
            ])
            ->when($search, function ($query) use ($search) {
                $query->whereHas('asset', function ($assetQuery) use ($search) {
                    $assetQuery->where('asset_code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                })
                ->orWhereHas('fromDepartment', function ($departmentQuery) use ($search) {
                    $departmentQuery->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('toDepartment', function ($departmentQuery) use ($search) {
                    $departmentQuery->where('name', 'like', "%{$search}%");
                })
                ->orWhere('reason', 'like', "%{$search}%");
            })
            ->latest('moved_at')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('asset-movements.index', compact('movements', 'search'));
    }

    public function create(Request $request)
    {
        abort_unless(auth()->user()->can('asset.movement'), 403);

        $assets = Asset::query()
            ->with(['department', 'responsibleEmployee'])
            ->orderBy('asset_code')
            ->get();

        $departments = Department::query()
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        $employees = Employee::query()
            ->where('status', 'active')
            ->orderBy('employee_code')
            ->get();

        $selectedAsset = null;

        if ($request->filled('asset_id')) {
            $selectedAsset = Asset::with(['department', 'responsibleEmployee'])
                ->find($request->integer('asset_id'));
        }

        return view('asset-movements.create', compact(
            'assets',
            'departments',
            'employees',
            'selectedAsset'
        ));
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->can('asset.movement'), 403);

        $validated = $request->validate([
            'asset_id' => ['required', 'exists:assets,id'],
            'to_department_id' => ['nullable', 'exists:departments,id'],
            'to_employee_id' => ['nullable', 'exists:employees,id'],
            'moved_at' => ['required', 'date'],
            'reason' => ['nullable', 'string', 'max:255'],
            'remark' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($validated) {
            $asset = Asset::lockForUpdate()->findOrFail($validated['asset_id']);

            AssetMovement::create([
                'asset_id' => $asset->id,
                'from_department_id' => $asset->department_id,
                'to_department_id' => $validated['to_department_id'] ?? null,
                'from_employee_id' => $asset->responsible_employee_id,
                'to_employee_id' => $validated['to_employee_id'] ?? null,
                'moved_by' => auth()->id(),
                'moved_at' => $validated['moved_at'],
                'reason' => $validated['reason'] ?? null,
                'remark' => $validated['remark'] ?? null,
            ]);

            $asset->update([
                'department_id' => $validated['to_department_id'] ?? null,
                'responsible_employee_id' => $validated['to_employee_id'] ?? null,
            ]);
        });

        return redirect()
            ->route('asset-movements.index')
            ->with('success', 'บันทึกการโอนย้ายพัสดุเรียบร้อยแล้ว');
    }
}
