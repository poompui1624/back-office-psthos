<?php

namespace App\Http\Controllers;

use App\Models\ShiftType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ShiftTypeController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->can('duty.view'), 403);

        $search = $request->string('search')->toString();

        $shiftTypes = ShiftType::query()
            ->withCount('dutySchedules')
            ->when($search, function ($query) use ($search) {
                $query->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            })
            ->orderBy('start_time')
            ->paginate(20)
            ->withQueryString();

        return view('shift-types.index', compact('shiftTypes', 'search'));
    }

    public function create()
    {
        abort_unless(auth()->user()->can('duty.create'), 403);

        return view('shift-types.create');
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->can('duty.create'), 403);

        $validated = $this->validateShiftType($request);

        ShiftType::create($validated);

        return redirect()
            ->route('shift-types.index')
            ->with('success', 'เพิ่มประเภทเวรเรียบร้อยแล้ว');
    }

    public function edit(ShiftType $shiftType)
    {
        abort_unless(auth()->user()->can('duty.update'), 403);

        return view('shift-types.edit', compact('shiftType'));
    }

    public function update(Request $request, ShiftType $shiftType)
    {
        abort_unless(auth()->user()->can('duty.update'), 403);

        $validated = $this->validateShiftType($request, $shiftType);

        $shiftType->update($validated);

        return redirect()
            ->route('shift-types.index')
            ->with('success', 'แก้ไขประเภทเวรเรียบร้อยแล้ว');
    }

    public function destroy(ShiftType $shiftType)
    {
        abort_unless(auth()->user()->can('duty.delete'), 403);

        if ($shiftType->dutySchedules()->exists()) {
            return back()->with('error', 'ไม่สามารถลบได้ เพราะมีตารางเวรใช้ประเภทนี้อยู่');
        }

        $shiftType->delete();

        return redirect()
            ->route('shift-types.index')
            ->with('success', 'ลบประเภทเวรเรียบร้อยแล้ว');
    }

    private function validateShiftType(Request $request, ?ShiftType $shiftType = null): array
    {
        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('shift_types', 'code')->ignore($shiftType?->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i'],
            'crosses_midnight' => ['nullable', 'boolean'],
            'is_ot' => ['nullable', 'boolean'],
            'ot_multiplier' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'ot_flat_rate' => ['nullable', 'numeric', 'min:0'],
            'color' => ['nullable', 'string', 'max:50'],
            'is_active' => ['nullable', 'boolean'],
            'description' => ['nullable', 'string'],
        ]);

        $validated['crosses_midnight'] = $request->boolean('crosses_midnight');
        $validated['is_ot'] = $request->boolean('is_ot');
        $validated['is_active'] = $request->boolean('is_active');

        // A blank multiplier means "no scaling", not "unpaid".
        $validated['ot_multiplier'] = $validated['ot_multiplier'] ?? 1;

        // An empty flat rate must stay null so the multiplier is used instead of
        // the shift paying zero.
        $validated['ot_flat_rate'] = ($validated['ot_flat_rate'] ?? '') === '' ? null : $validated['ot_flat_rate'];

        return $validated;
    }
}
