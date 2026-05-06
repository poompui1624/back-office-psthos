<?php

namespace App\Http\Controllers;

use App\Models\MeetingRoom;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MeetingRoomController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->can('meeting.view'), 403);

        $search = $request->string('search')->toString();

        $rooms = MeetingRoom::query()
            ->withCount('bookings')
            ->when($search, function ($query) use ($search) {
                $query->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            })
            ->orderBy('code')
            ->paginate(25)
            ->withQueryString();

        return view('meeting-rooms.index', compact('rooms', 'search'));
    }

    public function create()
    {
        abort_unless(auth()->user()->can('meeting.create'), 403);

        return view('meeting-rooms.create');
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->can('meeting.create'), 403);

        $validated = $this->validateRoom($request);

        MeetingRoom::create($validated);

        return redirect()
            ->route('meeting-rooms.index')
            ->with('success', 'เพิ่มห้องประชุมเรียบร้อยแล้ว');
    }

    public function edit(MeetingRoom $meetingRoom)
    {
        abort_unless(auth()->user()->can('meeting.update'), 403);

        return view('meeting-rooms.edit', compact('meetingRoom'));
    }

    public function update(Request $request, MeetingRoom $meetingRoom)
    {
        abort_unless(auth()->user()->can('meeting.update'), 403);

        $validated = $this->validateRoom($request, $meetingRoom);

        $meetingRoom->update($validated);

        return redirect()
            ->route('meeting-rooms.index')
            ->with('success', 'แก้ไขห้องประชุมเรียบร้อยแล้ว');
    }

    public function destroy(MeetingRoom $meetingRoom)
    {
        abort_unless(auth()->user()->can('meeting.delete'), 403);

        if ($meetingRoom->bookings()->exists()) {
            return back()->with('error', 'ไม่สามารถลบได้ เพราะมีรายการจองห้องนี้อยู่');
        }

        $meetingRoom->delete();

        return redirect()
            ->route('meeting-rooms.index')
            ->with('success', 'ลบห้องประชุมเรียบร้อยแล้ว');
    }

    private function validateRoom(Request $request, ?MeetingRoom $meetingRoom = null): array
    {
        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('meeting_rooms', 'code')->ignore($meetingRoom?->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'capacity' => ['nullable', 'integer', 'min:0'],
            'has_projector' => ['nullable', 'boolean'],
            'has_sound_system' => ['nullable', 'boolean'],
            'has_video_conference' => ['nullable', 'boolean'],
            'has_whiteboard' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'description' => ['nullable', 'string'],
        ]);

        $validated['capacity'] = $validated['capacity'] ?? 0;
        $validated['has_projector'] = $request->boolean('has_projector');
        $validated['has_sound_system'] = $request->boolean('has_sound_system');
        $validated['has_video_conference'] = $request->boolean('has_video_conference');
        $validated['has_whiteboard'] = $request->boolean('has_whiteboard');
        $validated['is_active'] = $request->boolean('is_active');

        return $validated;
    }
}
