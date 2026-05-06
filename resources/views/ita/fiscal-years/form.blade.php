<div>
    <label class="mb-1 block text-sm font-medium text-gray-700">ปีงบประมาณ</label>
    <input type="number"
           name="year"
           value="{{ old('year', $fiscalYear?->year) }}"
           class="w-full rounded border-gray-300"
           placeholder="เช่น 2569"
           min="2500"
           max="2700"
           required>

    @error('year')
        <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
    @enderror
</div>

<div>
    <label class="mb-1 block text-sm font-medium text-gray-700">ชื่อปีงบประมาณ</label>
    <input type="text"
           name="name"
           value="{{ old('name', $fiscalYear?->name) }}"
           class="w-full rounded border-gray-300"
           placeholder="เช่น ปีงบประมาณ 2569">

    @error('name')
        <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
    @enderror
</div>

<label class="flex items-center gap-2">
    <input type="checkbox"
           name="is_active"
           value="1"
           @checked(old('is_active', $fiscalYear?->is_active ?? true))
           class="rounded border-gray-300">

    <span class="text-sm text-gray-700">เปิดใช้งาน</span>
</label>
