<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Services\AppNotificationService;
use App\Services\DocumentNumberService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaveRequestController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->can('leave.view'), 403);

        $search = $request->string('search')->toString();
        $status = $request->string('status')->toString();

        $leaveRequests = LeaveRequest::query()
            ->visibleTo(auth()->user())
            ->with(['employee', 'department', 'leaveType', 'creator', 'approver'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('request_no', 'like', "%{$search}%")
                        ->orWhere('reason', 'like', "%{$search}%")
                        ->orWhereHas('employee', function ($employeeQuery) use ($search) {
                            $employeeQuery->where('employee_code', 'like', "%{$search}%")
                                ->orWhere('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('department', function ($departmentQuery) use ($search) {
                            $departmentQuery->where('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('leaveType', function ($typeQuery) use ($search) {
                            $typeQuery->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('leave-requests.index', compact('leaveRequests', 'search', 'status'));
    }

    public function create()
    {
        abort_unless(auth()->user()->can('leave.create'), 403);

        return view('leave-requests.create', $this->formData());
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->can('leave.create'), 403);

        $validated = $this->validateLeaveRequest($request);

        $leaveRequest = DB::transaction(function () use ($validated) {
            $employee = Employee::findOrFail($validated['employee_id']);

            $leaveRequest = LeaveRequest::create([
                'request_no' => $this->generateRequestNo(),
                'employee_id' => $employee->id,
                'department_id' => $validated['department_id'] ?? $employee->department_id,
                'leave_type_id' => $validated['leave_type_id'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'start_period' => $validated['start_period'],
                'end_period' => $validated['end_period'],
                'total_days' => $validated['total_days'],
                'reason' => $validated['reason'] ?? null,
                'contact_during_leave' => $validated['contact_during_leave'] ?? null,
                'created_by' => auth()->id(),
                'status' => 'pending',
            ]);

            $leaveRequest->actions()->create([
                'user_id' => auth()->id(),
                'action' => 'created',
                'old_status' => null,
                'new_status' => 'pending',
                'remark' => 'สร้างคำขอลา',
            ]);

            return $leaveRequest;
        });

        $this->notifyLeaveCreated($leaveRequest);

        return redirect()
            ->route('leave-requests.index')
            ->with('success', 'บันทึกคำขอลาเรียบร้อยแล้ว');
    }

    public function show(LeaveRequest $leaveRequest)
    {
        abort_unless(auth()->user()->can('leave.view'), 403);

        $leaveRequest->load([
            'employee',
            'department',
            'leaveType',
            'creator',
            'approver',
            'rejecter',
            'actions.user',
            'attachments.uploader',
        ]);

        return view('leave-requests.show', compact('leaveRequest'));
    }

    public function edit(LeaveRequest $leaveRequest)
    {
        abort_unless(auth()->user()->can('leave.update'), 403);

        if (! $leaveRequest->isPending()) {
            return redirect()
                ->route('leave-requests.show', $leaveRequest)
                ->with('error', 'แก้ไขได้เฉพาะคำขอที่ยังรออนุมัติ');
        }

        return view('leave-requests.edit', array_merge(
            $this->formData(),
            compact('leaveRequest')
        ));
    }

    public function update(Request $request, LeaveRequest $leaveRequest)
    {
        abort_unless(auth()->user()->can('leave.update'), 403);

        if (! $leaveRequest->isPending()) {
            return redirect()
                ->route('leave-requests.show', $leaveRequest)
                ->with('error', 'แก้ไขได้เฉพาะคำขอที่ยังรออนุมัติ');
        }

        $validated = $this->validateLeaveRequest($request);

        $employee = Employee::findOrFail($validated['employee_id']);

        $leaveRequest->update([
            'employee_id' => $employee->id,
            'department_id' => $validated['department_id'] ?? $employee->department_id,
            'leave_type_id' => $validated['leave_type_id'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'start_period' => $validated['start_period'],
            'end_period' => $validated['end_period'],
            'total_days' => $validated['total_days'],
            'reason' => $validated['reason'] ?? null,
            'contact_during_leave' => $validated['contact_during_leave'] ?? null,
        ]);

        $leaveRequest->actions()->create([
            'user_id' => auth()->id(),
            'action' => 'updated',
            'old_status' => $leaveRequest->status,
            'new_status' => $leaveRequest->status,
            'remark' => 'แก้ไขคำขอลา',
        ]);

        return redirect()
            ->route('leave-requests.show', $leaveRequest)
            ->with('success', 'แก้ไขคำขอลาเรียบร้อยแล้ว');
    }

    public function approve(Request $request, LeaveRequest $leaveRequest)
    {
        abort_unless(auth()->user()->can('leave.approve'), 403);

        if (! $leaveRequest->isPending()) {
            return back()->with('error', 'รายการนี้ไม่ได้อยู่ในสถานะรออนุมัติ');
        }

        $validated = $request->validate([
            'approval_remark' => ['nullable', 'string'],
        ]);

        $oldStatus = $leaveRequest->status;

        $leaveRequest->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'approval_remark' => $validated['approval_remark'] ?? null,
        ]);

        $leaveRequest->actions()->create([
            'user_id' => auth()->id(),
            'action' => 'approved',
            'old_status' => $oldStatus,
            'new_status' => 'approved',
            'remark' => $validated['approval_remark'] ?? null,
        ]);

        $this->notifyLeaveStatusChanged($leaveRequest, 'approved');

        return back()->with('success', 'อนุมัติคำขอลาเรียบร้อยแล้ว');
    }

    public function reject(Request $request, LeaveRequest $leaveRequest)
    {
        abort_unless(auth()->user()->can('leave.approve'), 403);

        if (! $leaveRequest->isPending()) {
            return back()->with('error', 'รายการนี้ไม่ได้อยู่ในสถานะรออนุมัติ');
        }

        $validated = $request->validate([
            'approval_remark' => ['required', 'string'],
        ]);

        $oldStatus = $leaveRequest->status;

        $leaveRequest->update([
            'status' => 'rejected',
            'rejected_by' => auth()->id(),
            'rejected_at' => now(),
            'approval_remark' => $validated['approval_remark'],
        ]);

        $leaveRequest->actions()->create([
            'user_id' => auth()->id(),
            'action' => 'rejected',
            'old_status' => $oldStatus,
            'new_status' => 'rejected',
            'remark' => $validated['approval_remark'],
        ]);

        $this->notifyLeaveStatusChanged($leaveRequest, 'rejected');

        return back()->with('success', 'ไม่อนุมัติคำขอลาเรียบร้อยแล้ว');
    }

    public function cancel(Request $request, LeaveRequest $leaveRequest)
    {
        abort_unless(auth()->user()->can('leave.update'), 403);

        if (! in_array($leaveRequest->status, ['pending', 'approved'])) {
            return back()->with('error', 'ไม่สามารถยกเลิกรายการนี้ได้');
        }

        $validated = $request->validate([
            'approval_remark' => ['nullable', 'string'],
        ]);

        $oldStatus = $leaveRequest->status;

        $leaveRequest->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'approval_remark' => $validated['approval_remark'] ?? $leaveRequest->approval_remark,
        ]);

        $leaveRequest->actions()->create([
            'user_id' => auth()->id(),
            'action' => 'cancelled',
            'old_status' => $oldStatus,
            'new_status' => 'cancelled',
            'remark' => $validated['approval_remark'] ?? null,
        ]);

        $this->notifyLeaveStatusChanged($leaveRequest, 'cancelled');

        return back()->with('success', 'ยกเลิกคำขอลาเรียบร้อยแล้ว');
    }

    public function destroy(LeaveRequest $leaveRequest)
    {
        abort_unless(auth()->user()->can('leave.delete'), 403);

        if (! $leaveRequest->isPending()) {
            return back()->with('error', 'ลบได้เฉพาะรายการที่ยังรออนุมัติ');
        }

        $leaveRequest->delete();

        return redirect()
            ->route('leave-requests.index')
            ->with('success', 'ลบคำขอลาเรียบร้อยแล้ว');
    }

    private function formData(): array
    {
        return [
            'employees' => Employee::query()
                ->where('status', 'active')
                ->orderBy('employee_code')
                ->get(),

            'departments' => Department::query()
                ->where('is_active', true)
                ->orderBy('code')
                ->get(),

            'leaveTypes' => LeaveType::query()
                ->where('is_active', true)
                ->orderBy('code')
                ->get(),
        ];
    }

    private function validateLeaveRequest(Request $request): array
    {
        return $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'leave_type_id' => ['required', 'exists:leave_types,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'start_period' => ['required', 'string', 'in:full,morning,afternoon'],
            'end_period' => ['required', 'string', 'in:full,morning,afternoon'],
            'total_days' => ['required', 'numeric', 'min:0.5'],
            'reason' => ['nullable', 'string'],
            'contact_during_leave' => ['nullable', 'string', 'max:255'],
        ]);
    }

    private function generateRequestNo(): string
    {
        return DocumentNumberService::nextForToday('LV', 'leave_requests', 'request_no');
    }

    private function notifyLeaveCreated(LeaveRequest $leaveRequest): void
    {
        $leaveRequest->load(['employee', 'leaveType']);

        $users = AppNotificationService::activeUsersWithPermission('leave.approve');

        foreach ($users as $user) {
            AppNotificationService::sendToUser(
                user: $user,
                title: 'มีคำขอลารออนุมัติ',
                message: "{$leaveRequest->request_no} - {$leaveRequest->employee?->full_name} / {$leaveRequest->leaveType?->name}",
                linkUrl: route('leave-requests.show', $leaveRequest),
                type: 'leave',
                data: [
                    'leave_request_id' => $leaveRequest->id,
                    'request_no' => $leaveRequest->request_no,
                    'status' => $leaveRequest->status,
                ]
            );
        }
    }

    private function notifyLeaveStatusChanged(LeaveRequest $leaveRequest, string $status): void
    {
        $leaveRequest->load(['creator', 'employee', 'leaveType']);

        if (! $leaveRequest->creator) {
            return;
        }

        $statusText = match ($status) {
            'approved' => 'อนุมัติแล้ว',
            'rejected' => 'ไม่อนุมัติ',
            'cancelled' => 'ยกเลิก',
            default => $status,
        };

        AppNotificationService::sendToUser(
            user: $leaveRequest->creator,
            title: 'สถานะคำขอลาถูกอัปเดต',
            message: "{$leaveRequest->request_no} {$statusText}",
            linkUrl: route('leave-requests.show', $leaveRequest),
            type: 'leave',
            data: [
                'leave_request_id' => $leaveRequest->id,
                'request_no' => $leaveRequest->request_no,
                'status' => $status,
            ]
        );
    }

    public function dashboard(Request $request)
    {
        abort_unless(auth()->user()->can('leave.view'), 403);

        ['month' => $month, 'year' => $year, 'selected_month' => $selectedMonth, 'start_date' => $startOfMonth, 'end_date' => $endOfMonth]
            = resolve_month_filter($request->input('month'), $request->input('year'));

        $departmentId = $request->string('department_id')->toString();
        $today = now()->toDateString();

        $periodScope = function ($query) use ($startOfMonth, $endOfMonth, $departmentId) {
            return $query
                ->whereDate('start_date', '<=', $endOfMonth)
                ->whereDate('end_date', '>=', $startOfMonth)
                ->when($departmentId, function ($leaveQuery) use ($departmentId) {
                    $leaveQuery->where('department_id', $departmentId);
                });
        };

        $summary = [
            'pending' => LeaveRequest::query()->tap($periodScope)->where('status', 'pending')->count(),
            'approved' => LeaveRequest::query()->tap($periodScope)->where('status', 'approved')->count(),
            'rejected' => LeaveRequest::query()->tap($periodScope)->where('status', 'rejected')->count(),
            'cancelled' => LeaveRequest::query()->tap($periodScope)->where('status', 'cancelled')->count(),

            'this_month' => LeaveRequest::query()->tap($periodScope)->count(),
            'approved_days' => (float) LeaveRequest::query()->tap($periodScope)->where('status', 'approved')->sum('total_days'),

            'today_on_leave' => LeaveRequest::query()
                ->where('status', 'approved')
                ->whereDate('start_date', '<=', $today)
                ->whereDate('end_date', '>=', $today)
                ->when($departmentId, function ($query) use ($departmentId) {
                    $query->where('department_id', $departmentId);
                })
                ->count(),
        ];

        $leaveTypeTotals = LeaveRequest::query()
            ->tap($periodScope)
            ->select('leave_type_id', DB::raw('count(*) as total_requests'), DB::raw('sum(total_days) as total_days'))
            ->groupBy('leave_type_id')
            ->get()
            ->keyBy('leave_type_id');

        $leaveTypeStats = LeaveType::query()
            ->where('is_active', true)
            ->orderBy('code')
            ->get()
            ->map(function (LeaveType $leaveType) use ($leaveTypeTotals) {
                $total = $leaveTypeTotals->get($leaveType->id);

                return [
                    'id' => $leaveType->id,
                    'code' => $leaveType->code,
                    'name' => $leaveType->name,
                    'requires_document' => $leaveType->requires_document,
                    'total_requests' => (int) ($total?->total_requests ?? 0),
                    'total_days' => (float) ($total?->total_days ?? 0),
                ];
            });

        $departmentStats = LeaveRequest::query()
            ->tap($periodScope)
            ->leftJoin('departments', 'departments.id', '=', 'leave_requests.department_id')
            ->select([
                DB::raw("coalesce(departments.name, 'ไม่ระบุหน่วยงาน') as department_name"),
                DB::raw('count(*) as total_requests'),
                DB::raw('sum(leave_requests.total_days) as total_days'),
            ])
            ->groupBy('departments.id', 'departments.name')
            ->orderByDesc('total_days')
            ->limit(8)
            ->get();

        $todayLeaves = LeaveRequest::query()
            ->with(['employee', 'department', 'leaveType'])
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->when($departmentId, function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            })
            ->orderBy('start_date')
            ->get();

        $pendingRequests = LeaveRequest::query()
            ->with(['employee', 'department', 'leaveType'])
            ->where('status', 'pending')
            ->when($departmentId, function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            })
            ->latest()
            ->limit(10)
            ->get();

        $upcomingLeaves = LeaveRequest::query()
            ->with(['employee', 'department', 'leaveType'])
            ->where('status', 'approved')
            ->whereDate('start_date', '>=', $today)
            ->when($departmentId, function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            })
            ->orderBy('start_date')
            ->limit(10)
            ->get();

        $recentRequests = LeaveRequest::query()
            ->with(['employee', 'department', 'leaveType', 'approver'])
            ->tap($periodScope)
            ->latest()
            ->limit(8)
            ->get();

        $departments = Department::query()
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        return view('leave-requests.dashboard', compact(
            'month',
            'year',
            'selectedMonth',
            'departmentId',
            'departments',
            'summary',
            'leaveTypeStats',
            'departmentStats',
            'todayLeaves',
            'pendingRequests',
            'upcomingLeaves',
            'recentRequests'
        ));
    }

    public function calendar(Request $request)
    {
        abort_unless(auth()->user()->can('leave.view'), 403);

        $year = (int) $request->input('year', now()->year);
        $month = (int) $request->input('month', now()->month);
        $status = $request->string('status')->toString();

        if ($month < 1 || $month > 12) {
            $month = now()->month;
        }

        $currentMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $startOfMonth = $currentMonth->copy()->startOfMonth();
        $endOfMonth = $currentMonth->copy()->endOfMonth();

        $calendarStart = $startOfMonth->copy()->startOfWeek(Carbon::SUNDAY);
        $calendarEnd = $endOfMonth->copy()->endOfWeek(Carbon::SATURDAY);

        $leaveRequests = LeaveRequest::query()
            ->with(['employee', 'department', 'leaveType'])
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            }, function ($query) {
                $query->whereIn('status', ['pending', 'approved']);
            })
            ->whereDate('start_date', '<=', $calendarEnd->toDateString())
            ->whereDate('end_date', '>=', $calendarStart->toDateString())
            ->orderBy('start_date')
            ->get();

        $leavesByDate = [];

        foreach ($leaveRequests as $leaveRequest) {
            $periodStart = Carbon::parse($leaveRequest->start_date)->max($calendarStart);
            $periodEnd = Carbon::parse($leaveRequest->end_date)->min($calendarEnd);

            foreach (CarbonPeriod::create($periodStart, $periodEnd) as $date) {
                $dateKey = $date->format('Y-m-d');

                if (! isset($leavesByDate[$dateKey])) {
                    $leavesByDate[$dateKey] = [];
                }

                $leavesByDate[$dateKey][] = $leaveRequest;
            }
        }

        $days = [];

        foreach (CarbonPeriod::create($calendarStart, $calendarEnd) as $date) {
            $days[] = [
                'date' => $date->copy(),
                'date_key' => $date->format('Y-m-d'),
                'is_current_month' => $date->month === $currentMonth->month,
                'is_today' => $date->isToday(),
                'leaves' => $leavesByDate[$date->format('Y-m-d')] ?? [],
            ];
        }

        $previousMonth = $currentMonth->copy()->subMonth();
        $nextMonth = $currentMonth->copy()->addMonth();

        return view('leave-requests.calendar', compact(
            'days',
            'currentMonth',
            'previousMonth',
            'nextMonth',
            'status'
        ));
    }
}
