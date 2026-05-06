<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="mb-1 block text-sm font-medium text-gray-700">ปีงบประมาณ</label>
        <select name="fiscal_year_id" class="w-full rounded border-gray-300" required>
            <option value="">-- เลือกปีงบประมาณ --</option>
            @foreach ($fiscalYears as $year)
                <option value="{{ $year->id }}"
                    @selected(old('fiscal_year_id', $topic?->fiscal_year_id) == $year->id)>
                    {{ $year->year }}
                </option>
            @endforeach
        </select>
        @error('fiscal_year_id')
            <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium text-gray-700">ตัวชี้วัดที่</label>
        <input type="number"
               name="indicator_no"
               value="{{ old('indicator_no', $topic?->indicator_no ?? 1) }}"
               class="w-full rounded border-gray-300"
               min="1"
               required>
        @error('indicator_no')
            <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
        @enderror
    </div>
</div>

<div>
    <label class="mb-1 block text-sm font-medium text-gray-700">ชื่อตัวชี้วัด</label>
    <input type="text"
           name="indicator_title"
           value="{{ old('indicator_title', $topic?->indicator_title) }}"
           class="w-full rounded border-gray-300"
           placeholder="เช่น การเปิดเผยข้อมูล">
    @error('indicator_title')
        <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
    @enderror
</div>

<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="mb-1 block text-sm font-medium text-gray-700">รหัส MOIT</label>
        <input type="text"
               name="code"
               value="{{ old('code', $topic?->code) }}"
               class="w-full rounded border-gray-300"
               placeholder="เช่น MOIT 1"
               required>
        @error('code')
            <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium text-gray-700">ลำดับแสดงผล</label>
        <input type="number"
               name="sort_order"
               value="{{ old('sort_order', $topic?->sort_order ?? 0) }}"
               class="w-full rounded border-gray-300"
               min="0">
        @error('sort_order')
            <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
        @enderror
    </div>
</div>

<div>
    <label class="mb-1 block text-sm font-medium text-gray-700">หัวข้อหลัก</label>
    <textarea name="title"
              rows="3"
              class="w-full rounded border-gray-300"
              required>{{ old('title', $topic?->title) }}</textarea>
    @error('title')
        <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
    @enderror
</div>

<div>
    <label class="mb-1 block text-sm font-medium text-gray-700">รายละเอียดเพิ่มเติม</label>
    <textarea name="description"
              rows="3"
              class="w-full rounded border-gray-300">{{ old('description', $topic?->description) }}</textarea>
    @error('description')
        <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
    @enderror
</div>

<label class="flex items-center gap-2">
    <input type="checkbox"
           name="is_active"
           value="1"
           @checked(old('is_active', $topic?->is_active ?? true))
           class="rounded border-gray-300">
    <span class="text-sm text-gray-700">เปิดใช้งาน</span>
</label>
