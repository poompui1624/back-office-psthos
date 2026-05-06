<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\MeetingBooking;
use App\Models\MeetingRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MeetingBookingController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->can('meeting.view'), 403);

        $search = $request->string('search')->toString();
        $status = $request->string('status')->toString();
        $roomId = $request->string('meeting_room_id')->toString();
        $dateFrom = $request->string('date_from')->toString();
        $dateTo = $request->string('date_to')->toString();

        $bookings = MeetingBooking::query()
            ->with(['room', 'employee', 'department', 'creator', 'approver'])
            ->when($search, function ($query) use ($search) {
                $query->where('booking_no', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhereHas('employee', function ($employeeQuery) use ($search) {
                        $employeeQuery->where('employee_code', 'like', "%{$search}%")
                            ->orWhere('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            })
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($roomId, fn ($query) => $query->where('meeting_room_id', $roomId))
            ->when($dateFrom, fn ($query) => $query->whereDate('start_at', '>=', $dateFrom))
            ->when($dateTo, fn ($query) => $query->whereDate('start_at', '<=', $dateTo))
            ->latest('start_at')
            ->paginate(25)
            ->withQueryString();

        return view('meeting-bookings.index', [
            'bookings' => $bookings,
            'search' => $search,
            'status' => $status,
            'roomId' => $roomId,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'rooms' => MeetingRoom::where('is_active', true)->orderBy('code')->get(),
        ]);
    }

    public function create()
    {
        abort_unless(auth()->user()->can('meeting.create'), 403);

        return view('meeting-bookings.create', $this->formData());
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->can('meeting.create'), 403);

        $validated = $this->validateBooking($request);

        if ($this->hasConflict(
            (int) $validated['meeting_room_id'],
            $validated['start_at'],
            $validated['end_at']
        )) {
            return back()
                ->withInput()
                ->with('error', 'ไม่สามารถจองได้ เพราะช่วงเวลานี้มีการจองห้องนี้แล้ว');
        }

        $booking = DB::transaction(function () use ($validated, $request) {
            $employee = ! empty($validated['employee_id'])
                ? Employee::find($validated['employee_id'])
                : null;

            $booking = MeetingBooking::create([
                'booking_no' => $this->generateBookingNo(),
                'meeting_room_id' => $validated['meeting_room_id'],
                'employee_id' => $validated['employee_id'] ?? null,
                'department_id' => $validated['department_id'] ?? $employee?->department_id,
                'created_by' => auth()->id(),
                'title' => $validated['title'],
                'purpose' => $validated['purpose'] ?? null,
                'start_at' => $validated['start_at'],
                'end_at' => $validated['end_at'],
                'attendees_count' => $validated['attendees_count'] ?? 0,
                'need_projector' => $request->boolean('need_projector'),
                'need_sound_system' => $request->boolean('need_sound_system'),
                'need_video_conference' => $request->boolean('need_video_conference'),
                'need_whiteboard' => $request->boolean('need_whiteboard'),
                'status' => 'pending',
                'remark' => $validated['remark'] ?? null,
            ]);

            $booking->actions()->create([
                'user_id' => auth()->id(),
                'action' => 'created',
                'old_status' => null,
                'new_status' => 'pending',
                'remark' => 'สร้างรายการจองห้องประชุม',
            ]);

            return $booking;
        });

        return redirect()
            ->route('meeting-bookings.show', $booking)
            ->with('success', 'บันทึกการจองห้องประชุมเรียบร้อยแล้ว');
    }

    public function show(MeetingBooking $meetingBooking)
    {
        abort_unless(auth()->user()->can('meeting.view'), 403);

        $meetingBooking->load([
            'room',
            'employee',
            'department',
            'creator',
            'approver',
            'rejecter',
            'actions.user',
        ]);

        return view('meeting-bookings.show', compact('meetingBooking'));
    }

    public function edit(MeetingBooking $meetingBooking)
    {
        abort_unless(auth()->user()->can('meeting.update'), 403);

        if (! $meetingBooking->isPending()) {
            return back()->with('error', 'แก้ไขได้เฉพาะรายการที่รออนุมัติ');
        }

        return view('meeting-bookings.edit', array_merge(
            $this->formData(),
            compact('meetingBooking')
        ));
    }

    public function update(Request $request, MeetingBooking $meetingBooking)
    {
        abort_unless(auth()->user()->can('meeting.update'), 403);

        if (! $meetingBooking->isPending()) {
            return back()->with('error', 'แก้ไขได้เฉพาะรายการที่รออนุมัติ');
        }

        $validated = $this->validateBooking($request);

        if ($this->hasConflict(
            (int) $validated['meeting_room_id'],
            $validated['start_at'],
            $validated['end_at'],
            $meetingBooking->id
        )) {
            return back()
                ->withInput()
                ->with('error', 'ไม่สามารถแก้ไขได้ เพราะช่วงเวลานี้มีการจองห้องนี้แล้ว');
        }

        $oldStatus = $meetingBooking->status;

        $employee = ! empty($validated['employee_id'])
            ? Employee::find($validated['employee_id'])
            : null;

        $meetingBooking->update([
            'meeting_room_id' => $validated['meeting_room_id'],
            'employee_id' => $validated['employee_id'] ?? null,
            'department_id' => $validated['department_id'] ?? $employee?->department_id,
            'title' => $validated['title'],
            'purpose' => $validated['purpose'] ?? null,
            'start_at' => $validated['start_at'],
            'end_at' => $validated['end_at'],
            'attendees_count' => $validated['attendees_count'] ?? 0,
            'need_projector' => $request->boolean('need_projector'),
            'need_sound_system' => $request->boolean('need_sound_system'),
            'need_video_conference' => $request->boolean('need_video_conference'),
            'need_whiteboard' => $request->boolean('need_whiteboard'),
            'remark' => $validated['remark'] ?? null,
        ]);

        $meetingBooking->actions()->create([
            'user_id' => auth()->id(),
            'action' => 'updated',
            'old_status' => $oldStatus,
            'new_status' => $meetingBooking->status,
            'remark' => 'แก้ไขรายการจองห้องประชุม',
        ]);

        return redirect()
            ->route('meeting-bookings.show', $meetingBooking)
            ->with('success', 'แก้ไขการจองห้องประชุมเรียบร้อยแล้ว');
    }

    public function approve(Request $request, MeetingBooking $meetingBooking)
    {
        abort_unless(auth()->user()->can('meeting.approve'), 403);

        if (! $meetingBooking->isPending()) {
            return back()->with('error', 'รายการนี้ไม่ได้อยู่ในสถานะรออนุมัติ');
        }

        if ($this->hasConflict(
            $meetingBooking->meeting_room_id,
            $meetingBooking->start_at,
            $meetingBooking->end_at,
            $meetingBooking->id
        )) {
            return back()->with('error', 'ไม่สามารถอนุมัติได้ เพราะมีรายการจองอื่นชนเวลา');
        }

        $validated = $request->validate([
            'approval_remark' => ['nullable', 'string'],
        ]);

        $oldStatus = $meetingBooking->status;

        $meetingBooking->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'approval_remark' => $validated['approval_remark'] ?? null,
        ]);

        $meetingBooking->actions()->create([
            'user_id' => auth()->id(),
            'action' => 'approved',
            'old_status' => $oldStatus,
            'new_status' => 'approved',
            'remark' => $validated['approval_remark'] ?? null,
        ]);

        return back()->with('success', 'อนุมัติการจองห้องประชุมเรียบร้อยแล้ว');
    }

    public function reject(Request $request, MeetingBooking $meetingBooking)
    {
        abort_unless(auth()->user()->can('meeting.approve'), 403);

        if (! $meetingBooking->isPending()) {
            return back()->with('error', 'รายการนี้ไม่ได้อยู่ในสถานะรออนุมัติ');
        }

        $validated = $request->validate([
            'approval_remark' => ['required', 'string'],
        ]);

        $oldStatus = $meetingBooking->status;

        $meetingBooking->update([
            'status' => 'rejected',
            'rejected_by' => auth()->id(),
            'rejected_at' => now(),
            'approval_remark' => $validated['approval_remark'],
        ]);

        $meetingBooking->actions()->create([
            'user_id' => auth()->id(),
            'action' => 'rejected',
            'old_status' => $oldStatus,
            'new_status' => 'rejected',
            'remark' => $validated['approval_remark'],
        ]);

        return back()->with('success', 'ไม่อนุมัติการจองห้องประชุมเรียบร้อยแล้ว');
    }

    public function cancel(Request $request, MeetingBooking $meetingBooking)
    {
        abort_unless(auth()->user()->can('meeting.update'), 403);

        if (! in_array($meetingBooking->status, ['pending', 'approved'])) {
            return back()->with('error', 'ไม่สามารถยกเลิกรายการนี้ได้');
        }

        $validated = $request->validate([
            'approval_remark' => ['nullable', 'string'],
        ]);

        $oldStatus = $meetingBooking->status;

        $meetingBooking->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'approval_remark' => $validated['approval_remark'] ?? $meetingBooking->approval_remark,
        ]);

        $meetingBooking->actions()->create([
            'user_id' => auth()->id(),
            'action' => 'cancelled',
            'old_status' => $oldStatus,
            'new_status' => 'cancelled',
            'remark' => $validated['approval_remark'] ?? null,
        ]);

        return back()->with('success', 'ยกเลิกการจองห้องประชุมเรียบร้อยแล้ว');
    }

    public function destroy(MeetingBooking $meetingBooking)
    {
        abort_unless(auth()->user()->can('meeting.delete'), 403);

        if (! $meetingBooking->isPending()) {
            return back()->with('error', 'ลบได้เฉพาะรายการที่รออนุมัติ');
        }

        $meetingBooking->delete();

        return redirect()
            ->route('meeting-bookings.index')
            ->with('success', 'ลบรายการจองห้องประชุมเรียบร้อยแล้ว');
    }

    private function formData(): array
    {
        return [
            'rooms' => MeetingRoom::where('is_active', true)
                ->orderBy('code')
                ->get(),

            'employees' => Employee::where('status', 'active')
                ->orderBy('employee_code')
                ->get(),

            'departments' => Department::where('is_active', true)
                ->orderBy('code')
                ->get(),
        ];
    }

    private function validateBooking(Request $request): array
    {
        return $request->validate([
            'meeting_room_id' => ['required', 'exists:meeting_rooms,id'],
            'employee_id' => ['nullable', 'exists:employees,id'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'title' => ['required', 'string', 'max:255'],
            'purpose' => ['nullable', 'string'],
            'start_at' => ['required', 'date'],
            'end_at' => ['required', 'date', 'after:start_at'],
            'attendees_count' => ['nullable', 'integer', 'min:0'],
            'need_projector' => ['nullable', 'boolean'],
            'need_sound_system' => ['nullable', 'boolean'],
            'need_video_conference' => ['nullable', 'boolean'],
            'need_whiteboard' => ['nullable', 'boolean'],
            'remark' => ['nullable', 'string'],
        ]);
    }

    private function hasConflict(int $roomId, string $startAt, string $endAt, ?int $ignoreId = null): bool
    {
        return MeetingBooking::query()
            ->where('meeting_room_id', $roomId)
            ->whereIn('status', ['pending', 'approved'])
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->where(function ($query) use ($startAt, $endAt) {
                $query->where('start_at', '<', $endAt)
                    ->where('end_at', '>', $startAt);
            })
            ->exists();
    }

    private function generateBookingNo(): string
    {
        $prefix = 'MR' . now()->format('Ymd');

        $count = MeetingBooking::where('booking_no', 'like', $prefix . '%')->count() + 1;

        return $prefix . str_pad((string) $count, 4, '0', STR_PAD_LEFT);
    }

    public function printView(MeetingBooking $meetingBooking)
    {
        abort_unless(auth()->user()->can('meeting.view'), 403);

        $meetingBooking->load([
            'room',
            'employee',
            'department',
            'creator',
            'approver',
            'rejecter',
            'actions.user',
        ]);

        return view('meeting-bookings.print', compact('meetingBooking'));
    }
}