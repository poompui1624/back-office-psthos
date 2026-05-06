<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <div>
        <label class="mb-1 block font-medium">รหัสพัสดุ <span class="text-red-600">*</span></label>
        <input type="text" name="asset_code" value="{{ old('asset_code', $asset->asset_code ?? '') }}"
               class="w-full rounded border-gray-300">
        @error('asset_code') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">ชื่อพัสดุ <span class="text-red-600">*</span></label>
        <input type="text" name="name" value="{{ old('name', $asset->name ?? '') }}"
               class="w-full rounded border-gray-300">
        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">หมวดหมู่</label>
        <select name="asset_category_id" class="w-full rounded border-gray-300">
            <option value="">-- เลือกหมวดหมู่ --</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}"
                    @selected(old('asset_category_id', $asset->asset_category_id ?? '') == $category->id)>
                    {{ $category->code }} - {{ $category->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="mb-1 block font-medium">หน่วยงานเจ้าของ</label>
        <select name="department_id" class="w-full rounded border-gray-300">
            <option value="">-- เลือกหน่วยงาน --</option>
            @foreach ($departments as $department)
                <option value="{{ $department->id }}"
                    @selected(old('department_id', $asset->department_id ?? '') == $department->id)>
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
                    @selected(old('responsible_employee_id', $asset->responsible_employee_id ?? '') == $employee->id)>
                    {{ $employee->employee_code }} - {{ $employee->full_name }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="mb-1 block font-medium">สถานที่ตั้ง</label>
        <input type="text" name="location" value="{{ old('location', $asset->location ?? '') }}"
               class="w-full rounded border-gray-300">
    </div>

    <div>
        <label class="mb-1 block font-medium">ยี่ห้อ</label>
        <input type="text" name="brand" value="{{ old('brand', $asset->brand ?? '') }}"
               class="w-full rounded border-gray-300">
    </div>

    <div>
        <label class="mb-1 block font-medium">รุ่น</label>
        <input type="text" name="model" value="{{ old('model', $asset->model ?? '') }}"
               class="w-full rounded border-gray-300">
    </div>

    <div>
        <label class="mb-1 block font-medium">Serial Number</label>
        <input type="text" name="serial_number" value="{{ old('serial_number', $asset->serial_number ?? '') }}"
               class="w-full rounded border-gray-300">
    </div>

    <div>
        <label class="mb-1 block font-medium">วันที่รับเข้า</label>
        <input type="date" name="received_date"
               value="{{ old('received_date', isset($asset) && $asset->received_date ? $asset->received_date->format('Y-m-d') : '') }}"
               class="w-full rounded border-gray-300">
    </div>

    <div>
        <label class="mb-1 block font-medium">ราคาทุน</label>
        <input type="number" step="0.01" name="purchase_price"
               value="{{ old('purchase_price', $asset->purchase_price ?? '') }}"
               class="w-full rounded border-gray-300">
    </div>

    <div>
        <label class="mb-1 block font-medium">แหล่งงบประมาณ</label>
        <input type="text" name="budget_source" value="{{ old('budget_source', $asset->budget_source ?? '') }}"
               class="w-full rounded border-gray-300">
    </div>

    <div>
        <label class="mb-1 block font-medium">สถานะ</label>
        <select name="status" class="w-full rounded border-gray-300">
            <option value="active" @selected(old('status', $asset->status ?? 'active') === 'active')>ใช้งาน</option>
            <option value="repairing" @selected(old('status', $asset->status ?? '') === 'repairing')>กำลังซ่อม</option>
            <option value="broken" @selected(old('status', $asset->status ?? '') === 'broken')>ชำรุด</option>
            <option value="disposed" @selected(old('status', $asset->status ?? '') === 'disposed')>จำหน่าย</option>
            <option value="lost" @selected(old('status', $asset->status ?? '') === 'lost')>สูญหาย</option>
        </select>
    </div>

    <div class="md:col-span-2">
        <label class="mb-1 block font-medium">หมายเหตุ</label>
        <textarea name="remark" rows="3"
                  class="w-full rounded border-gray-300">{{ old('remark', $asset->remark ?? '') }}</textarea>
    </div>
</div>
