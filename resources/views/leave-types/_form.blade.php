<div class="space-y-4">
    <div>
        <label class="mb-1 block font-medium">
            รหัสประเภทการลา <span class="text-red-600">*</span>
        </label>

        <input type="text"
               name="code"
               value="{{ old('code', $leaveType->code ?? '') }}"
               class="w-full rounded border-gray-300"
               placeholder="เช่น SICK, PERSONAL, VACATION">

        @error('code')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">
            ชื่อประเภทการลา <span class="text-red-600">*</span>
        </label>

        <input type="text"
               name="name"
               value="{{ old('name', $leaveType->name ?? '') }}"
               class="w-full rounded border-gray-300"
               placeholder="เช่น ลาป่วย, ลากิจ, ลาพักผ่อน">

        @error('name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">จำนวนวันต่อปี</label>

        <input type="number"
               step="0.5"
               name="default_days_per_year"
               value="{{ old('default_days_per_year', $leaveType->default_days_per_year ?? '') }}"
               class="w-full rounded border-gray-300">

        @error('default_days_per_year')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">รายละเอียด</label>

        <textarea name="description"
                  rows="3"
                  class="w-full rounded border-gray-300">{{ old('description', $leaveType->description ?? '') }}</textarea>

        @error('description')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex flex-col gap-2">
        <label class="flex items-center gap-2">
            <input type="checkbox"
                   name="requires_document"
                   value="1"
                   @checked(old('requires_document', $leaveType->requires_document ?? false))>

            <span>ต้องแนบเอกสารประกอบ</span>
        </label>

        <label class="flex items-center gap-2">
            <input type="checkbox"
                   name="is_active"
                   value="1"
                   @checked(old('is_active', $leaveType->is_active ?? true))>

            <span>เปิดใช้งาน</span>
        </label>
    </div>
</div>
