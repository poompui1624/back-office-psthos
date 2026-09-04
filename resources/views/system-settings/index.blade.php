{{--
    Uses <x-layouts.app>, the shell every other page uses. This was the only
    file in the project on <x-layouts::app>, which resolves to the starter
    kit's separate layout — its own sidebar, its own header — which is why the
    page looked like it belonged to a different application.
--}}
<x-layouts.app title="ตั้งค่าระบบ">
    <x-page-header title="ตั้งค่าระบบ"
                   subtitle="กำหนดค่าพื้นฐานของระบบ Back-office โรงพยาบาล และระบบงานที่เปิดใช้งาน">
        <x-btn :href="route('dashboard')" variant="secondary" icon="dashboard">Dashboard</x-btn>
    </x-page-header>

    <div class="space-y-6">
        {{-- System status --}}
        @php
            $modules = [
                ['label' => 'ทะเบียนบุคลากร', 'permission' => 'employee.view', 'route' => 'employees.index', 'model' => \App\Models\Employee::class, 'action' => 'จัดการบุคลากร'],
                ['label' => 'คำขอลา', 'permission' => 'leave.view', 'route' => 'leave-requests.index', 'model' => \App\Models\LeaveRequest::class, 'action' => 'จัดการการลา'],
                ['label' => 'สรุปเวลาทำงาน', 'permission' => 'attendance.view', 'route' => 'attendance-summaries.index', 'model' => \App\Models\AttendanceDailySummary::class, 'action' => 'ดูสรุปเวลา'],
                ['label' => 'ตารางเวร', 'permission' => 'duty.view', 'route' => 'duty-schedules.index', 'model' => \App\Models\DutySchedule::class, 'action' => 'จัดตารางเวร'],
                ['label' => 'รอบเงินเดือน', 'permission' => 'payroll.view', 'route' => 'payroll-periods.index', 'model' => \App\Models\PayrollPeriod::class, 'action' => 'จัดการเงินเดือน'],
                ['label' => 'จองห้องประชุม', 'permission' => 'meeting.view', 'route' => 'meeting-bookings.index', 'model' => \App\Models\MeetingBooking::class, 'action' => 'ดูรายการจอง'],
                ['label' => 'แจ้งซ่อม', 'permission' => 'repair.view', 'route' => 'repair-requests.index', 'model' => \App\Models\RepairRequest::class, 'action' => 'ดูงานซ่อม'],
                ['label' => 'ทะเบียนพัสดุ', 'permission' => 'asset.view', 'route' => 'assets.index', 'model' => \App\Models\Asset::class, 'action' => 'จัดการพัสดุ'],
            ];

            $visibleModules = collect($modules)->filter(
                fn (array $module) => auth()->user()?->can($module['permission'])
                    && Route::has($module['route'])
                    && class_exists($module['model'])
            );
        @endphp

        @if ($visibleModules->isNotEmpty())
            <section class="card card-pad">
                <div class="mb-5">
                    <h2 class="section-title">สถานะระบบที่เปิดใช้งาน</h2>
                    <p class="muted mt-1">ภาพรวมโมดูลหลักที่มีในระบบตอนนี้</p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($visibleModules as $module)
                        <a href="{{ route($module['route']) }}"
                           class="group rounded-xl border border-slate-200 p-4 transition hover:-translate-y-0.5 hover:border-brand-300 hover:bg-brand-50/50">
                            <div class="text-sm text-slate-500">{{ $module['label'] }}</div>

                            <div class="mt-2 text-2xl font-bold text-slate-900">
                                {{ number_format($module['model']::count()) }}
                            </div>

                            <div class="mt-1 flex items-center gap-1 text-sm font-medium text-brand-600">
                                {{ $module['action'] }}
                                <x-icon name="chevron-right" class="h-3.5 w-3.5 transition group-hover:translate-x-0.5" />
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Guide --}}
        <section class="card card-pad">
            <div class="mb-5">
                <h2 class="section-title">หมวดการตั้งค่าที่ควรมี</h2>
                <p class="muted mt-1">ใช้เป็นแนวทางตรวจสอบว่าระบบมีค่าพื้นฐานครบหรือยัง</p>
            </div>

            @php
                $guide = [
                    'ทั่วไป' => 'ชื่อโรงพยาบาล, ที่อยู่, เบอร์โทร, โลโก้',
                    'ระบบลา' => 'ปีงบประมาณ, จำนวนวันลา, การอนุมัติ',
                    'เวลาทำงาน' => 'เวลาเข้างาน, เวลาเลิกงาน, สายได้กี่นาที',
                    'ตารางเวร' => 'ประเภทเวร, เวรข้ามวัน, การสร้างเวรหลายรายการ',
                    'เงินเดือน' => 'หักมาสาย, หักกลับก่อน, หักขาดงาน',
                    'จองห้องประชุม' => 'อนุมัติการจอง, ตรวจเวลาชน, อุปกรณ์ประจำห้อง',
                ];
            @endphp

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($guide as $heading => $detail)
                    <div class="rounded-xl border border-slate-200 p-4">
                        <div class="font-semibold text-slate-900">{{ $heading }}</div>
                        <div class="mt-1 text-sm text-slate-500">{{ $detail }}</div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Settings --}}
        <form method="POST" action="{{ route('system-settings.update') }}"
              enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

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
            @endphp

            @forelse ($settings as $group => $items)
                <section class="card card-pad">
                    <div class="mb-5 flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <h2 class="section-title">{{ $groupLabels[$group] ?? strtoupper($group) }}</h2>
                            <p class="muted mt-1">กลุ่มค่า: {{ $group }}</p>
                        </div>

                        <x-badge tone="slate">{{ count($items) }} รายการ</x-badge>
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        @foreach ($items as $setting)
                            @php
                                $field = 'settings[' . $setting->key . ']';
                                $current = old('settings.' . $setting->key, $setting->value);
                                $error = $errors->first('settings.' . $setting->key)
                                    ?: $errors->first('setting_files.' . $setting->key);
                            @endphp

                            <x-form.field :label="$setting->label" :error="$error"
                                          :hint="$setting->description ?: 'key: ' . $setting->key">
                                @if ($setting->type === 'textarea')
                                    <x-form.textarea :name="$field" :value="$current" rows="3" />

                                @elseif ($setting->type === 'number')
                                    <x-form.input type="number" :name="$field" :value="$current" />

                                @elseif ($setting->type === 'boolean')
                                    <x-form.select :name="$field">
                                        <option value="1" @selected($current == 1)>เปิดใช้งาน</option>
                                        <option value="0" @selected($current == 0)>ปิดใช้งาน</option>
                                    </x-form.select>

                                @elseif ($setting->type === 'date')
                                    <x-form.input type="date" :name="$field" :value="$current" />

                                @elseif ($setting->type === 'time')
                                    <x-form.input type="time" :name="$field" :value="$current" />

                                @elseif ($setting->type === 'image')
                                    @if ($setting->value)
                                        <div class="mb-3 rounded-xl border border-slate-200 bg-slate-50 p-3">
                                            <div class="mb-2 text-sm text-slate-500">รูปปัจจุบัน</div>

                                            <img src="{{ asset('storage/' . $setting->value) }}"
                                                 alt="{{ $setting->label }}"
                                                 class="max-h-24 rounded-lg bg-white p-2 shadow-sm">
                                        </div>
                                    @endif

                                    <input type="file"
                                           name="setting_files[{{ $setting->key }}]"
                                           accept="image/png,image/jpeg,image/jpg,image/webp"
                                           class="w-full rounded-xl border border-slate-200 bg-white p-2 text-sm shadow-sm file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-slate-700">

                                    <input type="hidden" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}">

                                    <p class="mt-1.5 text-xs text-slate-400">รองรับ PNG, JPG, JPEG, WEBP ขนาดไม่เกิน 2MB</p>

                                @else
                                    <x-form.input :name="$field" :value="$current" />
                                @endif
                            </x-form.field>
                        @endforeach
                    </div>
                </section>
            @empty
                <div class="card">
                    <x-empty-state icon="cog" title="ยังไม่มีข้อมูลการตั้งค่าในระบบ"
                                   description="ค่าตั้งต้นจะถูกสร้างเมื่อรัน SystemSettingSeeder" />
                </div>
            @endforelse

            <div class="sticky bottom-0 -mx-4 border-t border-slate-200 bg-slate-100/95 px-4 py-4 backdrop-blur sm:-mx-6 sm:px-6">
                <div class="flex flex-wrap gap-2">
                    @can('setting.update')
                        <x-btn type="submit">บันทึกการตั้งค่า</x-btn>
                    @endcan

                    <x-btn :href="route('dashboard')" variant="secondary">กลับ Dashboard</x-btn>
                </div>
            </div>
        </form>
    </div>
</x-layouts.app>
