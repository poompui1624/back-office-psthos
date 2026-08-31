<div class="grid gap-4 md:grid-cols-2">
    <x-form.field label="ปีงบประมาณ" name="fiscal_year_id" required>
        <x-form.select name="fiscal_year_id" required>
            <option value="">-- เลือกปีงบประมาณ --</option>

            @foreach ($fiscalYears as $year)
                <option value="{{ $year->id }}" @selected(old('fiscal_year_id', $topic?->fiscal_year_id) == $year->id)>
                    {{ $year->year }}
                </option>
            @endforeach
        </x-form.select>
    </x-form.field>

    <x-form.field label="ตัวชี้วัดที่" name="indicator_no" required>
        <x-form.input type="number" name="indicator_no"
                      :value="old('indicator_no', $topic?->indicator_no ?? 1)" min="1" required />
    </x-form.field>
</div>

<x-form.field label="ชื่อตัวชี้วัด" name="indicator_title">
    <x-form.input name="indicator_title" :value="old('indicator_title', $topic?->indicator_title)"
                  placeholder="เช่น การเปิดเผยข้อมูล" />
</x-form.field>

<div class="grid gap-4 md:grid-cols-2">
    <x-form.field label="รหัส MOIT" name="code" required>
        <x-form.input name="code" :value="old('code', $topic?->code)" placeholder="เช่น MOIT 1" required />
    </x-form.field>

    <x-form.field label="ลำดับแสดงผล" name="sort_order">
        <x-form.input type="number" name="sort_order" :value="old('sort_order', $topic?->sort_order ?? 0)" min="0" />
    </x-form.field>
</div>

<x-form.field label="หัวข้อหลัก" name="title" required>
    <x-form.textarea name="title" rows="3" :value="old('title', $topic?->title)" required />
</x-form.field>

<x-form.field label="รายละเอียดเพิ่มเติม" name="description">
    <x-form.textarea name="description" rows="3" :value="old('description', $topic?->description)" />
</x-form.field>

<x-form.checkbox name="is_active" label="เปิดใช้งาน" :checked="old('is_active', $topic?->is_active ?? true)" />
