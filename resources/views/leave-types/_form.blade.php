<div class="grid gap-5 sm:grid-cols-2">
    <x-form.field label="รหัสประเภทการลา" name="code" required>
        <x-form.input name="code" :value="$leaveType->code ?? ''" placeholder="เช่น SICK, PERSONAL, VACATION" />
    </x-form.field>

    <x-form.field label="ชื่อประเภทการลา" name="name" required>
        <x-form.input name="name" :value="$leaveType->name ?? ''" placeholder="เช่น ลาป่วย, ลากิจ, ลาพักผ่อน" />
    </x-form.field>

    <x-form.field label="จำนวนวันต่อปี" name="default_days_per_year" hint="เว้นว่างได้ถ้าไม่จำกัด">
        <x-form.input type="number" step="0.5" min="0"
                      name="default_days_per_year"
                      :value="$leaveType->default_days_per_year ?? ''" />
    </x-form.field>

    <x-form.field label="รายละเอียด" name="description" class="sm:col-span-2">
        <x-form.textarea name="description" :value="$leaveType->description ?? ''" rows="3" />
    </x-form.field>

    <div class="flex flex-col gap-3 sm:col-span-2">
        <x-form.checkbox name="requires_document" label="ต้องแนบเอกสารประกอบ"
                         :checked="old('requires_document', $leaveType->requires_document ?? false)" />

        <x-form.checkbox name="is_active" label="เปิดใช้งาน"
                         :checked="old('is_active', $leaveType->is_active ?? true)" />
    </div>
</div>
