@php
    // The icon set the public page can actually render, so a link cannot be
    // given a name that would come out blank.
    $iconNames = [
        'document', 'clipboard', 'building', 'users', 'user', 'calendar', 'clock',
        'device', 'wrench', 'key', 'cloud', 'box', 'chart', 'money', 'bell',
        'search', 'shield', 'inbox', 'external-link', 'cog', 'approvals',
    ];
@endphp

<div class="grid gap-4 md:grid-cols-2">
    <x-form.field label="ชื่อลิงก์" name="label" required>
        <x-form.input name="label" :value="old('label', $link?->label)" placeholder="เช่น ตรวจสอบสิทธิ์รักษา" required />
    </x-form.field>

    <x-form.field label="ไอคอน" name="icon">
        <x-form.select name="icon">
            @foreach ($iconNames as $name)
                <option value="{{ $name }}" @selected(old('icon', $link?->icon ?? 'document') === $name)>{{ $name }}</option>
            @endforeach
        </x-form.select>
    </x-form.field>
</div>

<x-form.field label="ปลายทาง" name="url" required
              hint="ใส่ที่อยู่เต็มขึ้นต้นด้วย https:// หรือเส้นทางภายในเช่น /ita-public">
    <x-form.input name="url" :value="old('url', $link?->url)" placeholder="https://" required />
</x-form.field>

<x-form.field label="คำอธิบายสั้น" name="description">
    <x-form.input name="description" :value="old('description', $link?->description)" />
</x-form.field>

<x-form.field label="ลำดับแสดงผล" name="sort_order" hint="เลขน้อยอยู่ก่อน" class="max-w-xs">
    <x-form.input type="number" name="sort_order" :value="old('sort_order', $link?->sort_order ?? 0)" min="0" />
</x-form.field>

<div class="space-y-3">
    <x-form.checkbox name="opens_new_tab" label="เปิดในแท็บใหม่"
                     :checked="old('opens_new_tab', $link?->opens_new_tab ?? true)" />

    <x-form.checkbox name="is_active" label="แสดงบนหน้าเว็บ"
                     :checked="old('is_active', $link?->is_active ?? true)" />
</div>
