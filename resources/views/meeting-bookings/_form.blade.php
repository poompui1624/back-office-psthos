<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <div>
        <label class="mb-1 block font-medium">
            ห้องประชุม <span class="text-red-600">*</span>
        </label>

        <select name="meeting_room_id" class="w-full rounded border-gray-300">
            <option value="">-- เลือกห้องประชุม --</option>
            @foreach ($rooms as $room)
                <option value="{{ $room->id }}"
                    @selected(old('meeting_room_id', $meetingBooking->meeting_room_id ?? '') == $room->id)>
                    {{ $room->code }} - {{ $room->name }}
                    @if ($room->capacity)
                        ({{ $room->capacity }} คน)
                    @endif
                </option>
            @endforeach
        </select>

        @error('meeting_room_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">ผู้จอง / บุคลากร</label>

        <select name="employee_id" class="w-full rounded border-gray-300">
            <option value="">-- ไม่ระบุ --</option>
            @foreach ($employees as $employee)
                <option value="{{ $employee->id }}"
                    @selected(old('employee_id', $meetingBooking->employee_id ?? '') == $employee->id)>
                    {{ $employee->employee_code }} - {{ $employee->full_name }}
                </option>
            @endforeach
        </select>

        @error('employee_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">หน่วยงาน</label>

        <select name="department_id" class="w-full rounded border-gray-300">
            <option value="">-- ใช้ตามข้อมูลบุคลากร --</option>
            @foreach ($departments as $department)
                <option value="{{ $department->id }}"
                    @selected(old('department_id', $meetingBooking->department_id ?? '') == $department->id)>
                    {{ $department->code }} - {{ $department->name }}
                </option>
            @endforeach
        </select>

        @error('department_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">จำนวนผู้เข้าร่วม</label>

        <input type="number"
               name="attendees_count"
               min="0"
               value="{{ old('attendees_count', $meetingBooking->attendees_count ?? 0) }}"
               class="w-full rounded border-gray-300">

        @error('attendees_count')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label class="mb-1 block font-medium">
            หัวข้อการประชุม <span class="text-red-600">*</span>
        </label>

        <input type="text"
               name="title"
               value="{{ old('title', $meetingBooking->title ?? '') }}"
               class="w-full rounded border-gray-300"
               placeholder="เช่น ประชุมคณะกรรมการบริหาร">

        @error('title')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">
            เริ่มประชุม <span class="text-red-600">*</span>
        </label>

        <input type="datetime-local"
               name="start_at"
               value="{{ old('start_at', isset($meetingBooking) && $meetingBooking->start_at ? $meetingBooking->start_at->format('Y-m-d\TH:i') : '') }}"
               class="w-full rounded border-gray-300">

        @error('start_at')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">
            สิ้นสุดประชุม <span class="text-red-600">*</span>
        </label>

        <input type="datetime-local"
               name="end_at"
               value="{{ old('end_at', isset($meetingBooking) && $meetingBooking->end_at ? $meetingBooking->end_at->format('Y-m-d\TH:i') : '') }}"
               class="w-full rounded border-gray-300">

        @error('end_at')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label class="mb-1 block font-medium">วัตถุประสงค์</label>

        <textarea name="purpose"
                  rows="3"
                  class="w-full rounded border-gray-300">{{ old('purpose', $meetingBooking->purpose ?? '') }}</textarea>

        @error('purpose')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label class="mb-2 block font-medium">อุปกรณ์ที่ต้องการใช้</label>

        <div class="grid gap-2 md:grid-cols-4">
            <label class="flex items-center gap-2 rounded border border-gray-200 p-3">
                <input type="checkbox"
                       name="need_projector"
                       value="1"
                       @checked(old('need_projector', $meetingBooking->need_projector ?? false))>
                <span>โปรเจคเตอร์</span>
            </label>

            <label class="flex items-center gap-2 rounded border border-gray-200 p-3">
                <input type="checkbox"
                       name="need_sound_system"
                       value="1"
                       @checked(old('need_sound_system', $meetingBooking->need_sound_system ?? false))>
                <span>เครื่องเสียง</span>
            </label>

            <label class="flex items-center gap-2 rounded border border-gray-200 p-3">
                <input type="checkbox"
                       name="need_video_conference"
                       value="1"
                       @checked(old('need_video_conference', $meetingBooking->need_video_conference ?? false))>
                <span>Video Conference</span>
            </label>

            <label class="flex items-center gap-2 rounded border border-gray-200 p-3">
                <input type="checkbox"
                       name="need_whiteboard"
                       value="1"
                       @checked(old('need_whiteboard', $meetingBooking->need_whiteboard ?? false))>
                <span>Whiteboard</span>
            </label>
        </div>
    </div>

    <div class="md:col-span-2">
        <label class="mb-1 block font-medium">หมายเหตุ</label>

        <textarea name="remark"
                  rows="3"
                  class="w-full rounded border-gray-300">{{ old('remark', $meetingBooking->remark ?? '') }}</textarea>

        @error('remark')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>
