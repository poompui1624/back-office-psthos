<div class="grid gap-5 sm:grid-cols-2">
    <x-form.field label="รหัสหมวดหมู่" name="code" required>
        <x-form.input name="code" :value="$assetCategory->code ?? ''" placeholder="เช่น CO, IT, FU" />
    </x-form.field>

    <x-form.field label="ชื่อหมวดหมู่" name="name" required>
        <x-form.input name="name" :value="$assetCategory->name ?? ''" placeholder="เช่น คอมพิวเตอร์และอุปกรณ์" />
    </x-form.field>

    <x-form.field label="รายละเอียด" name="description" class="sm:col-span-2">
        <x-form.textarea name="description" :value="$assetCategory->description ?? ''" rows="3" />
    </x-form.field>

    <div class="sm:col-span-2">
        <x-form.checkbox name="is_active" label="เปิดใช้งาน"
                         :checked="old('is_active', $assetCategory->is_active ?? true)" />
    </div>
</div>
