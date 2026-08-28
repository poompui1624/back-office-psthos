@php
    $computerStatuses = [
        'active' => 'ใช้งาน',
        'inactive' => 'ปิดใช้งาน',
        'repairing' => 'กำลังซ่อม',
        'disposed' => 'จำหน่าย',
    ];
@endphp

<div class="grid gap-5 sm:grid-cols-2">
    <x-form.field label="ผูกกับพัสดุ" name="asset_id" class="sm:col-span-2">
        <x-form.select name="asset_id">
            <option value="">— ไม่ผูกกับทะเบียนพัสดุ —</option>

            @foreach ($assets as $asset)
                <option value="{{ $asset->id }}" @selected(old('asset_id', $computer->asset_id ?? '') == $asset->id)>
                    {{ $asset->asset_code }} — {{ $asset->name }}
                </option>
            @endforeach
        </x-form.select>
    </x-form.field>

    <x-form.field label="Hostname" name="hostname" required>
        <x-form.input name="hostname" :value="$computer->hostname ?? ''" />
    </x-form.field>

    <x-form.field label="Machine UUID" name="machine_uuid">
        <x-form.input name="machine_uuid" :value="$computer->machine_uuid ?? ''" />
    </x-form.field>

    <x-form.field label="IP Address" name="ip_address">
        <x-form.input name="ip_address" :value="$computer->ip_address ?? ''" />
    </x-form.field>

    <x-form.field label="MAC Address" name="mac_address">
        <x-form.input name="mac_address" :value="$computer->mac_address ?? ''" />
    </x-form.field>

    <x-form.field label="Serial Number" name="serial_number">
        <x-form.input name="serial_number" :value="$computer->serial_number ?? ''" />
    </x-form.field>

    <x-form.field label="Manufacturer" name="manufacturer">
        <x-form.input name="manufacturer" :value="$computer->manufacturer ?? ''" />
    </x-form.field>

    <x-form.field label="Model" name="model">
        <x-form.input name="model" :value="$computer->model ?? ''" />
    </x-form.field>

    <x-form.field label="OS Name" name="os_name">
        <x-form.input name="os_name" :value="$computer->os_name ?? ''" />
    </x-form.field>

    <x-form.field label="OS Version" name="os_version">
        <x-form.input name="os_version" :value="$computer->os_version ?? ''" />
    </x-form.field>

    <x-form.field label="CPU" name="cpu_name">
        <x-form.input name="cpu_name" :value="$computer->cpu_name ?? ''" />
    </x-form.field>

    <x-form.field label="RAM (GB)" name="ram_gb">
        <x-form.input type="number" min="0" name="ram_gb" :value="$computer->ram_gb ?? ''" />
    </x-form.field>

    <x-form.field label="Storage (GB)" name="storage_gb">
        <x-form.input type="number" min="0" name="storage_gb" :value="$computer->storage_gb ?? ''" />
    </x-form.field>

    <x-form.field label="หน่วยงาน" name="department_id">
        <x-form.select name="department_id">
            <option value="">— ไม่ระบุ —</option>

            @foreach ($departments as $department)
                <option value="{{ $department->id }}"
                    @selected(old('department_id', $computer->department_id ?? '') == $department->id)>
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
                    @selected(old('responsible_employee_id', $computer->responsible_employee_id ?? '') == $employee->id)>
                    {{ $employee->employee_code }} — {{ $employee->full_name }}
                </option>
            @endforeach
        </x-form.select>
    </x-form.field>

    <x-form.field label="สถานะ" name="status">
        <x-form.select name="status">
            @foreach ($computerStatuses as $value => $label)
                <option value="{{ $value }}"
                    @selected(old('status', $computer->status ?? 'active') === $value)>{{ $label }}</option>
            @endforeach
        </x-form.select>
    </x-form.field>

    <x-form.field label="หมายเหตุ" name="remark" class="sm:col-span-2">
        <x-form.textarea name="remark" :value="$computer->remark ?? ''" rows="3" />
    </x-form.field>
</div>
