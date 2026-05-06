<x-layouts::app :title="__('ตั้งค่าระบบ')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
        {{-- Header --}}
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    ตั้งค่าระบบ
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    กำหนดค่าพื้นฐานของระบบ Back-office โรงพยาบาล และระบบงานที่เปิดใช้งาน
                </p>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('dashboard') }}"
                   class="rounded bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">
                    Dashboard
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="rounded-lg bg-green-100 px-4 py-3 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-lg bg-red-100 px-4 py-3 text-red-800">
                {{ session('error') }}
            </div>
        @endif

        {{-- System Status --}}
        <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <div class="mb-5">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                    สถานะระบบที่เปิดใช้งาน
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    ภาพรวมโมดูลหลักที่มีในระบบตอนนี้
                </p>
            </div>

            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                @can('employee.view')
                    @if (Route::has('employees.index'))
                        <a href="{{ route('employees.index') }}"
                           class="rounded-lg border border-gray-200 p-4 transition hover:border-blue-500 hover:bg-blue-50 dark:border-neutral-700 dark:hover:bg-neutral-800">
                            <div class="text-sm text-gray-500 dark:text-gray-400">ทะเบียนบุคลากร</div>
                            <div class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">
                                {{ class_exists(\App\Models\Employee::class) ? \App\Models\Employee::count() : 0 }}
                            </div>
                            <div class="mt-1 text-sm text-blue-600">จัดการบุคลากร →</div>
                        </a>
                    @endif
                @endcan

                @can('leave.view')
                    @if (Route::has('leave-requests.index') && class_exists(\App\Models\LeaveRequest::class))
                        <a href="{{ route('leave-requests.index') }}"
                           class="rounded-lg border border-gray-200 p-4 transition hover:border-blue-500 hover:bg-blue-50 dark:border-neutral-700 dark:hover:bg-neutral-800">
                            <div class="text-sm text-gray-500 dark:text-gray-400">ระบบการลา</div>
                            <div class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">
                                {{ \App\Models\LeaveRequest::count() }}
                            </div>
                            <div class="mt-1 text-sm text-blue-600">คำขอลาทั้งหมด →</div>
                        </a>
                    @endif
                @endcan

                @can('attendance.view')
                    @if (Route::has('attendance-summaries.index') && class_exists(\App\Models\AttendanceDailySummary::class))
                        <a href="{{ route('attendance-summaries.index') }}"
                           class="rounded-lg border border-gray-200 p-4 transition hover:border-blue-500 hover:bg-blue-50 dark:border-neutral-700 dark:hover:bg-neutral-800">
                            <div class="text-sm text-gray-500 dark:text-gray-400">เวลาทำงาน</div>
                            <div class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">
                                {{ \App\Models\AttendanceDailySummary::count() }}
                            </div>
                            <div class="mt-1 text-sm text-blue-600">สรุปเวลาทำงาน →</div>
                        </a>
                    @endif
                @endcan

                @can('duty.view')
                    @if (Route::has('duty-schedules.index') && class_exists(\App\Models\DutySchedule::class))
                        <a href="{{ route('duty-schedules.index') }}"
                           class="rounded-lg border border-gray-200 p-4 transition hover:border-blue-500 hover:bg-blue-50 dark:border-neutral-700 dark:hover:bg-neutral-800">
                            <div class="text-sm text-gray-500 dark:text-gray-400">ตารางเวร</div>
                            <div class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">
                                {{ \App\Models\DutySchedule::count() }}
                            </div>
                            <div class="mt-1 text-sm text-blue-600">จัดการตารางเวร →</div>
                        </a>
                    @endif
                @endcan

                @can('payroll.view')
                    @if (Route::has('payroll-periods.index') && class_exists(\App\Models\PayrollPeriod::class))
                        <a href="{{ route('payroll-periods.index') }}"
                           class="rounded-lg border border-gray-200 p-4 transition hover:border-blue-500 hover:bg-blue-50 dark:border-neutral-700 dark:hover:bg-neutral-800">
                            <div class="text-sm text-gray-500 dark:text-gray-400">เงินเดือน / สลิป</div>
                            <div class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">
                                {{ \App\Models\PayrollPeriod::count() }}
                            </div>
                            <div class="mt-1 text-sm text-blue-600">รอบเงินเดือน →</div>
                        </a>
                    @endif
                @endcan

                @can('meeting.view')
                    @if (Route::has('meeting-bookings.index') && class_exists(\App\Models\MeetingBooking::class))
                        <a href="{{ route('meeting-bookings.index') }}"
                           class="rounded-lg border border-gray-200 p-4 transition hover:border-blue-500 hover:bg-blue-50 dark:border-neutral-700 dark:hover:bg-neutral-800">
                            <div class="text-sm text-gray-500 dark:text-gray-400">จองห้องประชุม</div>
                            <div class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">
                                {{ \App\Models\MeetingBooking::count() }}
                            </div>
                            <div class="mt-1 text-sm text-blue-600">รายการจอง →</div>
                        </a>
                    @endif
                @endcan

                @can('repair.view')
                    @if (Route::has('repair-requests.index') && class_exists(\App\Models\RepairRequest::class))
                        <a href="{{ route('repair-requests.index') }}"
                           class="rounded-lg border border-gray-200 p-4 transition hover:border-blue-500 hover:bg-blue-50 dark:border-neutral-700 dark:hover:bg-neutral-800">
                            <div class="text-sm text-gray-500 dark:text-gray-400">แจ้งซ่อม</div>
                            <div class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">
                                {{ \App\Models\RepairRequest::count() }}
                            </div>
                            <div class="mt-1 text-sm text-blue-600">รายการแจ้งซ่อม →</div>
                        </a>
                    @endif
                @endcan

                @can('asset.view')
                    @if (Route::has('assets.index') && class_exists(\App\Models\Asset::class))
                        <a href="{{ route('assets.index') }}"
                           class="rounded-lg border border-gray-200 p-4 transition hover:border-blue-500 hover:bg-blue-50 dark:border-neutral-700 dark:hover:bg-neutral-800">
                            <div class="text-sm text-gray-500 dark:text-gray-400">ทะเบียนพัสดุ</div>
                            <div class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">
                                {{ \App\Models\Asset::count() }}
                            </div>
                            <div class="mt-1 text-sm text-blue-600">ทะเบียนพัสดุ →</div>
                        </a>
                    @endif
                @endcan
            </div>
        </div>

        {{-- Quick Settings Guide --}}
        <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <div class="mb-5">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                    หมวดการตั้งค่าที่ควรมี
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    ใช้เป็นแนวทางตรวจสอบว่าระบบมีค่าพื้นฐานครบหรือยัง
                </p>
            </div>

            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <div class="rounded-lg border border-gray-200 p-4 dark:border-neutral-700">
                    <div class="font-semibold text-gray-900 dark:text-white">ทั่วไป</div>
                    <div class="mt-1 text-sm text-gray-500">ชื่อโรงพยาบาล, ที่อยู่, เบอร์โทร, โลโก้</div>
                </div>

                <div class="rounded-lg border border-gray-200 p-4 dark:border-neutral-700">
                    <div class="font-semibold text-gray-900 dark:text-white">ระบบลา</div>
                    <div class="mt-1 text-sm text-gray-500">ปีงบประมาณ, จำนวนวันลา, การอนุมัติ</div>
                </div>

                <div class="rounded-lg border border-gray-200 p-4 dark:border-neutral-700">
                    <div class="font-semibold text-gray-900 dark:text-white">เวลาทำงาน</div>
                    <div class="mt-1 text-sm text-gray-500">เวลาเข้างาน, เวลาเลิกงาน, สายได้กี่นาที</div>
                </div>

                <div class="rounded-lg border border-gray-200 p-4 dark:border-neutral-700">
                    <div class="font-semibold text-gray-900 dark:text-white">ตารางเวร</div>
                    <div class="mt-1 text-sm text-gray-500">ประเภทเวร, เวรข้ามวัน, การสร้างเวรหลายรายการ</div>
                </div>

                <div class="rounded-lg border border-gray-200 p-4 dark:border-neutral-700">
                    <div class="font-semibold text-gray-900 dark:text-white">เงินเดือน</div>
                    <div class="mt-1 text-sm text-gray-500">หักมาสาย, หักกลับก่อน, หักขาดงาน</div>
                </div>

                <div class="rounded-lg border border-gray-200 p-4 dark:border-neutral-700">
                    <div class="font-semibold text-gray-900 dark:text-white">จองห้องประชุม</div>
                    <div class="mt-1 text-sm text-gray-500">อนุมัติการจอง, ตรวจเวลาชน, อุปกรณ์ประจำห้อง</div>
                </div>
            </div>
        </div>

        {{-- Setting Form --}}
        <form method="POST"
              action="{{ route('system-settings.update') }}"
              enctype="multipart/form-data"
              class="space-y-6">
            @csrf
            @method('PUT')

            @forelse ($settings as $group => $items)
                @php
                    $groupLabels = [
                        'general' => 'ข้อมูลทั่วไป',
                        'hospital' => 'ข้อมูลโรงพยาบาล',
                        'leave' => 'ระบบการลา',
                        'attendance' => 'ระบบเวลาทำงาน',
                        'duty' => 'ตารางเวร',
                        'payroll' => 'เงินเดือน / สลิปเงินเดือน',
                        'meeting' => 'จองห้องประชุม',
                        'repair' => 'ระบบแจ้งซ่อม',
                        'asset' => 'ทะเบียนพัสดุ',
                        'computer' => 'ทะเบียนคอมพิวเตอร์',
                        'software' => 'ทะเบียน Software',
                        'vehicle' => 'ระบบใช้รถ',
                        'notification' => 'การแจ้งเตือน',
                        'security' => 'ความปลอดภัย',
                    ];

                    $groupName = $groupLabels[$group] ?? strtoupper($group);
                @endphp

                <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                    <div class="mb-5 flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                                {{ $groupName }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                กลุ่มค่า: {{ $group }}
                            </p>
                        </div>

                        <span class="rounded bg-gray-100 px-2 py-1 text-xs text-gray-600 dark:bg-neutral-800 dark:text-gray-300">
                            {{ count($items) }} รายการ
                        </span>
                    </div>

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        @foreach ($items as $setting)
                            <div>
                                <label class="mb-1 block font-medium text-gray-900 dark:text-white">
                                    {{ $setting->label }}
                                </label>

                                @if ($setting->type === 'textarea')
                                    <textarea name="settings[{{ $setting->key }}]"
                                              rows="3"
                                              class="w-full rounded border-gray-300 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white">{{ old('settings.' . $setting->key, $setting->value) }}</textarea>

                                @elseif ($setting->type === 'number')
                                    <input type="number"
                                           name="settings[{{ $setting->key }}]"
                                           value="{{ old('settings.' . $setting->key, $setting->value) }}"
                                           class="w-full rounded border-gray-300 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white">

                                @elseif ($setting->type === 'boolean')
                                    <select name="settings[{{ $setting->key }}]"
                                            class="w-full rounded border-gray-300 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white">
                                        <option value="1" @selected(old('settings.' . $setting->key, $setting->value) == 1)>
                                            เปิดใช้งาน
                                        </option>
                                        <option value="0" @selected(old('settings.' . $setting->key, $setting->value) == 0)>
                                            ปิดใช้งาน
                                        </option>
                                    </select>

                                @elseif ($setting->type === 'date')
                                    <input type="date"
                                           name="settings[{{ $setting->key }}]"
                                           value="{{ old('settings.' . $setting->key, $setting->value) }}"
                                           class="w-full rounded border-gray-300 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white">

                                @elseif ($setting->type === 'time')
                                    <input type="time"
                                           name="settings[{{ $setting->key }}]"
                                           value="{{ old('settings.' . $setting->key, $setting->value) }}"
                                           class="w-full rounded border-gray-300 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white">

                                @elseif ($setting->type === 'image')
                                    @if ($setting->value)
                                        <div class="mb-3 rounded border border-gray-200 bg-gray-50 p-3 dark:border-neutral-700 dark:bg-neutral-800">
                                            <div class="mb-2 text-sm text-gray-500 dark:text-gray-400">
                                                รูปปัจจุบัน
                                            </div>

                                            <img src="{{ asset('storage/' . $setting->value) }}"
                                                 alt="{{ $setting->label }}"
                                                 class="max-h-24 rounded bg-white p-2 shadow">
                                        </div>
                                    @endif

                                    <input type="file"
                                           name="setting_files[{{ $setting->key }}]"
                                           accept="image/png,image/jpeg,image/jpg,image/webp"
                                           class="w-full rounded border border-gray-300 bg-white p-2 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white">

                                    <input type="hidden"
                                           name="settings[{{ $setting->key }}]"
                                           value="{{ $setting->value }}">

                                    <p class="mt-1 text-xs text-gray-400">
                                        รองรับ PNG, JPG, JPEG, WEBP ขนาดไม่เกิน 2MB
                                    </p>

                                @else
                                    <input type="text"
                                           name="settings[{{ $setting->key }}]"
                                           value="{{ old('settings.' . $setting->key, $setting->value) }}"
                                           class="w-full rounded border-gray-300 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white">
                                @endif

                                @if ($setting->description)
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $setting->description }}
                                    </p>
                                @endif

                                <p class="mt-1 text-xs text-gray-400">
                                    key: {{ $setting->key }}
                                </p>

                                @error('settings.' . $setting->key)
                                    <p class="mt-1 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror

                                @error('setting_files.' . $setting->key)
                                    <p class="mt-1 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="rounded-xl border border-dashed border-gray-300 bg-white p-8 text-center text-gray-500 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                    ยังไม่มีข้อมูลการตั้งค่าในระบบ
                </div>
            @endforelse

            <div class="sticky bottom-0 flex gap-2 border-t border-gray-200 bg-gray-100 py-4 dark:border-neutral-700 dark:bg-neutral-950">
                @can('setting.update')
                    <button type="submit"
                            class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                        บันทึกการตั้งค่า
                    </button>
                @endcan

                <a href="{{ route('dashboard') }}"
                   class="rounded bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">
                    กลับ Dashboard
                </a>
            </div>
        </form>
    </div>
</x-layouts::app>
