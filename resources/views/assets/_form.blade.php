@php
    $assetStatuses = [
        'active' => 'ใช้งาน',
        'repairing' => 'กำลังซ่อม',
        'broken' => 'ชำรุด',
        'disposed' => 'จำหน่าย',
        'lost' => 'สูญหาย',
    ];
@endphp

<div class="grid gap-5 sm:grid-cols-2">
    <x-form.field label="รหัสพัสดุ" name="asset_code" required>
        <x-form.input name="asset_code" :value="$asset->asset_code ?? ''" />
    </x-form.field>

    <x-form.field label="ชื่อพัสดุ" name="name" required>
        <x-form.input name="name" :value="$asset->name ?? ''" />
    </x-form.field>

    <x-form.field label="หมวดหมู่" name="asset_category_id">
        <x-form.select name="asset_category_id">
            <option value="">— เลือกหมวดหมู่ —</option>

            @foreach ($categories as $category)
                <option value="{{ $category->id }}"
                    @selected(old('asset_category_id', $asset->asset_category_id ?? '') == $category->id)>
                    {{ $category->code }} — {{ $category->name }}
                </option>
            @endforeach
        </x-form.select>
    </x-form.field>

    <x-form.field label="หน่วยงานเจ้าของ" name="department_id">
        <x-form.select name="department_id">
            <option value="">— ไม่ระบุ —</option>

            @foreach ($departments as $department)
                <option value="{{ $department->id }}"
                    @selected(old('department_id', $asset->department_id ?? '') == $department->id)>
                    {{ $department->code }} — {{ $department->name }}
                </option>
            @endforeach
        </x-form.select>
    </x-form.field>

    <x-form.field label="ผู้รับผิดชอบ" name="responsible_employee_id">
        <x-form.select name="responsible_employee_id">
            <option value="">— ไม่ระบุ —</option>

            @foreach ($employees as $employee)
                <option value="{{ $employee->id }}"
                    @selected(old('responsible_employee_id', $asset->responsible_employee_id ?? '') == $employee->id)>
                    {{ $employee->employee_code }} — {{ $employee->full_name }}
                </option>
            @endforeach
        </x-form.select>
    </x-form.field>

    <x-form.field label="สถานที่ตั้ง" name="location">
        <x-form.input name="location" :value="$asset->location ?? ''" />
    </x-form.field>

    <x-form.field label="ยี่ห้อ" name="brand">
        <x-form.input name="brand" :value="$asset->brand ?? ''" />
    </x-form.field>

    <x-form.field label="รุ่น" name="model">
        <x-form.input name="model" :value="$asset->model ?? ''" />
    </x-form.field>

    <x-form.field label="Serial Number" name="serial_number">
        <x-form.input name="serial_number" :value="$asset->serial_number ?? ''" />
    </x-form.field>

    <x-form.field label="วันที่รับเข้า" name="received_date">
        <x-form.input type="date" name="received_date"
                      :value="isset($asset) && $asset->received_date ? $asset->received_date->format('Y-m-d') : ''" />
    </x-form.field>

    <x-form.field label="ราคาทุน" name="purchase_price">
        <x-form.input type="number" step="0.01" min="0" name="purchase_price" :value="$asset->purchase_price ?? ''" />
    </x-form.field>

    <x-form.field label="แหล่งงบประมาณ" name="budget_source">
        <x-form.input name="budget_source" :value="$asset->budget_source ?? ''" placeholder="เช่น เงินบำรุง, งบประมาณ" />
    </x-form.field>

    <x-form.field label="สถานะ" name="status">
        <x-form.select name="status">
            @foreach ($assetStatuses as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $asset->status ?? 'active') === $value)>{{ $label }}</option>
            @endforeach
        </x-form.select>
    </x-form.field>

    <x-form.field label="หมายเหตุ" name="remark" class="sm:col-span-2">
        <x-form.textarea name="remark" :value="$asset->remark ?? ''" rows="3" />
    </x-form.field>
</div>
