<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->can('asset.view'), 403);

        $search = $request->string('search')->toString();
        $status = $request->string('status')->toString();

        $assets = Asset::query()
            ->visibleTo(auth()->user())
            ->with(['category', 'department', 'responsibleEmployee'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('asset_code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('brand', 'like', "%{$search}%")
                        ->orWhere('model', 'like', "%{$search}%")
                        ->orWhere('serial_number', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%")
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
            ->orderBy('asset_code')
            ->paginate(20)
            ->withQueryString();

        return view('assets.index', compact('assets', 'search', 'status'));
    }

    public function create()
    {
        abort_unless(auth()->user()->can('asset.create'), 403);

        return view('assets.create', $this->formData());
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->can('asset.create'), 403);

        $validated = $this->validateAsset($request);

        Asset::create($validated);

        return redirect()
            ->route('assets.index')
            ->with('success', 'เพิ่มพัสดุเรียบร้อยแล้ว');
    }

    public function edit(Asset $asset)
    {
        abort_unless(auth()->user()->can('asset.update'), 403);

        $attachments = $asset->attachments()
            ->with('uploader')
            ->latest()
            ->get();

        return view('assets.edit', array_merge(
            $this->formData(),
            compact('asset', 'attachments')
        ));
    }

    public function update(Request $request, Asset $asset)
    {
        abort_unless(auth()->user()->can('asset.update'), 403);

        $validated = $this->validateAsset($request, $asset);

        $asset->update($validated);

        return redirect()
            ->route('assets.index')
            ->with('success', 'แก้ไขพัสดุเรียบร้อยแล้ว');
    }

    public function destroy(Asset $asset)
    {
        abort_unless(auth()->user()->can('asset.delete'), 403);

        $asset->delete();

        return redirect()
            ->route('assets.index')
            ->with('success', 'ลบพัสดุเรียบร้อยแล้ว');
    }

    private function formData(): array
    {
        return [
            'categories' => AssetCategory::query()
                ->where('is_active', true)
                ->orderBy('code')
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

    private function validateAsset(Request $request, ?Asset $asset = null): array
    {
        return $request->validate([
            'asset_code' => [
                'required',
                'string',
                'max:100',
                Rule::unique('assets', 'asset_code')->ignore($asset?->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'asset_category_id' => ['nullable', 'exists:asset_categories,id'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'responsible_employee_id' => ['nullable', 'exists:employees,id'],
            'brand' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'serial_number' => ['nullable', 'string', 'max:255'],
            'received_date' => ['nullable', 'date'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
            'budget_source' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', 'max:50'],
            'remark' => ['nullable', 'string'],
        ]);
    }
}
