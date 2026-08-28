<div class="grid gap-5 sm:grid-cols-2">
    <x-form.field label="รหัสห้อง" name="code" required>
        <x-form.input name="code" :value="$meetingRoom->code ?? ''" placeholder="เช่น MR01" />
    </x-form.field>

    <x-form.field label="ชื่อห้องประชุม" name="name" required>
        <x-form.input name="name" :value="$meetingRoom->name ?? ''" />
    </x-form.field>

    <x-form.field label="สถานที่" name="location">
        <x-form.input name="location" :value="$meetingRoom->location ?? ''" placeholder="เช่น อาคาร A ชั้น 2" />
    </x-form.field>

    <x-form.field label="ความจุ (คน)" name="capacity">
        <x-form.input type="number" min="0" name="capacity" :value="$meetingRoom->capacity ?? ''" />
    </x-form.field>

    <div class="grid gap-3 sm:col-span-2 sm:grid-cols-2">
        <x-form.checkbox name="has_projector" label="โปรเจกเตอร์"
                         :checked="old('has_projector', $meetingRoom->has_projector ?? false)" />

        <x-form.checkbox name="has_sound_system" label="เครื่องเสียง"
                         :checked="old('has_sound_system', $meetingRoom->has_sound_system ?? false)" />

        <x-form.checkbox name="has_video_conference" label="ระบบประชุมทางไกล"
                         :checked="old('has_video_conference', $meetingRoom->has_video_conference ?? false)" />

        <x-form.checkbox name="has_whiteboard" label="ไวท์บอร์ด"
                         :checked="old('has_whiteboard', $meetingRoom->has_whiteboard ?? false)" />
    </div>

    <x-form.field label="รายละเอียด" name="description" class="sm:col-span-2">
        <x-form.textarea name="description" :value="$meetingRoom->description ?? ''" rows="3" />
    </x-form.field>

    <div class="sm:col-span-2">
        <x-form.checkbox name="is_active" label="เปิดใช้งาน"
                         :checked="old('is_active', $meetingRoom->is_active ?? true)" />
    </div>
</div>
