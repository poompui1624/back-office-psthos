<x-form.field label="ปีงบประมาณ" name="year" required>
    <x-form.input type="number" name="year" :value="old('year', $fiscalYear?->year)"
                  placeholder="เช่น 2569" min="2500" max="2700" required />
</x-form.field>

<x-form.field label="ชื่อปีงบประมาณ" name="name">
    <x-form.input name="name" :value="old('name', $fiscalYear?->name)" placeholder="เช่น ปีงบประมาณ 2569" />
</x-form.field>

<x-form.checkbox name="is_active" label="เปิดใช้งาน"
                 :checked="old('is_active', $fiscalYear?->is_active ?? true)" />
