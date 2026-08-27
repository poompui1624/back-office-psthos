<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <div class="md:col-span-2">
        <label class="mb-1 block font-medium">
            บุคลากร <span class="text-red-600">*</span>
        </label>

        <select name="employee_id" class="w-full rounded border-gray-300">
            <option value="">-- เลือกบุคลากร --</option>
            @foreach ($employees as $employee)
                <option value="{{ $employee->id }}"
                    @selected(old('employee_id', $salaryProfile->employee_id ?? '') == $employee->id)>
                    {{ $employee->employee_code }} - {{ $employee->full_name }}
                    @if ($employee->department)
                        / {{ $employee->department->name }}
                    @endif
                </option>
            @endforeach
        </select>

        @error('employee_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="md:col-span-2">
        <h2 class="mt-2 border-b pb-2 text-lg font-bold">รายได้</h2>
    </div>

    <div>
        <label class="mb-1 block font-medium">
            เงินเดือนพื้นฐาน <span class="text-red-600">*</span>
        </label>

        <input type="number"
               step="0.01"
               min="0"
               name="base_salary"
               value="{{ old('base_salary', $salaryProfile->base_salary ?? 0) }}"
               class="w-full rounded border-gray-300">

        @error('base_salary')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">เงินประจำตำแหน่ง</label>

        <input type="number"
               step="0.01"
               min="0"
               name="position_allowance"
               value="{{ old('position_allowance', $salaryProfile->position_allowance ?? 0) }}"
               class="w-full rounded border-gray-300">

        @error('position_allowance')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">เงินวิชาชีพ</label>

        <input type="number"
               step="0.01"
               min="0"
               name="professional_allowance"
               value="{{ old('professional_allowance', $salaryProfile->professional_allowance ?? 0) }}"
               class="w-full rounded border-gray-300">

        @error('professional_allowance')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">รายได้อื่น</label>

        <input type="number"
               step="0.01"
               min="0"
               name="other_allowance"
               value="{{ old('other_allowance', $salaryProfile->other_allowance ?? 0) }}"
               class="w-full rounded border-gray-300">

        @error('other_allowance')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="md:col-span-2">
        <h2 class="mt-4 border-b pb-2 text-lg font-bold">รายการหักประจำ</h2>
    </div>

    <div>
        <label class="mb-1 block font-medium">ประกันสังคม</label>

        <input type="number"
               step="0.01"
               min="0"
               name="social_security"
               value="{{ old('social_security', $salaryProfile->social_security ?? 0) }}"
               class="w-full rounded border-gray-300">

        @error('social_security')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">ภาษี</label>

        <input type="number"
               step="0.01"
               min="0"
               name="tax"
               value="{{ old('tax', $salaryProfile->tax ?? 0) }}"
               class="w-full rounded border-gray-300">

        @error('tax')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">กองทุนสำรองเลี้ยงชีพ</label>

        <input type="number"
               step="0.01"
               min="0"
               name="provident_fund"
               value="{{ old('provident_fund', $salaryProfile->provident_fund ?? 0) }}"
               class="w-full rounded border-gray-300">

        @error('provident_fund')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">รายการหักอื่น</label>

        <input type="number"
               step="0.01"
               min="0"
               name="other_deduction"
               value="{{ old('other_deduction', $salaryProfile->other_deduction ?? 0) }}"
               class="w-full rounded border-gray-300">

        @error('other_deduction')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="md:col-span-2">
        <h2 class="mt-4 border-b pb-2 text-lg font-bold">อัตราหักจากเวลาทำงาน</h2>
    </div>

    <div>
        <label class="mb-1 block font-medium">หักมาสาย / นาที</label>

        <input type="number"
               step="0.01"
               min="0"
               name="late_deduction_per_minute"
               value="{{ old('late_deduction_per_minute', $salaryProfile->late_deduction_per_minute ?? 0) }}"
               class="w-full rounded border-gray-300">

        @error('late_deduction_per_minute')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">หักกลับก่อน / นาที</label>

        <input type="number"
               step="0.01"
               min="0"
               name="early_leave_deduction_per_minute"
               value="{{ old('early_leave_deduction_per_minute', $salaryProfile->early_leave_deduction_per_minute ?? 0) }}"
               class="w-full rounded border-gray-300">

        @error('early_leave_deduction_per_minute')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">หักขาดงาน / วัน</label>

        <input type="number"
               step="0.01"
               min="0"
               name="absent_deduction_per_day"
               value="{{ old('absent_deduction_per_day', $salaryProfile->absent_deduction_per_day ?? 0) }}"
               class="w-full rounded border-gray-300">

        @error('absent_deduction_per_day')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label class="mb-1 block font-medium">อัตรา OT / ชั่วโมง</label>

        <input type="number"
               step="0.01"
               min="0"
               name="ot_rate_per_hour"
               value="{{ old('ot_rate_per_hour', $salaryProfile->ot_rate_per_hour ?? 0) }}"
               class="w-full rounded border-gray-300">

        <p class="mt-1 text-sm text-gray-500">ใช้คูณกับชั่วโมงเวร OT ที่ยืนยันแล้ว</p>

        @error('ot_rate_per_hour')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex items-end">
        <label class="flex items-center gap-2">
            <input type="checkbox"
                   name="is_active"
                   value="1"
                   @checked(old('is_active', $salaryProfile->is_active ?? true))>

            <span>เปิดใช้งาน</span>
        </label>
    </div>

    <div class="md:col-span-2">
        <label class="mb-1 block font-medium">หมายเหตุ</label>

        <textarea name="remark"
                  rows="3"
                  class="w-full rounded border-gray-300">{{ old('remark', $salaryProfile->remark ?? '') }}</textarea>

        @error('remark')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>
