<div class="grid gap-5 sm:grid-cols-2">
    <x-form.field label="ชื่อ Software" name="name" required>
        <x-form.input name="name" :value="$softwareProduct->name ?? ''" />
    </x-form.field>

    <x-form.field label="Vendor" name="vendor">
        <x-form.input name="vendor" :value="$softwareProduct->vendor ?? ''" />
    </x-form.field>

    <x-form.field label="Category" name="category" class="sm:col-span-2">
        <x-form.input name="category" :value="$softwareProduct->category ?? ''"
                      placeholder="เช่น Office, Antivirus, OS, Medical" />
    </x-form.field>

    <x-form.field label="รายละเอียด" name="description" class="sm:col-span-2">
        <x-form.textarea name="description" :value="$softwareProduct->description ?? ''" rows="3" />
    </x-form.field>

    <div class="sm:col-span-2">
        <x-form.checkbox name="is_active" label="เปิดใช้งาน"
                         :checked="old('is_active', $softwareProduct->is_active ?? true)" />
    </div>
</div>
