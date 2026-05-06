<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <div>
        <label class="mb-1 block font-medium">
            รหัสห้อง <span class="text-red-600">*</span>
        </label>

        <input type="text"
               name="code"
               value="{{ old('code', $meetingRoom->code ?? '') }}"
               class="w-full rounded border-gray-300"
               placeholder="เช่น MR001">

        @error('code')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">
            ชื่อห้อง <span class="text-red-600">*</span>
        </label>

        <input type="text"
               name="name"
               value="{{ old('name', $meetingRoom->name ?? '') }}"
               class="w-full rounded border-gray-300"
               placeholder="เช่น ห้องประชุมใหญ่">

        @error('name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">สถานที่</label>

        <input type="text"
               name="location"
               value="{{ old('location', $meetingRoom->location ?? '') }}"
               class="w-full rounded border-gray-300"
               placeholder="เช่น ชั้น 2 อาคารอำนวยการ">

        @error('location')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">ความจุ</label>

        <input type="number"
               name="capacity"
               min="0"
               value="{{ old('capacity', $meetingRoom->capacity ?? 0) }}"
               class="w-full rounded border-gray-300">

        @error('capacity')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label class="mb-2 block font-medium">อุปกรณ์ประจำห้อง</label>

        <div class="grid gap-2 md:grid-cols-4">
            <label class="flex items-center gap-2 rounded border border-gray-200 p-3">
                <input type="checkbox"
                       name="has_projector"
                       value="1"
                       @checked(old('has_projector', $meetingRoom->has_projector ?? false))>
                <span>โปรเจคเตอร์</span>
            </label>

            <label class="flex items-center gap-2 rounded border border-gray-200 p-3">
                <input type="checkbox"
                       name="has_sound_system"
                       value="1"
                       @checked(old('has_sound_system', $meetingRoom->has_sound_system ?? false))>
                <span>เครื่องเสียง</span>
            </label>

            <label class="flex items-center gap-2 rounded border border-gray-200 p-3">
                <input type="checkbox"
                       name="has_video_conference"
                       value="1"
                       @checked(old('has_video_conference', $meetingRoom->has_video_conference ?? false))>
                <span>Video Conference</span>
            </label>

            <label class="flex items-center gap-2 rounded border border-gray-200 p-3">
                <input type="checkbox"
                       name="has_whiteboard"
                       value="1"
                       @checked(old('has_whiteboard', $meetingRoom->has_whiteboard ?? false))>
                <span>Whiteboard</span>
            </label>
        </div>
    </div>

    <div class="md:col-span-2">
        <label class="mb-1 block font-medium">รายละเอียด</label>

        <textarea name="description"
                  rows="3"
                  class="w-full rounded border-gray-300">{{ old('description', $meetingRoom->description ?? '') }}</textarea>

        @error('description')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label class="flex items-center gap-2">
            <input type="checkbox"
                   name="is_active"
                   value="1"
                   @checked(old('is_active', $meetingRoom->is_active ?? true))>
            <span>เปิดใช้งาน</span>
        </label>
    </div>
</div>
