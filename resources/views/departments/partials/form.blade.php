<div>
    <label class="mb-1 block text-sm font-medium">รหัสหน่วยงาน</label>
    <input type="text"
           name="code"
           value="{{ old('code', $department?->code) }}"
           class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-900">
    @error('code')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<div>
    <label class="mb-1 block text-sm font-medium">ชื่อหน่วยงาน <span class="text-red-500">*</span></label>
    <input type="text"
           name="name"
           value="{{ old('name', $department?->name) }}"
           required
           class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-900">
    @error('name')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<div>
    <label class="mb-1 block text-sm font-medium">ชื่อย่อ</label>
    <input type="text"
           name="short_name"
           value="{{ old('short_name', $department?->short_name) }}"
           class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-900">
    @error('short_name')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<div>
    <label class="mb-1 block text-sm font-medium">เบอร์โทร</label>
    <input type="text"
           name="phone"
           value="{{ old('phone', $department?->phone) }}"
           class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-900">
    @error('phone')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<div>
    <label class="mb-1 block text-sm font-medium">สถานที่ตั้ง</label>
    <input type="text"
           name="location"
           value="{{ old('location', $department?->location) }}"
           class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-900">
    @error('location')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<div>
    <label class="mb-1 block text-sm font-medium">รายละเอียด</label>
    <textarea name="description"
              rows="4"
              class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-900">{{ old('description', $department?->description) }}</textarea>
    @error('description')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<label class="flex items-center gap-2">
    <input type="checkbox"
           name="is_active"
           value="1"
           @checked(old('is_active', $department?->is_active ?? true))
           class="rounded border-zinc-300">
    <span class="text-sm">เปิดใช้งาน</span>
</label>
