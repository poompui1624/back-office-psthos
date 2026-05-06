<?php

namespace App\Http\Controllers;

use App\Models\AttendanceDailySummary;
use App\Models\AttendanceLog;
use App\Models\DutySchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceSummaryController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->can('attendance.view'), 403);

        $search = $request->string('search')->toString();
        $dateFrom = $request->string('date_from')->toString();
        $dateTo = $request->string('date_to')->toString();
        $status = $request->string('status')->toString();

        $summaries = AttendanceDailySummary::query()
            ->with(['employee.department', 'dutySchedule.shiftType'])
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
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->latest('work_date')
            ->paginate(50)
            ->withQueryString();

        return view('attendance-summaries.index', compact(
            'summaries',
            'search',
            'dateFrom',
            'dateTo',
            'status'
        ));
    }

    public function generateForm()
    {
        abort_unless(auth()->user()->can('attendance.import'), 403);

        return view('attendance-summaries.generate');
    }

    public function generate(Request $request)
    {
        abort_unless(auth()->user()->can('attendance.import'), 403);

        $validated = $request->validate([
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date', 'after_or_equal:date_from'],
            'expected_in_time' => ['required', 'date_format:H:i'],
            'expected_out_time' => ['required', 'date_format:H:i'],
            'use_duty_schedule' => ['nullable', 'boolean'],
        ]);

        if ($request->boolean('use_duty_schedule')) {
            return $this->generateFromDutySchedules($validated);
        }

        return $this->generateFromFixedTime($validated);
    }

    private function generateFromFixedTime(array $validated)
    {
        $dateFrom = Carbon::parse($validated['date_from'])->toDateString();
        $dateTo = Carbon::parse($validated['date_to'])->toDateString();

        $expectedInTime = $validated['expected_in_time'];
        $expectedOutTime = $validated['expected_out_time'];

        $created = 0;
        $updated = 0;
        $skipped = 0;

        $groups = AttendanceLog::query()
            ->with('employee')
            ->whereNotNull('employee_id')
            ->whereDate('scan_date', '>=', $dateFrom)
            ->whereDate('scan_date', '<=', $dateTo)
            ->orderBy('employee_id')
            ->orderBy('scan_date')
            ->orderBy('scan_time')
            ->get()
            ->groupBy(function ($log) {
                return $log->employee_id . '|' . $log->scan_date->format('Y-m-d');
            });

        DB::transaction(function () use (
            $groups,
            $expectedInTime,
            $expectedOutTime,
            &$created,
            &$updated,
            &$skipped
        ) {
            foreach ($groups as $groupKey => $logs) {
                [$employeeId, $workDate] = explode('|', $groupKey);

                $logs = $logs->sortBy('scan_time')->values();

                $firstLog = $logs->first();
                $lastLog = $logs->last();

                if (! $firstLog) {
                    $skipped++;
                    continue;
                }

                $firstInAt = $firstLog->scan_time;
                $lastOutAt = $logs->count() > 1 ? $lastLog->scan_time : null;

                $expectedInAt = Carbon::parse($workDate . ' ' . $expectedInTime);
                $expectedOutAt = Carbon::parse($workDate . ' ' . $expectedOutTime);

                $workMinutes = 0;
                $lateMinutes = 0;
                $earlyLeaveMinutes = 0;
                $status = 'normal';
                $remark = null;

                if ($lastOutAt) {
                    $workMinutes = max(0, $firstInAt->diffInMinutes($lastOutAt));
                } else {
                    $status = 'incomplete';
                    $remark = 'มีข้อมูลสแกนเพียงครั้งเดียว';
                }

                if ($firstInAt->gt($expectedInAt)) {
                    $lateMinutes = $expectedInAt->diffInMinutes($firstInAt);
                }

                if ($lastOutAt && $lastOutAt->lt($expectedOutAt)) {
                    $earlyLeaveMinutes = $lastOutAt->diffInMinutes($expectedOutAt);
                }

                if ($status !== 'incomplete') {
                    if ($lateMinutes > 0 && $earlyLeaveMinutes > 0) {
                        $status = 'late_and_early_leave';
                    } elseif ($lateMinutes > 0) {
                        $status = 'late';
                    } elseif ($earlyLeaveMinutes > 0) {
                        $status = 'early_leave';
                    } else {
                        $status = 'normal';
                    }
                }

                $summary = AttendanceDailySummary::updateOrCreate(
                    [
                        'employee_id' => (int) $employeeId,
                        'work_date' => $workDate,
                    ],
                    [
                        'duty_schedule_id' => null,
                        'first_in_at' => $firstInAt,
                        'last_out_at' => $lastOutAt,
                        'expected_in_time' => $expectedInTime,
                        'expected_out_time' => $expectedOutTime,
                        'work_minutes' => $workMinutes,
                        'late_minutes' => $lateMinutes,
                        'early_leave_minutes' => $earlyLeaveMinutes,
                        'status' => $status,
                        'remark' => $remark,
                        'generated_at' => now(),
                    ]
                );

                if ($summary->wasRecentlyCreated) {
                    $created++;
                } else {
                    $updated++;
                }
            }
        });

        return redirect()
            ->route('attendance-summaries.index')
            ->with('success', "สร้างสรุปเวลาทำงานแบบเวลาเข้าออกคงที่สำเร็จ เพิ่มใหม่ {$created} รายการ, อัปเดต {$updated} รายการ, ข้าม {$skipped} รายการ");
    }

    private function generateFromDutySchedules(array $validated)
    {
        $dateFrom = Carbon::parse($validated['date_from'])->toDateString();
        $dateTo = Carbon::parse($validated['date_to'])->toDateString();

        $created = 0;
        $updated = 0;
        $skipped = 0;

        $schedules = DutySchedule::query()
            ->with(['employee', 'shiftType'])
            ->whereDate('work_date', '>=', $dateFrom)
            ->whereDate('work_date', '<=', $dateTo)
            ->where('status', '!=', 'cancelled')
            ->orderBy('work_date')
            ->orderBy('employee_id')
            ->get();

        DB::transaction(function () use (
            $schedules,
            &$created,
            &$updated,
            &$skipped
        ) {
            foreach ($schedules as $schedule) {
                if (! $schedule->employee || ! $schedule->shiftType) {
                    $skipped++;
                    continue;
                }

                if ($schedule->shiftType->code === 'OFF') {
                    $summary = AttendanceDailySummary::updateOrCreate(
                        [
                            'employee_id' => $schedule->employee_id,
                            'work_date' => $schedule->work_date->format('Y-m-d'),
                        ],
                        [
                            'duty_schedule_id' => $schedule->id,
                            'first_in_at' => null,
                            'last_out_at' => null,
                            'expected_in_time' => $schedule->start_at->format('H:i:s'),
                            'expected_out_time' => $schedule->end_at->format('H:i:s'),
                            'work_minutes' => 0,
                            'late_minutes' => 0,
                            'early_leave_minutes' => 0,
                            'status' => 'off',
                            'remark' => 'วันหยุดตามตารางเวร',
                            'generated_at' => now(),
                        ]
                    );

                    if ($summary->wasRecentlyCreated) {
                        $created++;
                    } else {
                        $updated++;
                    }

                    continue;
                }

                $scanStart = $schedule->start_at->copy()->subHours(4);
                $scanEnd = $schedule->end_at->copy()->addHours(6);

                $logs = AttendanceLog::query()
                    ->where('employee_id', $schedule->employee_id)
                    ->whereBetween('scan_time', [$scanStart, $scanEnd])
                    ->orderBy('scan_time')
                    ->get();

                $firstLog = $logs->first();
                $lastLog = $logs->last();

                $firstInAt = $firstLog?->scan_time;
                $lastOutAt = $logs->count() > 1 ? $lastLog->scan_time : null;

                $workMinutes = 0;
                $lateMinutes = 0;
                $earlyLeaveMinutes = 0;
                $status = 'normal';
                $remark = null;

                if ($logs->count() === 0) {
                    $status = 'absent';
                    $remark = 'ไม่พบข้อมูลสแกนในช่วงเวร';
                } elseif ($logs->count() === 1) {
                    $status = 'incomplete';
                    $remark = 'มีข้อมูลสแกนเพียงครั้งเดียว';
                }

                if ($firstInAt && $lastOutAt) {
                    $workMinutes = max(0, $firstInAt->diffInMinutes($lastOutAt));

                    if ($firstInAt->gt($schedule->start_at)) {
                        $lateMinutes = $schedule->start_at->diffInMinutes($firstInAt);
                    }

                    if ($lastOutAt->lt($schedule->end_at)) {
                        $earlyLeaveMinutes = $lastOutAt->diffInMinutes($schedule->end_at);
                    }

                    if ($lateMinutes > 0 && $earlyLeaveMinutes > 0) {
                        $status = 'late_and_early_leave';
                    } elseif ($lateMinutes > 0) {
                        $status = 'late';
                    } elseif ($earlyLeaveMinutes > 0) {
                        $status = 'early_leave';
                    } else {
                        $status = 'normal';
                    }
                }

                $summary = AttendanceDailySummary::updateOrCreate(
                    [
                        'employee_id' => $schedule->employee_id,
                        'work_date' => $schedule->work_date->format('Y-m-d'),
                    ],
                    [
                        'duty_schedule_id' => $schedule->id,
                        'first_in_at' => $firstInAt,
                        'last_out_at' => $lastOutAt,
                        'expected_in_time' => $schedule->start_at->format('H:i:s'),
                        'expected_out_time' => $schedule->end_at->format('H:i:s'),
                        'work_minutes' => $workMinutes,
                        'late_minutes' => $lateMinutes,
                        'early_leave_minutes' => $earlyLeaveMinutes,
                        'status' => $status,
                        'remark' => $remark,
                        'generated_at' => now(),
                    ]
                );

                if ($summary->wasRecentlyCreated) {
                    $created++;
                } else {
                    $updated++;
                }
            }
        });

        return redirect()
            ->route('attendance-summaries.index')
            ->with('success', "สร้างสรุปจากตารางเวรสำเร็จ เพิ่มใหม่ {$created} รายการ, อัปเดต {$updated} รายการ, ข้าม {$skipped} รายการ");
    }

    public function dashboard(Request $request)
    {
        abort_unless(auth()->user()->can('attendance.view'), 403);

        $year = (int) $request->input('year', now()->year);
        $month = (int) $request->input('month', now()->month);

        if ($month < 1 || $month > 12) {
            $month = now()->month;
        }

        $currentMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $startOfMonth = $currentMonth->copy()->startOfMonth()->toDateString();
        $endOfMonth = $currentMonth->copy()->endOfMonth()->toDateString();

        $baseQuery = AttendanceDailySummary::query()
            ->whereDate('work_date', '>=', $startOfMonth)
            ->whereDate('work_date', '<=', $endOfMonth);

        $summary = [
            'total' => (clone $baseQuery)->count(),
            'normal' => (clone $baseQuery)->where('status', 'normal')->count(),
            'late' => (clone $baseQuery)->where('status', 'late')->count(),
            'early_leave' => (clone $baseQuery)->where('status', 'early_leave')->count(),
            'late_and_early_leave' => (clone $baseQuery)->where('status', 'late_and_early_leave')->count(),
            'incomplete' => (clone $baseQuery)->where('status', 'incomplete')->count(),
            'absent' => (clone $baseQuery)->where('status', 'absent')->count(),
            'off' => (clone $baseQuery)->where('status', 'off')->count(),
            'late_minutes' => (clone $baseQuery)->sum('late_minutes'),
            'early_leave_minutes' => (clone $baseQuery)->sum('early_leave_minutes'),
        ];

        $problemSummaries = AttendanceDailySummary::query()
            ->with(['employee.department', 'dutySchedule.shiftType'])
            ->whereDate('work_date', '>=', $startOfMonth)
            ->whereDate('work_date', '<=', $endOfMonth)
            ->whereIn('status', [
                'late',
                'early_leave',
                'late_and_early_leave',
                'incomplete',
                'absent',
            ])
            ->latest('work_date')
            ->limit(20)
            ->get();

        $topLateEmployees = AttendanceDailySummary::query()
            ->selectRaw('employee_id, COUNT(*) as late_count, SUM(late_minutes) as total_late_minutes')
            ->with('employee.department')
            ->whereDate('work_date', '>=', $startOfMonth)
            ->whereDate('work_date', '<=', $endOfMonth)
            ->whereIn('status', ['late', 'late_and_early_leave'])
            ->groupBy('employee_id')
            ->orderByDesc('late_count')
            ->orderByDesc('total_late_minutes')
            ->limit(10)
            ->get();

        $previousMonth = $currentMonth->copy()->subMonth();
        $nextMonth = $currentMonth->copy()->addMonth();

        return view('attendance-summaries.dashboard', compact(
            'summary',
            'problemSummaries',
            'topLateEmployees',
            'currentMonth',
            'previousMonth',
            'nextMonth'
        ));
    }

    public function print(Request $request)
    {
        abort_unless(auth()->user()->can('attendance.view'), 403);

        $search = $request->string('search')->toString();
        $dateFrom = $request->string('date_from')->toString();
        $dateTo = $request->string('date_to')->toString();
        $status = $request->string('status')->toString();

        $summaries = AttendanceDailySummary::query()
            ->with(['employee.department', 'dutySchedule.shiftType'])
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
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->orderBy('work_date')
            ->orderBy('employee_id')
            ->get();

        return view('attendance-summaries.print', compact(
            'summaries',
            'search',
            'dateFrom',
            'dateTo',
            'status'
        ));
    }
}
