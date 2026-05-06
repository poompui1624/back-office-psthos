<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <div>
        <label class="mb-1 block font-medium">
            บุคลากร <span class="text-red-600">*</span>
        </label>

        <select name="employee_id" class="w-full rounded border-gray-300">
            <option value="">-- เลือกบุคลากร --</option>
            @foreach ($employees as $employee)
                <option value="{{ $employee->id }}"
                    @selected(old('employee_id', $leaveRequest->employee_id ?? '') == $employee->id)>
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
                    @selected(old('department_id', $leaveRequest->department_id ?? '') == $department->id)>
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
            ประเภทการลา <span class="text-red-600">*</span>
        </label>

        <select name="leave_type_id" class="w-full rounded border-gray-300">
            <option value="">-- เลือกประเภทการลา --</option>
            @foreach ($leaveTypes as $leaveType)
                <option value="{{ $leaveType->id }}"
                    @selected(old('leave_type_id', $leaveRequest->leave_type_id ?? '') == $leaveType->id)>
                    {{ $leaveType->code }} - {{ $leaveType->name }}
                </option>
            @endforeach
        </select>

        @error('leave_type_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">
            จำนวนวันลา <span class="text-red-600">*</span>
        </label>

        <input type="number"
               step="0.5"
               min="0.5"
               name="total_days"
               id="total_days"
               value="{{ old('total_days', $leaveRequest->total_days ?? 1) }}"
               class="w-full rounded border-gray-300">

        @error('total_days')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">
            วันที่เริ่มลา <span class="text-red-600">*</span>
        </label>

        <input type="date"
               name="start_date"
               id="start_date"
               value="{{ old('start_date', isset($leaveRequest) && $leaveRequest->start_date ? $leaveRequest->start_date->format('Y-m-d') : '') }}"
               class="w-full rounded border-gray-300">

        @error('start_date')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">
            ถึงวันที่ <span class="text-red-600">*</span>
        </label>

        <input type="date"
               name="end_date"
               id="end_date"
               value="{{ old('end_date', isset($leaveRequest) && $leaveRequest->end_date ? $leaveRequest->end_date->format('Y-m-d') : '') }}"
               class="w-full rounded border-gray-300">

        @error('end_date')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">ช่วงเริ่มลา</label>

        <select name="start_period" id="start_period" class="w-full rounded border-gray-300">
            <option value="full" @selected(old('start_period', $leaveRequest->start_period ?? 'full') === 'full')>
                เต็มวัน
            </option>
            <option value="morning" @selected(old('start_period', $leaveRequest->start_period ?? '') === 'morning')>
                ช่วงเช้า
            </option>
            <option value="afternoon" @selected(old('start_period', $leaveRequest->start_period ?? '') === 'afternoon')>
                ช่วงบ่าย
            </option>
        </select>

        @error('start_period')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">ช่วงสิ้นสุดการลา</label>

        <select name="end_period" id="end_period" class="w-full rounded border-gray-300">
            <option value="full" @selected(old('end_period', $leaveRequest->end_period ?? 'full') === 'full')>
                เต็มวัน
            </option>
            <option value="morning" @selected(old('end_period', $leaveRequest->end_period ?? '') === 'morning')>
                ช่วงเช้า
            </option>
            <option value="afternoon" @selected(old('end_period', $leaveRequest->end_period ?? '') === 'afternoon')>
                ช่วงบ่าย
            </option>
        </select>

        @error('end_period')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label class="mb-1 block font-medium">เหตุผลการลา</label>

        <textarea name="reason"
                  rows="4"
                  class="w-full rounded border-gray-300">{{ old('reason', $leaveRequest->reason ?? '') }}</textarea>

        @error('reason')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label class="mb-1 block font-medium">ติดต่อระหว่างลา</label>

        <input type="text"
               name="contact_during_leave"
               value="{{ old('contact_during_leave', $leaveRequest->contact_during_leave ?? '') }}"
               class="w-full rounded border-gray-300"
               placeholder="เบอร์โทร / Line / ที่อยู่ระหว่างลา">

        @error('contact_during_leave')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
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
