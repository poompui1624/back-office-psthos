<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Computer;
use App\Models\Department;
use App\Models\Employee;
use App\Models\RepairRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\AppNotificationService;

class RepairRequestController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->can('repair.view'), 403);

        $search = $request->string('search')->toString();
        $status = $request->string('status')->toString();

        $repairRequests = RepairRequest::query()
            ->with([
                'requester',
                'requesterEmployee',
                'department',
                'assignedUser',
                'repairable',
            ])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('ticket_no', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%")
                        ->orWhereHas('requesterEmployee', function ($employeeQuery) use ($search) {
                            $employeeQuery->where('employee_code', 'like', "%{$search}%")
                                ->orWhere('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('department', function ($departmentQuery) use ($search) {
                            $departmentQuery->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('repair-requests.index', compact('repairRequests', 'search', 'status'));
    }

    public function create()
    {
        abort_unless(auth()->user()->can('repair.create'), 403);

        return view('repair-requests.create', $this->formData());
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->can('repair.create'), 403);

        $validated = $this->validateRepairRequest($request);

        $repairRequest = DB::transaction(function () use ($validated) {
            $repairable = $this->resolveRepairable(
                $validated['repairable_type'] ?? null,
                $validated['repairable_id'] ?? null
            );

            $repairRequest = RepairRequest::create([
                'ticket_no' => $this->generateTicketNo(),
                'requested_by' => auth()->id(),
                'requester_employee_id' => $validated['requester_employee_id'] ?? null,
                'department_id' => $validated['department_id'] ?? null,
                'repairable_type' => $repairable ? get_class($repairable) : null,
                'repairable_id' => $repairable?->getKey(),
                'category' => $validated['category'],
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'location' => $validated['location'] ?? null,
                'priority' => $validated['priority'],
                'assigned_to' => $validated['assigned_to'] ?? null,
                'status' => 'new',
            ]);

            $repairRequest->updates()->create([
                'user_id' => auth()->id(),
                'action' => 'created',
                'old_status' => null,
                'new_status' => 'new',
                'note' => 'สร้างรายการแจ้งซ่อม',
            ]);

            return $repairRequest;
        });

        $repairRequest->load(['assignedUser', 'requester']);

        $this->notifyRepairCreated($repairRequest);

        return redirect()
            ->route('repair-requests.index')
            ->with('success', 'บันทึกแจ้งซ่อมเรียบร้อยแล้ว');
    }

    public function show(RepairRequest $repairRequest)
    {
        abort_unless(auth()->user()->can('repair.view'), 403);

        $repairRequest->load([
            'requester',
            'requesterEmployee',
            'department',
            'assignedUser',
            'repairable',
            'updates.user',
            'attachments.uploader',
        ]);

        return view('repair-requests.show', compact('repairRequest'));
    }

    public function edit(RepairRequest $repairRequest)
    {
        abort_unless(auth()->user()->can('repair.update'), 403);

        return view('repair-requests.edit', array_merge(
            $this->formData(),
            compact('repairRequest')
        ));
    }

    public function update(Request $request, RepairRequest $repairRequest)
    {
        abort_unless(auth()->user()->can('repair.update'), 403);

        $validated = $this->validateRepairRequest($request, $repairRequest);

        $oldAssignedTo = $repairRequest->assigned_to;

        $repairable = $this->resolveRepairable(
            $validated['repairable_type'] ?? null,
            $validated['repairable_id'] ?? null
        );

        $repairRequest->update([
            'requester_employee_id' => $validated['requester_employee_id'] ?? null,
            'department_id' => $validated['department_id'] ?? null,
            'repairable_type' => $repairable ? get_class($repairable) : null,
            'repairable_id' => $repairable?->getKey(),
            'category' => $validated['category'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'location' => $validated['location'] ?? null,
            'priority' => $validated['priority'],
            'assigned_to' => $validated['assigned_to'] ?? null,
        ]);

        $repairRequest->updates()->create([
            'user_id' => auth()->id(),
            'action' => 'updated',
            'old_status' => $repairRequest->status,
            'new_status' => $repairRequest->status,
            'note' => 'แก้ไขข้อมูลแจ้งซ่อม',
        ]);

        if ($oldAssignedTo != $repairRequest->assigned_to && $repairRequest->assigned_to) {
            $this->notifyRepairAssigned($repairRequest);
        }

        return redirect()
            ->route('repair-requests.show', $repairRequest)
            ->with('success', 'แก้ไขแจ้งซ่อมเรียบร้อยแล้ว');
    }

    public function updateStatus(Request $request, RepairRequest $repairRequest)
    {
        abort_unless(auth()->user()->can('repair.update'), 403);

        $validated = $request->validate([
            'status' => ['required', 'string', 'max:50'],
            'solution' => ['nullable', 'string'],
            'note' => ['nullable', 'string'],
        ]);

        $oldStatus = $repairRequest->status;
        $newStatus = $validated['status'];

        $data = [
            'status' => $newStatus,
        ];

        if ($newStatus === 'in_progress' && ! $repairRequest->started_at) {
            $data['started_at'] = now();
        }

        if ($newStatus === 'completed') {
            $data['completed_at'] = now();
            $data['solution'] = $validated['solution'] ?? $repairRequest->solution;
        }

        if ($newStatus === 'cancelled') {
            $data['cancelled_at'] = now();
        }

        $repairRequest->update($data);

        $repairRequest->updates()->create([
            'user_id' => auth()->id(),
            'action' => 'status_changed',
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'note' => $validated['note'] ?? null,
        ]);

        if ($oldStatus !== $newStatus) {
            $this->notifyRepairStatusChanged($repairRequest, $oldStatus, $newStatus);
        }

        return back()->with('success', 'อัปเดตสถานะแจ้งซ่อมเรียบร้อยแล้ว');
    }

    public function destroy(RepairRequest $repairRequest)
    {
        abort_unless(auth()->user()->can('repair.delete'), 403);

        $repairRequest->delete();

        return redirect()
            ->route('repair-requests.index')
            ->with('success', 'ลบรายการแจ้งซ่อมเรียบร้อยแล้ว');
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

            'assets' => Asset::query()
                ->orderBy('asset_code')
                ->get(),

            'computers' => Computer::query()
                ->orderBy('hostname')
                ->get(),

            'users' => User::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
        ];
    }

    private function validateRepairRequest(Request $request): array
    {
        return $request->validate([
            'requester_employee_id' => ['nullable', 'exists:employees,id'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'repairable_type' => ['nullable', 'string', 'in:asset,computer,other'],
            'repairable_id' => ['nullable', 'integer'],
            'category' => ['required', 'string', 'max:100'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'priority' => ['required', 'string', 'max:50'],
            'assigned_to' => ['nullable', 'exists:users,id'],
        ]);
    }

    private function resolveRepairable(?string $type, ?int $id)
    {
        if (! $type || ! $id || $type === 'other') {
            return null;
        }

        return match ($type) {
            'asset' => Asset::findOrFail($id),
            'computer' => Computer::findOrFail($id),
            default => null,
        };
    }

    private function generateTicketNo(): string
    {
        $prefix = 'RP' . now()->format('Ymd');

        $count = RepairRequest::where('ticket_no', 'like', $prefix . '%')->count() + 1;

        return $prefix . str_pad((string) $count, 4, '0', STR_PAD_LEFT);
    }

    private function notifyRepairCreated(RepairRequest $repairRequest): void
    {
        $repairRequest->load(['assignedUser', 'requester']);

        if ($repairRequest->assignedUser) {
            AppNotificationService::sendToUser(
                user: $repairRequest->assignedUser,
                title: 'มีงานแจ้งซ่อมใหม่ที่มอบหมายให้คุณ',
                message: "{$repairRequest->ticket_no} - {$repairRequest->title}",
                linkUrl: route('repair-requests.show', $repairRequest),
                type: 'repair',
                data: [
                    'repair_request_id' => $repairRequest->id,
                    'ticket_no' => $repairRequest->ticket_no,
                    'status' => $repairRequest->status,
                ]
            );
        }

        $repairAdmins = User::permission('repair.update')
            ->where('is_active', true)
            ->where('id', '!=', $repairRequest->assigned_to)
            ->get();

        foreach ($repairAdmins as $user) {
            AppNotificationService::sendToUser(
                user: $user,
                title: 'มีรายการแจ้งซ่อมใหม่',
                message: "{$repairRequest->ticket_no} - {$repairRequest->title}",
                linkUrl: route('repair-requests.show', $repairRequest),
                type: 'repair',
                data: [
                    'repair_request_id' => $repairRequest->id,
                    'ticket_no' => $repairRequest->ticket_no,
                    'status' => $repairRequest->status,
                ]
            );
        }
    }

    private function notifyRepairAssigned(RepairRequest $repairRequest): void
    {
        $repairRequest->load('assignedUser');

        if (! $repairRequest->assignedUser) {
            return;
        }

        AppNotificationService::sendToUser(
            user: $repairRequest->assignedUser,
            title: 'คุณได้รับมอบหมายงานแจ้งซ่อม',
            message: "{$repairRequest->ticket_no} - {$repairRequest->title}",
            linkUrl: route('repair-requests.show', $repairRequest),
            type: 'repair',
            data: [
                'repair_request_id' => $repairRequest->id,
                'ticket_no' => $repairRequest->ticket_no,
                'status' => $repairRequest->status,
            ]
        );
    }

    private function notifyRepairStatusChanged(
        RepairRequest $repairRequest,
        string $oldStatus,
        string $newStatus
    ): void {
        $repairRequest->load(['requester', 'assignedUser']);

        $statusText = $this->repairStatusText($newStatus);

        $users = collect();

        if ($repairRequest->requester) {
            $users->push($repairRequest->requester);
        }

        if ($repairRequest->assignedUser) {
            $users->push($repairRequest->assignedUser);
        }

        $users = $users
            ->filter()
            ->unique('id')
            ->values();

        foreach ($users as $user) {
            AppNotificationService::sendToUser(
                user: $user,
                title: 'สถานะแจ้งซ่อมถูกอัปเดต',
                message: "{$repairRequest->ticket_no} เปลี่ยนสถานะเป็น {$statusText}",
                linkUrl: route('repair-requests.show', $repairRequest),
                type: 'repair',
                data: [
                    'repair_request_id' => $repairRequest->id,
                    'ticket_no' => $repairRequest->ticket_no,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                ]
            );
        }
    }

    private function repairStatusText(string $status): string
    {
        return match ($status) {
            'new' => 'ใหม่',
            'in_progress' => 'กำลังดำเนินการ',
            'completed' => 'เสร็จแล้ว',
            'cancelled' => 'ยกเลิก',
            default => $status,
        };
    }

    public function kanban(Request $request)
    {
        abort_unless(auth()->user()->can('repair.view'), 403);

        $search = $request->string('search')->toString();

        $statuses = [
            'new' => 'ใหม่',
            'in_progress' => 'กำลังดำเนินการ',
            'completed' => 'เสร็จแล้ว',
            'cancelled' => 'ยกเลิก',
        ];

        $repairRequests = RepairRequest::query()
            ->with([
                'requester',
                'requesterEmployee',
                'department',
                'assignedUser',
                'repairable',
            ])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('ticket_no', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%")
                        ->orWhereHas('requesterEmployee', function ($employeeQuery) use ($search) {
                            $employeeQuery->where('employee_code', 'like', "%{$search}%")
                                ->orWhere('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('department', function ($departmentQuery) use ($search) {
                            $departmentQuery->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->whereIn('status', array_keys($statuses))
            ->latest()
            ->get()
            ->groupBy('status');

        return view('repair-requests.kanban', compact(
            'repairRequests',
            'statuses',
            'search'
        ));
    }
}
