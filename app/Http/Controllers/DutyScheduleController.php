<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\DutySchedule;
use App\Models\Employee;
use App\Models\ShiftType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\CarbonPeriod;

class DutyScheduleController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->can('duty.view'), 403);

        $search = $request->string('search')->toString();
        $dateFrom = $request->string('date_from')->toString();
        $dateTo = $request->string('date_to')->toString();
        $departmentId = $request->string('department_id')->toString();
        $roleGroup = $request->string('role_group')->toString();

        $perPage = (int) $request->input('per_page', 25);

        if (! in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 25;
        }

        $schedules = DutySchedule::query()
            ->with(['employee', 'department', 'shiftType', 'assignedBy'])
            ->when($search, function ($query) use ($search) {
                $query->whereHas('employee', function ($employeeQuery) use ($search) {
                    $employeeQuery->where('employee_code', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                });
            })
            ->when($dateFrom, function ($query) use ($dateFrom) {
                $query->whereDate('work_date', '>=', $dateFrom);
            })
            ->when($dateTo, function ($query) use ($dateTo) {
                $query->whereDate('work_date', '<=', $dateTo);
            })
            ->when($departmentId, function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            })
            ->when($roleGroup, function ($query) use ($roleGroup) {
                $query->where('role_group', $roleGroup);
            })
            ->orderBy('work_date')
            ->orderBy('start_at')
            ->paginate($perPage)
            ->withQueryString();

        return view('duty-schedules.index', [
            'schedules' => $schedules,
            'search' => $search,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'departmentId' => $departmentId,
            'roleGroup' => $roleGroup,
            'perPage' => $perPage,
            'departments' => Department::where('is_active', true)->orderBy('code')->get(),
        ]);
    }

    public function create()
    {
        abort_unless(auth()->user()->can('duty.create'), 403);

        return view('duty-schedules.create', $this->formData());
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->can('duty.create'), 403);

        $validated = $this->validateDutySchedule($request);

        DB::transaction(function () use ($validated) {
            $employee = Employee::findOrFail($validated['employee_id']);
            $shiftType = ShiftType::findOrFail($validated['shift_type_id']);

            [$startAt, $endAt] = $this->buildDateTimes(
                $validated['work_date'],
                $shiftType
            );

            $schedule = DutySchedule::create([
                'employee_id' => $employee->id,
                'department_id' => $validated['department_id'] ?? $employee->department_id,
                'shift_type_id' => $shiftType->id,
                'work_date' => $validated['work_date'],
                'start_at' => $startAt,
                'end_at' => $endAt,
                'role_group' => $validated['role_group'] ?? null,
                'status' => $validated['status'],
                'assigned_by' => auth()->id(),
                'remark' => $validated['remark'] ?? null,
            ]);

            $schedule->actions()->create([
                'user_id' => auth()->id(),
                'action' => 'created',
                'new_values' => $schedule->toArray(),
                'remark' => 'สร้างตารางเวร',
            ]);
        });

        return redirect()
            ->route('duty-schedules.index')
            ->with('success', 'บันทึกตารางเวรเรียบร้อยแล้ว');
    }

    public function edit(DutySchedule $dutySchedule)
    {
        abort_unless(auth()->user()->can('duty.update'), 403);

        return view('duty-schedules.edit', array_merge(
            $this->formData(),
            compact('dutySchedule')
        ));
    }

    public function update(Request $request, DutySchedule $dutySchedule)
    {
        abort_unless(auth()->user()->can('duty.update'), 403);

        $validated = $this->validateDutySchedule($request);

        DB::transaction(function () use ($validated, $dutySchedule) {
            $oldValues = $dutySchedule->toArray();

            $employee = Employee::findOrFail($validated['employee_id']);
            $shiftType = ShiftType::findOrFail($validated['shift_type_id']);

            [$startAt, $endAt] = $this->buildDateTimes(
                $validated['work_date'],
                $shiftType
            );

            $dutySchedule->update([
                'employee_id' => $employee->id,
                'department_id' => $validated['department_id'] ?? $employee->department_id,
                'shift_type_id' => $shiftType->id,
                'work_date' => $validated['work_date'],
                'start_at' => $startAt,
                'end_at' => $endAt,
                'role_group' => $validated['role_group'] ?? null,
                'status' => $validated['status'],
                'remark' => $validated['remark'] ?? null,
            ]);

            $dutySchedule->actions()->create([
                'user_id' => auth()->id(),
                'action' => 'updated',
                'old_values' => $oldValues,
                'new_values' => $dutySchedule->fresh()->toArray(),
                'remark' => 'แก้ไขตารางเวร',
            ]);
        });

        return redirect()
            ->route('duty-schedules.index')
            ->with('success', 'แก้ไขตารางเวรเรียบร้อยแล้ว');
    }

    public function destroy(DutySchedule $dutySchedule)
    {
        abort_unless(auth()->user()->can('duty.delete'), 403);

        $dutySchedule->actions()->create([
            'user_id' => auth()->id(),
            'action' => 'deleted',
            'old_values' => $dutySchedule->toArray(),
            'remark' => 'ลบตารางเวร',
        ]);

        $dutySchedule->delete();

        return redirect()
            ->route('duty-schedules.index')
            ->with('success', 'ลบตารางเวรเรียบร้อยแล้ว');
    }

    private function formData(): array
    {
        return [
            'employees' => Employee::where('status', 'active')
                ->orderBy('employee_code')
                ->get(),

            'departments' => Department::where('is_active', true)
                ->orderBy('code')
                ->get(),

            'shiftTypes' => ShiftType::where('is_active', true)
                ->orderBy('start_time')
                ->get(),
        ];
    }

    private function validateDutySchedule(Request $request): array
    {
        return $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'shift_type_id' => ['required', 'exists:shift_types,id'],
            'work_date' => ['required', 'date'],
            'role_group' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'string', 'max:50'],
            'remark' => ['nullable', 'string'],
        ]);
    }

    private function buildDateTimes(string $workDate, ShiftType $shiftType): array
    {
        $startAt = Carbon::parse($workDate . ' ' . $shiftType->start_time);
        $endAt = Carbon::parse($workDate . ' ' . $shiftType->end_time);

        if ($shiftType->crosses_midnight || $endAt->lessThanOrEqualTo($startAt)) {
            $endAt->addDay();
        }

        return [$startAt, $endAt];
    }

    public function calendar(Request $request)
    {
        abort_unless(auth()->user()->can('duty.view'), 403);

        $year = (int) $request->input('year', now()->year);
        $month = (int) $request->input('month', now()->month);
        $departmentId = $request->string('department_id')->toString();
        $roleGroup = $request->string('role_group')->toString();

        if ($month < 1 || $month > 12) {
            $month = now()->month;
        }

        $currentMonth = Carbon::create($year, $month, 1)->startOfMonth();

        $startOfMonth = $currentMonth->copy()->startOfMonth();
        $endOfMonth = $currentMonth->copy()->endOfMonth();

        $calendarStart = $startOfMonth->copy()->startOfWeek(Carbon::SUNDAY);
        $calendarEnd = $endOfMonth->copy()->endOfWeek(Carbon::SATURDAY);

        $schedules = DutySchedule::query()
            ->with([
                'employee',
                'department',
                'shiftType',
            ])
            ->when($departmentId, function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            })
            ->when($roleGroup, function ($query) use ($roleGroup) {
                $query->where('role_group', 'like', "%{$roleGroup}%");
            })
            ->whereDate('work_date', '>=', $calendarStart->toDateString())
            ->whereDate('work_date', '<=', $calendarEnd->toDateString())
            ->orderBy('work_date')
            ->orderBy('start_at')
            ->get()
            ->groupBy(function ($schedule) {
                return $schedule->work_date->format('Y-m-d');
            });

        $days = [];

        foreach (CarbonPeriod::create($calendarStart, $calendarEnd) as $date) {
            $dateKey = $date->format('Y-m-d');

            $days[] = [
                'date' => $date->copy(),
                'date_key' => $dateKey,
                'is_current_month' => $date->month === $currentMonth->month,
                'is_today' => $date->isToday(),
                'schedules' => $schedules->get($dateKey, collect()),
            ];
        }

        $previousMonth = $currentMonth->copy()->subMonth();
        $nextMonth = $currentMonth->copy()->addMonth();

        return view('duty-schedules.calendar', [
            'days' => $days,
            'currentMonth' => $currentMonth,
            'previousMonth' => $previousMonth,
            'nextMonth' => $nextMonth,
            'departmentId' => $departmentId,
            'roleGroup' => $roleGroup,
            'departments' => Department::where('is_active', true)
                ->orderBy('code')
                ->get(),
        ]);
    }

    public function bulkCreate()
    {
        abort_unless(auth()->user()->can('duty.create'), 403);

        return view('duty-schedules.bulk-create', $this->formData());
    }

    public function bulkStore(Request $request)
    {
        abort_unless(auth()->user()->can('duty.create'), 403);

        $validated = $request->validate([
            'employee_ids' => ['required', 'array', 'min:1'],
            'employee_ids.*' => ['required', 'exists:employees,id'],

            'department_id' => ['nullable', 'exists:departments,id'],
            'shift_type_id' => ['required', 'exists:shift_types,id'],

            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date', 'after_or_equal:date_from'],

            'weekdays' => ['required', 'array', 'min:1'],
            'weekdays.*' => ['required', 'integer', 'between:0,6'],

            'role_group' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'string', 'max:50'],
            'remark' => ['nullable', 'string'],
            'overwrite' => ['nullable', 'boolean'],
        ]);

        $shiftType = ShiftType::findOrFail($validated['shift_type_id']);

        $dateFrom = Carbon::parse($validated['date_from'])->startOfDay();
        $dateTo = Carbon::parse($validated['date_to'])->startOfDay();

        $weekdays = collect($validated['weekdays'])
            ->map(fn ($day) => (int) $day)
            ->values()
            ->all();

        $created = 0;
        $updated = 0;
        $skipped = 0;

        DB::transaction(function () use (
            $validated,
            $shiftType,
            $dateFrom,
            $dateTo,
            $weekdays,
            &$created,
            &$updated,
            &$skipped
        ) {
            $employees = Employee::whereIn('id', $validated['employee_ids'])->get();

            foreach (CarbonPeriod::create($dateFrom, $dateTo) as $date) {
                if (! in_array($date->dayOfWeek, $weekdays, true)) {
                    continue;
                }

                foreach ($employees as $employee) {
                    [$startAt, $endAt] = $this->buildDateTimes(
                        $date->format('Y-m-d'),
                        $shiftType
                    );

                    $existing = DutySchedule::query()
                        ->where('employee_id', $employee->id)
                        ->whereDate('work_date', $date->format('Y-m-d'))
                        ->where('shift_type_id', $shiftType->id)
                        ->first();

                    $overwrite = (bool) ($validated['overwrite'] ?? false);

                        if ($existing && ! $overwrite) {
                            $skipped++;
                            continue;
                        }

                    $data = [
                        'employee_id' => $employee->id,
                        'department_id' => $validated['department_id'] ?? $employee->department_id,
                        'shift_type_id' => $shiftType->id,
                        'work_date' => $date->format('Y-m-d'),
                        'start_at' => $startAt,
                        'end_at' => $endAt,
                        'role_group' => $validated['role_group'] ?? null,
                        'status' => $validated['status'],
                        'assigned_by' => auth()->id(),
                        'remark' => $validated['remark'] ?? null,
                    ];

                    if ($existing) {
                        $oldValues = $existing->toArray();

                        $existing->update($data);

                        $existing->actions()->create([
                            'user_id' => auth()->id(),
                            'action' => 'bulk_updated',
                            'old_values' => $oldValues,
                            'new_values' => $existing->fresh()->toArray(),
                            'remark' => 'อัปเดตจากการสร้างตารางเวรหลายรายการ',
                        ]);

                        $updated++;
                        continue;
                    }

                    $schedule = DutySchedule::create($data);

                    $schedule->actions()->create([
                        'user_id' => auth()->id(),
                        'action' => 'bulk_created',
                        'new_values' => $schedule->toArray(),
                        'remark' => 'สร้างจากระบบสร้างตารางเวรหลายรายการ',
                    ]);

                    $created++;
                }
            }
        });

        return redirect()
            ->route('duty-schedules.index')
            ->with('success', "สร้างตารางเวรสำเร็จ เพิ่มใหม่ {$created} รายการ, อัปเดต {$updated} รายการ, ข้าม {$skipped} รายการ");
    }
}