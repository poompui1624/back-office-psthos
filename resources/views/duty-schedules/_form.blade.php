<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <div>
        <label class="mb-1 block font-medium">
            บุคลากร <span class="text-red-600">*</span>
        </label>

        <select name="employee_id" class="w-full rounded border-gray-300">
            <option value="">-- เลือกบุคลากร --</option>
            @foreach ($employees as $employee)
                <option value="{{ $employee->id }}"
                    @selected(old('employee_id', $dutySchedule->employee_id ?? '') == $employee->id)>
                    {{ $employee->employee_code }} - {{ $employee->full_name }}
                </option>
            @endforeach
        </select>

        @error('employee_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">หน่วยงาน</label>

        <select name="department_id" class="w-full rounded border-gray-300">
            <option value="">-- ใช้ตามข้อมูลบุคลากร --</option>
            @foreach ($departments as $department)
                <option value="{{ $department->id }}"
                    @selected(old('department_id', $dutySchedule->department_id ?? '') == $department->id)>
                    {{ $department->code }} - {{ $department->name }}
                </option>
            @endforeach
        </select>

        @error('department_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">
            วันที่เข้าเวร <span class="text-red-600">*</span>
        </label>

        <input type="date"
               name="work_date"
               value="{{ old('work_date', isset($dutySchedule) && $dutySchedule->work_date ? $dutySchedule->work_date->format('Y-m-d') : now()->format('Y-m-d')) }}"
               class="w-full rounded border-gray-300">

        @error('work_date')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">
            ประเภทเวร <span class="text-red-600">*</span>
        </label>

        <select name="shift_type_id" class="w-full rounded border-gray-300">
            <option value="">-- เลือกประเภทเวร --</option>
            @foreach ($shiftTypes as $shiftType)
                <option value="{{ $shiftType->id }}"
                    @selected(old('shift_type_id', $dutySchedule->shift_type_id ?? '') == $shiftType->id)>
                    {{ $shiftType->code }} - {{ $shiftType->name }}
                    ({{ substr($shiftType->start_time, 0, 5) }}-{{ substr($shiftType->end_time, 0, 5) }})
                </option>
            @endforeach
        </select>

        @error('shift_type_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">กลุ่มงาน / บทบาท</label>

        <input type="text"
               name="role_group"
               value="{{ old('role_group', $dutySchedule->role_group ?? '') }}"
               class="w-full rounded border-gray-300"
               placeholder="เช่น พยาบาล, คนขับรถ, ห้องบัตร, IT">

        @error('role_group')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">สถานะ</label>

        <select name="status" class="w-full rounded border-gray-300">
            <option value="assigned" @selected(old('status', $dutySchedule->status ?? 'assigned') === 'assigned')>
                มอบหมายแล้ว
            </option>
            <option value="confirmed" @selected(old('status', $dutySchedule->status ?? '') === 'confirmed')>
                ยืนยันแล้ว
            </option>
            <option value="cancelled" @selected(old('status', $dutySchedule->status ?? '') === 'cancelled')>
                ยกเลิก
            </option>
        </select>

        @error('status')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label class="mb-1 block font-medium">หมายเหตุ</label>

        <textarea name="remark"
                  rows="3"
                  class="w-full rounded border-gray-300">{{ old('remark', $dutySchedule->remark ?? '') }}</textarea>

        @error('remark')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>
