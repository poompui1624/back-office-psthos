<x-layouts.app title="สร้างสรุปเวลาทำงาน">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">สร้างสรุปเวลาทำงาน</h1>
        <p class="text-sm text-gray-600">
            ระบบจะใช้เวลา scan แรกของวันเป็นเวลาเข้า และ scan สุดท้ายของวันเป็นเวลาออก
        </p>
    </div>

    @if (session('error'))
        <div class="mb-4 rounded bg-red-100 px-4 py-3 text-red-800">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded bg-white p-6 shadow">
            <h2 class="mb-4 text-lg font-bold">กำหนดช่วงวันที่</h2>

            <form method="POST" action="{{ route('attendance-summaries.generate') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="mb-1 block font-medium">
                        วันที่เริ่ม <span class="text-red-600">*</span>
                    </label>

                    <input type="date"
                           name="date_from"
                           value="{{ old('date_from', now()->startOfMonth()->format('Y-m-d')) }}"
                           class="w-full rounded border-gray-300">

                    @error('date_from')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1 block font-medium">
                        วันที่สิ้นสุด <span class="text-red-600">*</span>
                    </label>

                    <input type="date"
                           name="date_to"
                           value="{{ old('date_to', now()->format('Y-m-d')) }}"
                           class="w-full rounded border-gray-300">

                    @error('date_to')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block font-medium">
                            เวลาเข้างานปกติ <span class="text-red-600">*</span>
                        </label>

                        <input type="time"
                               name="expected_in_time"
                               value="{{ old('expected_in_time', '08:30') }}"
                               class="w-full rounded border-gray-300">

                        @error('expected_in_time')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block font-medium">
                            เวลาเลิกงานปกติ <span class="text-red-600">*</span>
                        </label>

                        <input type="time"
                               name="expected_out_time"
                               value="{{ old('expected_out_time', '16:30') }}"
                               class="w-full rounded border-gray-300">

                        @error('expected_out_time')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="rounded bg-blue-50 p-4 text-sm text-blue-900">
                    <label class="flex items-start gap-2">
                        <input type="checkbox"
                            name="use_duty_schedule"
                            value="1"
                            @checked(old('use_duty_schedule', true))
                            class="mt-1">

                        <span>
                            ใช้เวลาจากตารางเวรแทนเวลาเข้าออกด้านบน
                            <br>
                            <span class="text-blue-700">
                                ถ้าเลือกตัวนี้ ระบบจะใช้เวลาเริ่ม-สิ้นสุดจาก /duty-schedules
                                เช่น เวรเช้า เวรบ่าย เวรดึก
                            </span>
                        </span>
                    </label>
                </div>

                <div class="rounded bg-yellow-50 p-4 text-sm text-yellow-900">
                    หากกดสร้างซ้ำในช่วงวันที่เดิม ระบบจะอัปเดตข้อมูลสรุปเดิม ไม่สร้างซ้ำ
                </div>

                <div class="flex gap-2">
                    <button type="submit"
                            class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
                            onclick="return confirm('ยืนยันการสร้างสรุปเวลาทำงาน?')">
                        สร้างสรุป
                    </button>

                    <a href="{{ route('attendance-summaries.index') }}"
                       class="rounded bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">
                        ย้อนกลับ
                    </a>
                </div>
            </form>
        </div>

        <div class="rounded bg-white p-6 shadow">
            <h2 class="mb-4 text-lg font-bold">วิธีคำนวณ</h2>

            <div class="space-y-4 text-sm text-gray-700">
                <div class="rounded border border-gray-200 p-4">
                    <div class="font-semibold">เวลาเข้า</div>
                    <div class="mt-1 text-gray-600">
                        ใช้เวลาสแกนครั้งแรกของบุคลากรในวันนั้น
                    </div>
                </div>

                <div class="rounded border border-gray-200 p-4">
                    <div class="font-semibold">เวลาออก</div>
                    <div class="mt-1 text-gray-600">
                        ใช้เวลาสแกนครั้งสุดท้ายของบุคลากรในวันนั้น
                    </div>
                </div>

                <div class="rounded border border-gray-200 p-4">
                    <div class="font-semibold">มาสาย</div>
                    <div class="mt-1 text-gray-600">
                        ถ้าเวลาเข้า มากกว่าเวลาเข้างานปกติ ระบบจะคำนวณจำนวนนาทีที่มาสาย
                    </div>
                </div>

                <div class="rounded border border-gray-200 p-4">
                    <div class="font-semibold">กลับก่อน</div>
                    <div class="mt-1 text-gray-600">
                        ถ้าเวลาออก น้อยกว่าเวลาเลิกงานปกติ ระบบจะคำนวณจำนวนนาทีที่กลับก่อน
                    </div>
                </div>

                <div class="rounded border border-gray-200 p-4">
                    <div class="font-semibold">ข้อมูลไม่ครบ</div>
                    <div class="mt-1 text-gray-600">
                        ถ้ามีเวลาสแกนเพียงครั้งเดียว ระบบจะระบุสถานะเป็นข้อมูลไม่ครบ
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
