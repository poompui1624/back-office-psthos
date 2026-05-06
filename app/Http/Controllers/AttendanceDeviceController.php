<?php

namespace App\Http\Controllers;

use App\Models\AttendanceDevice;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AttendanceDeviceController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->can('attendance.view'), 403);

        $search = $request->string('search')->toString();

        $devices = AttendanceDevice::query()
            ->withCount('logs')
            ->when($search, function ($query) use ($search) {
                $query->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%");
            })
            ->orderBy('code')
            ->paginate(20)
            ->withQueryString();

        return view('attendance-devices.index', compact('devices', 'search'));
    }

    public function create()
    {
        abort_unless(auth()->user()->can('attendance.create'), 403);

        return view('attendance-devices.create');
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->can('attendance.create'), 403);

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:100', 'unique:attendance_devices,code'],
            'name' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'ip_address' => ['nullable', 'string', 'max:100'],
            'brand' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'remark' => ['nullable', 'string'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        AttendanceDevice::create($validated);

        return redirect()
            ->route('attendance-devices.index')
            ->with('success', 'เพิ่มเครื่องสแกนนิ้วเรียบร้อยแล้ว');
    }

    public function edit(AttendanceDevice $attendanceDevice)
    {
        abort_unless(auth()->user()->can('attendance.update'), 403);

        return view('attendance-devices.edit', compact('attendanceDevice'));
    }

    public function update(Request $request, AttendanceDevice $attendanceDevice)
    {
        abort_unless(auth()->user()->can('attendance.update'), 403);

        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:100',
                Rule::unique('attendance_devices', 'code')->ignore($attendanceDevice->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'ip_address' => ['nullable', 'string', 'max:100'],
            'brand' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'remark' => ['nullable', 'string'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $attendanceDevice->update($validated);

        return redirect()
            ->route('attendance-devices.index')
            ->with('success', 'แก้ไขเครื่องสแกนนิ้วเรียบร้อยแล้ว');
    }

    public function destroy(AttendanceDevice $attendanceDevice)
    {
        abort_unless(auth()->user()->can('attendance.delete'), 403);

        if ($attendanceDevice->logs()->exists()) {
            return back()->with('error', 'ไม่สามารถลบได้ เพราะมีข้อมูลเวลาสแกนผูกอยู่');
        }

        $attendanceDevice->delete();

        return redirect()
            ->route('attendance-devices.index')
            ->with('success', 'ลบเครื่องสแกนนิ้วเรียบร้อยแล้ว');
    }
}
