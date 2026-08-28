<div class="grid gap-5 sm:grid-cols-2">
    <x-form.field label="บุคลากร" name="employee_id" required>
        <x-form.select name="employee_id">
            <option value="">— เลือกบุคลากร —</option>

            @foreach ($employees as $employee)
                <option value="{{ $employee->id }}"
                    @selected(old('employee_id', $dutySchedule->employee_id ?? '') == $employee->id)>
                    {{ $employee->employee_code }} — {{ $employee->full_name }}
                </option>
            @endforeach
        </x-form.select>
    </x-form.field>

    <x-form.field label="หน่วยงาน" name="department_id" hint="เว้นว่างเพื่อใช้ตามข้อมูลบุคลากร">
        <x-form.select name="department_id">
            <option value="">— ใช้ตามข้อมูลบุคลากร —</option>

            @foreach ($departments as $department)
                <option value="{{ $department->id }}"
                    @selected(old('department_id', $dutySchedule->department_id ?? '') == $department->id)>
                    {{ $department->code }} — {{ $department->name }}
                </option>
            @endforeach
        </x-form.select>
    </x-form.field>

    <x-form.field label="วันที่เข้าเวร" name="work_date" required>
        {{-- New rows default to today, as they did before the rewrite. --}}
        <x-form.input type="date" name="work_date"
                      :value="isset($dutySchedule) && $dutySchedule->work_date
                          ? $dutySchedule->work_date->format('Y-m-d')
                          : now()->format('Y-m-d')" />
    </x-form.field>

    <x-form.field label="ประเภทเวร" name="shift_type_id" required>
        <x-form.select name="shift_type_id">
            <option value="">— เลือกประเภทเวร —</option>

            @foreach ($shiftTypes as $shiftType)
                <option value="{{ $shiftType->id }}"
                    @selected(old('shift_type_id', $dutySchedule->shift_type_id ?? '') == $shiftType->id)>
                    {{ $shiftType->code }} — {{ $shiftType->name }}
                </option>
            @endforeach
        </x-form.select>
    </x-form.field>

    <x-form.field label="กลุ่มงาน / บทบาท" name="role_group">
        <x-form.input name="role_group" :value="$dutySchedule->role_group ?? ''" placeholder="เช่น พยาบาล, เวรเปล" />
    </x-form.field>

    <x-form.field label="สถานะ" name="status">
        @php
            $statuses = ['assigned' => 'จัดแล้ว', 'confirmed' => 'ยืนยันแล้ว', 'cancelled' => 'ยกเลิก'];
        @endphp

        <x-form.select name="status">
            @foreach ($statuses as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $dutySchedule->status ?? 'assigned') === $value)>
                    {{ $label }}
                </option>
            @endforeach
        </x-form.select>
    </x-form.field>

    <x-form.field label="หมายเหตุ" name="remark" class="sm:col-span-2">
        <x-form.textarea name="remark" :value="$dutySchedule->remark ?? ''" rows="3" />
    </x-form.field>
</div>
