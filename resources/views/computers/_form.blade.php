<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <div>
        <label class="mb-1 block font-medium">ผูกกับพัสดุ</label>
        <select name="asset_id" class="w-full rounded border-gray-300">
            <option value="">-- ไม่ผูกกับพัสดุ --</option>
            @foreach ($assets as $asset)
                <option value="{{ $asset->id }}"
                    @selected(old('asset_id', $computer->asset_id ?? '') == $asset->id)>
                    {{ $asset->asset_code }} - {{ $asset->name }}
                </option>
            @endforeach
        </select>
        @error('asset_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">Hostname <span class="text-red-600">*</span></label>
        <input type="text" name="hostname" value="{{ old('hostname', $computer->hostname ?? '') }}"
               class="w-full rounded border-gray-300">
        @error('hostname') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">Machine UUID</label>
        <input type="text" name="machine_uuid" value="{{ old('machine_uuid', $computer->machine_uuid ?? '') }}"
               class="w-full rounded border-gray-300">
        @error('machine_uuid') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">IP Address</label>
        <input type="text" name="ip_address" value="{{ old('ip_address', $computer->ip_address ?? '') }}"
               class="w-full rounded border-gray-300">
        @error('ip_address') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">MAC Address</label>
        <input type="text" name="mac_address" value="{{ old('mac_address', $computer->mac_address ?? '') }}"
               class="w-full rounded border-gray-300">
        @error('mac_address') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">Serial Number</label>
        <input type="text" name="serial_number" value="{{ old('serial_number', $computer->serial_number ?? '') }}"
               class="w-full rounded border-gray-300">
        @error('serial_number') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">Manufacturer</label>
        <input type="text" name="manufacturer" value="{{ old('manufacturer', $computer->manufacturer ?? '') }}"
               class="w-full rounded border-gray-300">
    </div>

    <div>
        <label class="mb-1 block font-medium">Model</label>
        <input type="text" name="model" value="{{ old('model', $computer->model ?? '') }}"
               class="w-full rounded border-gray-300">
    </div>

    <div>
        <label class="mb-1 block font-medium">OS Name</label>
        <input type="text" name="os_name" value="{{ old('os_name', $computer->os_name ?? '') }}"
               class="w-full rounded border-gray-300">
    </div>

    <div>
        <label class="mb-1 block font-medium">OS Version</label>
        <input type="text" name="os_version" value="{{ old('os_version', $computer->os_version ?? '') }}"
               class="w-full rounded border-gray-300">
    </div>

    <div>
        <label class="mb-1 block font-medium">CPU</label>
        <input type="text" name="cpu_name" value="{{ old('cpu_name', $computer->cpu_name ?? '') }}"
               class="w-full rounded border-gray-300">
    </div>

    <div>
        <label class="mb-1 block font-medium">RAM GB</label>
        <input type="number" name="ram_gb" value="{{ old('ram_gb', $computer->ram_gb ?? '') }}"
               class="w-full rounded border-gray-300">
    </div>

    <div>
        <label class="mb-1 block font-medium">Storage GB</label>
        <input type="number" name="storage_gb" value="{{ old('storage_gb', $computer->storage_gb ?? '') }}"
               class="w-full rounded border-gray-300">
    </div>

    <div>
        <label class="mb-1 block font-medium">หน่วยงาน</label>
        <select name="department_id" class="w-full rounded border-gray-300">
            <option value="">-- เลือกหน่วยงาน --</option>
            @foreach ($departments as $department)
                <option value="{{ $department->id }}"
                    @selected(old('department_id', $computer->department_id ?? '') == $department->id)>
                    {{ $department->code }} - {{ $department->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="mb-1 block font-medium">ผู้รับผิดชอบ</label>
        <select name="responsible_employee_id" class="w-full rounded border-gray-300">
            <option value="">-- เลือกบุคลากร --</option>
            @foreach ($employees as $employee)
                <option value="{{ $employee->id }}"
                    @selected(old('responsible_employee_id', $computer->responsible_employee_id ?? '') == $employee->id)>
                    {{ $employee->employee_code }} - {{ $employee->full_name }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="mb-1 block font-medium">สถานะ</label>
        <select name="status" class="w-full rounded border-gray-300">
            <option value="active" @selected(old('status', $computer->status ?? 'active') === 'active')>ใช้งาน</option>
            <option value="inactive" @selected(old('status', $computer->status ?? '') === 'inactive')>ปิดใช้งาน</option>
            <option value="repairing" @selected(old('status', $computer->status ?? '') === 'repairing')>กำลังซ่อม</option>
            <option value="disposed" @selected(old('status', $computer->status ?? '') === 'disposed')>จำหน่าย</option>
        </select>
    </div>

    <div class="md:col-span-2">
        <label class="mb-1 block font-medium">หมายเหตุ</label>
        <textarea name="remark" rows="3"
                  class="w-full rounded border-gray-300">{{ old('remark', $computer->remark ?? '') }}</textarea>
    </div>
</div>
