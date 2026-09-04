@props(['position' => null])

<div class="grid gap-5 sm:grid-cols-2">
    <x-form.field label="ชื่อตำแหน่ง" name="name" required>
        <x-form.input name="name" :value="$position?->name" placeholder="เช่น พยาบาลวิชาชีพ" />
    </x-form.field>

    <x-form.field label="ระดับ" name="level" hint="เช่น ปฏิบัติการ ชำนาญการ">
        <x-form.input name="level" :value="$position?->level" placeholder="เช่น ชำนาญการ" />
    </x-form.field>

    <div class="sm:col-span-2">
        <x-form.checkbox name="is_active" label="เปิดใช้งาน"
                         :checked="old('is_active', $position?->is_active ?? true)" />
    </div>
</div>
