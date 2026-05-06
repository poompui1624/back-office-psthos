<x-layouts.app title="สร้างตารางเวรหลายรายการ">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">สร้างตารางเวรหลายรายการ</h1>
            <p class="text-sm text-gray-600">
                เลือกหลายคน หลายวัน และสร้างตารางเวรในครั้งเดียว
            </p>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('duty-schedules.calendar') }}"
               class="rounded bg-gray-700 px-4 py-2 text-white hover:bg-gray-800">
                ปฏิทินเวร
            </a>

            <a href="{{ route('duty-schedules.index') }}"
               class="rounded bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">
                ย้อนกลับ
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-4 rounded bg-red-100 px-4 py-3 text-red-800">
            <div class="font-bold">กรุณาตรวจสอบข้อมูล</div>
            <ul class="mt-2 list-inside list-disc text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('duty-schedules.bulk-store') }}" class="space-y-6">
        @csrf

        <div class="rounded bg-white p-6 shadow">
            <h2 class="mb-4 text-lg font-bold">1. กำหนดช่วงวันที่</h2>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1 block font-medium">
                        วันที่เริ่ม <span class="text-red-600">*</span>
                    </label>

                    <input type="date"
                           name="date_from"
                           value="{{ old('date_from', now()->startOfMonth()->format('Y-m-d')) }}"
                           class="w-full rounded border-gray-300">
                </div>

                <div>
                    <label class="mb-1 block font-medium">
                        วันที่สิ้นสุด <span class="text-red-600">*</span>
                    </label>

                    <input type="date"
                           name="date_to"
                           value="{{ old('date_to', now()->endOfMonth()->format('Y-m-d')) }}"
                           class="w-full rounded border-gray-300">
                </div>
            </div>

            <div class="mt-4">
                <label class="mb-2 block font-medium">
                    เลือกวันในสัปดาห์ <span class="text-red-600">*</span>
                </label>

                @php
                    $weekdays = [
                        0 => 'อาทิตย์',
                        1 => 'จันทร์',
                        2 => 'อังคาร',
                        3 => 'พุธ',
                        4 => 'พฤหัส',
                        5 => 'ศุกร์',
                        6 => 'เสาร์',
                    ];

                    $oldWeekdays = old('weekdays', [1, 2, 3, 4, 5]);
                @endphp

                <div class="grid gap-2 md:grid-cols-7">
                    @foreach ($weekdays as $value => $label)
                        <label class="flex items-center gap-2 rounded border border-gray-200 p-3">
                            <input type="checkbox"
                                   name="weekdays[]"
                                   value="{{ $value }}"
                                   @checked(in_array($value, $oldWeekdays))>

                            <span>{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="rounded bg-white p-6 shadow">
            <h2 class="mb-4 text-lg font-bold">2. เลือกเวรและหน่วยงาน</h2>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1 block font-medium">
                        ประเภทเวร <span class="text-red-600">*</span>
                    </label>

                    <select name="shift_type_id" class="w-full rounded border-gray-300">
                        <option value="">-- เลือกประเภทเวร --</option>
                        @foreach ($shiftTypes as $shiftType)
                            <option value="{{ $shiftType->id }}" @selected(old('shift_type_id') == $shiftType->id)>
                                {{ $shiftType->code }} - {{ $shiftType->name }}
                                ({{ substr($shiftType->start_time, 0, 5) }}-{{ substr($shiftType->end_time, 0, 5) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-1 block font-medium">หน่วยงาน</label>

                    <select name="department_id" class="w-full rounded border-gray-300">
                        <option value="">-- ใช้ตามข้อมูลบุคลากร --</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}" @selected(old('department_id') == $department->id)>
                                {{ $department->code }} - {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-1 block font-medium">กลุ่มงาน / บทบาท</label>

                    <input type="text"
                           name="role_group"
                           value="{{ old('role_group') }}"
                           class="w-full rounded border-gray-300"
                           placeholder="เช่น พยาบาล, คนขับรถ, ห้องบัตร, IT">
                </div>

                <div>
                    <label class="mb-1 block font-medium">สถานะ</label>

                    <select name="status" class="w-full rounded border-gray-300">
                        <option value="assigned" @selected(old('status', 'assigned') === 'assigned')>
                            มอบหมายแล้ว
                        </option>
                        <option value="confirmed" @selected(old('status') === 'confirmed')>
                            ยืนยันแล้ว
                        </option>
                        <option value="cancelled" @selected(old('status') === 'cancelled')>
                            ยกเลิก
                        </option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="mb-1 block font-medium">หมายเหตุ</label>

                    <textarea name="remark"
                              rows="3"
                              class="w-full rounded border-gray-300">{{ old('remark') }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="flex items-center gap-2 rounded bg-yellow-50 p-4 text-yellow-900">
                        <input type="checkbox"
                               name="overwrite"
                               value="1"
                               @checked(old('overwrite'))>

                        <span>
                            ถ้ามีตารางเวรเดิมในวันเดียวกันและเวรเดียวกัน ให้เขียนทับ
                        </span>
                    </label>
                </div>
            </div>
        </div>

        <div class="rounded bg-white p-6 shadow">
            <h2 class="mb-4 text-lg font-bold">3. เลือกบุคลากร</h2>

            <div class="mb-3 flex gap-2">
                <button type="button"
                        onclick="toggleEmployees(true)"
                        class="rounded bg-gray-800 px-3 py-1 text-sm text-white">
                    เลือกทั้งหมด
                </button>

                <button type="button"
                        onclick="toggleEmployees(false)"
                        class="rounded bg-gray-200 px-3 py-1 text-sm text-gray-700">
                    ล้างทั้งหมด
                </button>
            </div>

            <div class="grid max-h-96 gap-2 overflow-y-auto rounded border border-gray-200 p-3 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($employees as $employee)
                    <label class="flex items-start gap-2 rounded border border-gray-200 p-3 hover:bg-gray-50">
                        <input type="checkbox"
                               name="employee_ids[]"
                               value="{{ $employee->id }}"
                               class="employee-checkbox mt-1"
                               @checked(in_array($employee->id, old('employee_ids', [])))>

                        <span>
                            <span class="block font-medium">
                                {{ $employee->employee_code }} - {{ $employee->full_name }}
                            </span>

                            <span class="block text-xs text-gray-500">
                                {{ $employee->department?->name ?? '-' }}
                            </span>
                        </span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="flex gap-2">
            <button type="submit"
                    class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
                    onclick="return confirm('ยืนยันการสร้างตารางเวรหลายรายการ?')">
                สร้างตารางเวร
            </button>

            <a href="{{ route('duty-schedules.index') }}"
               class="rounded bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">
                ย้อนกลับ
            </a>
        </div>
    </form>

    <script>
        function toggleEmployees(checked) {
            document.querySelectorAll('.employee-checkbox').forEach(function (checkbox) {
                checkbox.checked = checked;
            });
        }
    </script>
</x-layouts.app>
