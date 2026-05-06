<?php

namespace App\Http\Controllers;

use App\Models\LeaveType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LeaveTypeController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->can('leave.view'), 403);

        $search = $request->string('search')->toString();

        $leaveTypes = LeaveType::query()
            ->withCount('leaveRequests')
            ->when($search, function ($query) use ($search) {
                $query->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            })
            ->orderBy('code')
            ->paginate(20)
            ->withQueryString();

        return view('leave-types.index', compact('leaveTypes', 'search'));
    }

    public function create()
    {
        abort_unless(auth()->user()->can('leave.create'), 403);

        return view('leave-types.create');
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->can('leave.create'), 403);

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:leave_types,code'],
            'name' => ['required', 'string', 'max:255'],
            'default_days_per_year' => ['nullable', 'numeric', 'min:0'],
            'requires_document' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'description' => ['nullable', 'string'],
        ]);

        $validated['requires_document'] = $request->boolean('requires_document');
        $validated['is_active'] = $request->boolean('is_active');

        LeaveType::create($validated);

        return redirect()
            ->route('leave-types.index')
            ->with('success', 'เพิ่มประเภทการลาเรียบร้อยแล้ว');
    }

    public function edit(LeaveType $leaveType)
    {
        abort_unless(auth()->user()->can('leave.update'), 403);

        return view('leave-types.edit', compact('leaveType'));
    }

    public function update(Request $request, LeaveType $leaveType)
    {
        abort_unless(auth()->user()->can('leave.update'), 403);

        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('leave_types', 'code')->ignore($leaveType->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'default_days_per_year' => ['nullable', 'numeric', 'min:0'],
            'requires_document' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'description' => ['nullable', 'string'],
        ]);

        $validated['requires_document'] = $request->boolean('requires_document');
        $validated['is_active'] = $request->boolean('is_active');

        $leaveType->update($validated);

        return redirect()
            ->route('leave-types.index')
            ->with('success', 'แก้ไขประเภทการลาเรียบร้อยแล้ว');
    }

    public function destroy(LeaveType $leaveType)
    {
        abort_unless(auth()->user()->can('leave.delete'), 403);

        if ($leaveType->leaveRequests()->exists()) {
            return redirect()
                ->route('leave-types.index')
                ->with('error', 'ไม่สามารถลบได้ เพราะมีคำขอลาใช้ประเภทนี้อยู่');
        }

        $leaveType->delete();

        return redirect()
            ->route('leave-types.index')
            ->with('success', 'ลบประเภทการลาเรียบร้อยแล้ว');
    }
}
