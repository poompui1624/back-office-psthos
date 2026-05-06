<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->can('department.view'), 403);

        $search = $request->string('search')->toString();

        $departments = Department::query()
            ->with('parent')
            ->withCount('employees')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('type', 'like', "%{$search}%");
                });
            })
            ->orderBy('code')
            ->paginate(20)
            ->withQueryString();

        return view('departments.index', compact('departments', 'search'));
    }

    public function create()
    {
        abort_unless(auth()->user()->can('department.create'), 403);

        $parents = Department::query()
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        return view('departments.create', compact('parents'));
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->can('department.create'), 403);

        $validated = $request->validate([
            'parent_id' => ['nullable', 'exists:departments,id'],
            'code' => ['required', 'string', 'max:50', 'unique:departments,code'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        Department::create($validated);

        return redirect()
            ->route('departments.index')
            ->with('success', 'เพิ่มหน่วยงานเรียบร้อยแล้ว');
    }

    public function edit(Department $department)
    {
        abort_unless(auth()->user()->can('department.update'), 403);

        $parents = Department::query()
            ->where('id', '!=', $department->id)
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        return view('departments.edit', compact('department', 'parents'));
    }

    public function update(Request $request, Department $department)
    {
        abort_unless(auth()->user()->can('department.update'), 403);

        $validated = $request->validate([
            'parent_id' => ['nullable', 'exists:departments,id'],
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('departments', 'code')->ignore($department->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $department->update($validated);

        return redirect()
            ->route('departments.index')
            ->with('success', 'แก้ไขหน่วยงานเรียบร้อยแล้ว');
    }

    public function destroy(Department $department)
    {
        abort_unless(auth()->user()->can('department.delete'), 403);

        if ($department->employees()->exists()) {
            return redirect()
                ->route('departments.index')
                ->with('error', 'ไม่สามารถลบได้ เพราะมีบุคลากรอยู่ในหน่วยงานนี้');
        }

        if ($department->children()->exists()) {
            return redirect()
                ->route('departments.index')
                ->with('error', 'ไม่สามารถลบได้ เพราะมีหน่วยงานย่อยอยู่');
        }

        $department->delete();

        return redirect()
            ->route('departments.index')
            ->with('success', 'ลบหน่วยงานเรียบร้อยแล้ว');
    }
}
