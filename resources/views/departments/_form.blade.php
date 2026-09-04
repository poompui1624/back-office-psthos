@props(['department' => null, 'parents'])

<div class="grid gap-5 sm:grid-cols-2">
    <x-form.field label="หน่วยงานแม่" name="parent_id">
        <x-form.select name="parent_id">
            <option value="">— ไม่มีหน่วยงานแม่ —</option>

            @foreach ($parents as $parent)
                <option value="{{ $parent->id }}" @selected(old('parent_id', $department?->parent_id) == $parent->id)>
                    {{ $parent->code }} — {{ $parent->name }}
                </option>
            @endforeach
        </x-form.select>
    </x-form.field>

    <x-form.field label="ประเภท" name="type" hint="เช่น organization, department_group, unit">
        <x-form.input name="type" :value="$department?->type" placeholder="เช่น unit" />
    </x-form.field>

    <x-form.field label="รหัสหน่วยงาน" name="code" required>
        <x-form.input name="code" :value="$department?->code" placeholder="เช่น IT, HR, ER" />
    </x-form.field>

    <x-form.field label="ชื่อหน่วยงาน" name="name" required>
        <x-form.input name="name" :value="$department?->name" placeholder="เช่น เทคโนโลยีสารสนเทศ" />
    </x-form.field>

    <div class="sm:col-span-2">
        <x-form.checkbox name="is_active" label="เปิดใช้งาน"
                         :checked="old('is_active', $department?->is_active ?? true)" />
    </div>
</div>
