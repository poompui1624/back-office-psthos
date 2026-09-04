<div class="grid gap-5 sm:grid-cols-2">
    <x-form.field label="บุคลากร" name="employee_id" required>
        <x-form.select name="employee_id">
            <option value="">— เลือกบุคลากร —</option>

            @foreach ($employees as $employee)
                <option value="{{ $employee->id }}"
                    @selected(old('employee_id', $leaveRequest->employee_id ?? '') == $employee->id)>
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
                    @selected(old('department_id', $leaveRequest->department_id ?? '') == $department->id)>
                    {{ $department->code }} — {{ $department->name }}
                </option>
            @endforeach
        </x-form.select>
    </x-form.field>

    <x-form.field label="ประเภทการลา" name="leave_type_id" required>
        <x-form.select name="leave_type_id">
            <option value="">— เลือกประเภทการลา —</option>

            @foreach ($leaveTypes as $leaveType)
                <option value="{{ $leaveType->id }}"
                    @selected(old('leave_type_id', $leaveRequest->leave_type_id ?? '') == $leaveType->id)>
                    {{ $leaveType->code }} — {{ $leaveType->name }}
                </option>
            @endforeach
        </x-form.select>
    </x-form.field>

    <x-form.field label="จำนวนวันลา" name="total_days" required hint="คำนวณให้อัตโนมัติเมื่อเลือกช่วงวันที่">
        <x-form.input type="number" step="0.5" min="0.5" name="total_days"
                      :value="$leaveRequest->total_days ?? 1" />
    </x-form.field>

    <x-form.field label="วันที่เริ่มลา" name="start_date" required>
        <x-form.input type="date" name="start_date"
                      :value="isset($leaveRequest) && $leaveRequest->start_date
                          ? $leaveRequest->start_date->format('Y-m-d')
                          : ''" />
    </x-form.field>

    <x-form.field label="วันที่สิ้นสุดการลา" name="end_date" required>
        <x-form.input type="date" name="end_date"
                      :value="isset($leaveRequest) && $leaveRequest->end_date
                          ? $leaveRequest->end_date->format('Y-m-d')
                          : ''" />
    </x-form.field>

    @php
        $periods = ['full' => 'เต็มวัน', 'morning' => 'ช่วงเช้า', 'afternoon' => 'ช่วงบ่าย'];
    @endphp

    <x-form.field label="ช่วงเริ่มลา" name="start_period">
        <x-form.select name="start_period">
            @foreach ($periods as $value => $label)
                <option value="{{ $value }}"
                    @selected(old('start_period', $leaveRequest->start_period ?? 'full') === $value)>{{ $label }}</option>
            @endforeach
        </x-form.select>
    </x-form.field>

    <x-form.field label="ช่วงสิ้นสุดการลา" name="end_period">
        <x-form.select name="end_period">
            @foreach ($periods as $value => $label)
                <option value="{{ $value }}"
                    @selected(old('end_period', $leaveRequest->end_period ?? 'full') === $value)>{{ $label }}</option>
            @endforeach
        </x-form.select>
    </x-form.field>

    <x-form.field label="เหตุผลการลา" name="reason" class="sm:col-span-2">
        <x-form.textarea name="reason" :value="$leaveRequest->reason ?? ''" rows="3" />
    </x-form.field>

    <x-form.field label="ติดต่อระหว่างลา" name="contact_during_leave" class="sm:col-span-2">
        <x-form.input name="contact_during_leave" :value="$leaveRequest->contact_during_leave ?? ''"
                      placeholder="เบอร์โทร หรือช่องทางติดต่อ" />
    </x-form.field>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const startDate = document.getElementById('start_date');
        const endDate = document.getElementById('end_date');
        const startPeriod = document.getElementById('start_period');
        const endPeriod = document.getElementById('end_period');
        const totalDays = document.getElementById('total_days');

        function calculateDays() {
            if (!startDate.value || !endDate.value) {
                return;
            }

            const start = new Date(startDate.value);
            const end = new Date(endDate.value);

            if (end < start) {
                totalDays.value = 0.5;
                return;
            }

            const diffTime = end.getTime() - start.getTime();
            let days = Math.floor(diffTime / (1000 * 60 * 60 * 24)) + 1;

            if (startDate.value === endDate.value) {
                if (startPeriod.value !== 'full' || endPeriod.value !== 'full') {
                    days = 0.5;
                }
            } else {
                if (startPeriod.value === 'afternoon') {
                    days -= 0.5;
                }

                if (endPeriod.value === 'morning') {
                    days -= 0.5;
                }
            }

            if (days < 0.5) {
                days = 0.5;
            }

            totalDays.value = days;
        }

        startDate.addEventListener('change', calculateDays);
        endDate.addEventListener('change', calculateDays);
        startPeriod.addEventListener('change', calculateDays);
        endPeriod.addEventListener('change', calculateDays);
    });
</script>
