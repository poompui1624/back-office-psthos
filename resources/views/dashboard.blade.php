<x-layouts.app title="Dashboard">
    @php
        $user = auth()->user();
        $hospitalName = function_exists('hospital_name') ? hospital_name() : config('app.name', 'Hospital Backoffice');

        $can = fn (string $permission): bool => $user?->can($permission) ?? false;
        $modelCount = fn (string $model): int => class_exists($model) ? $model::count() : 0;

        $pendingApprovals = class_exists(\App\Models\ApprovalRequest::class)
            ? \App\Models\ApprovalRequest::where('status', 'pending')->count()
            : 0;
        $pendingLeaveRequests = class_exists(\App\Models\LeaveRequest::class)
            ? \App\Models\LeaveRequest::where('status', 'pending')->count()
            : 0;
        $activeRepairRequests = class_exists(\App\Models\RepairRequest::class)
            ? \App\Models\RepairRequest::whereIn('status', ['new', 'in_progress'])->count()
            : 0;
        $todayLeaveRequests = class_exists(\App\Models\LeaveRequest::class)
            ? \App\Models\LeaveRequest::where('status', 'approved')
                ->whereDate('start_date', '<=', now()->toDateString())
                ->whereDate('end_date', '>=', now()->toDateString())
                ->count()
            : 0;
        $monthlyLateSummaries = class_exists(\App\Models\AttendanceDailySummary::class)
            ? \App\Models\AttendanceDailySummary::where('status', 'late')
                ->whereBetween('work_date', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])
                ->count()
            : 0;

        $overviewCards = [
            [
                'label' => 'บุคลากร',
                'value' => $modelCount(\App\Models\Employee::class),
                'href' => Route::has('employees.index') ? route('employees.index') : null,
                'permission' => 'employee.view',
                'tone' => 'border-sky-200 bg-sky-50 text-sky-900',
            ],
            [
                'label' => 'ผู้ใช้งาน',
                'value' => $modelCount(\App\Models\User::class),
                'href' => Route::has('users.index') ? route('users.index') : null,
                'permission' => 'user.view',
                'tone' => 'border-indigo-200 bg-indigo-50 text-indigo-900',
            ],
            [
                'label' => 'พัสดุ',
                'value' => $modelCount(\App\Models\Asset::class),
                'href' => Route::has('assets.index') ? route('assets.index') : null,
                'permission' => 'asset.view',
                'tone' => 'border-emerald-200 bg-emerald-50 text-emerald-900',
            ],
            [
                'label' => 'คอมพิวเตอร์',
                'value' => $modelCount(\App\Models\Computer::class),
                'href' => Route::has('computers.index') ? route('computers.index') : null,
                'permission' => 'computer.view',
                'tone' => 'border-violet-200 bg-violet-50 text-violet-900',
            ],
        ];

        $priorityItems = [
            [
                'label' => 'รายการรออนุมัติ',
                'value' => $pendingApprovals,
                'href' => Route::has('approvals.index') ? route('approvals.index') : null,
                'permission' => 'approval.view',
                'tone' => 'border-amber-200 bg-amber-50 text-amber-900',
            ],
            [
                'label' => 'คำขอลารออนุมัติ',
                'value' => $pendingLeaveRequests,
                'href' => Route::has('leave-requests.index') ? route('leave-requests.index', ['status' => 'pending']) : null,
                'permission' => 'leave.view',
                'tone' => 'border-orange-200 bg-orange-50 text-orange-900',
            ],
            [
                'label' => 'งานซ่อมที่ยังเปิดอยู่',
                'value' => $activeRepairRequests,
                'href' => Route::has('repair-requests.index') ? route('repair-requests.index') : null,
                'permission' => 'repair.view',
                'tone' => 'border-rose-200 bg-rose-50 text-rose-900',
            ],
            [
                'label' => 'บุคลากรลาวันนี้',
                'value' => $todayLeaveRequests,
                'href' => Route::has('leave-requests.calendar') ? route('leave-requests.calendar') : null,
                'permission' => 'leave.view',
                'tone' => 'border-teal-200 bg-teal-50 text-teal-900',
            ],
        ];

        $quickActions = [
            ['label' => 'เพิ่มบุคลากร', 'href' => Route::has('employees.create') ? route('employees.create') : null, 'permission' => 'employee.create'],
            ['label' => 'แจ้งซ่อมใหม่', 'href' => Route::has('repair-requests.create') ? route('repair-requests.create') : null, 'permission' => 'repair.create'],
            ['label' => 'ยื่นคำขอลา', 'href' => Route::has('leave-requests.create') ? route('leave-requests.create') : null, 'permission' => 'leave.create'],
            ['label' => 'จองห้องประชุม', 'href' => Route::has('meeting-bookings.create') ? route('meeting-bookings.create') : null, 'permission' => 'meeting.create'],
            ['label' => 'สร้างตารางเวร', 'href' => Route::has('duty-schedules.bulk-create') ? route('duty-schedules.bulk-create') : null, 'permission' => 'duty.create'],
            ['label' => 'นำเข้าเวลา', 'href' => Route::has('attendance-logs.import-form') ? route('attendance-logs.import-form') : null, 'permission' => 'attendance.import'],
        ];

        $moduleLinks = [
            ['label' => 'บุคลากร', 'description' => 'ข้อมูลแผนก ตำแหน่ง บุคลากร และผู้ใช้งาน', 'href' => Route::has('employees.index') ? route('employees.index') : null, 'permission' => 'employee.view'],
            ['label' => 'การลา', 'description' => 'คำขอลา ปฏิทินลา และการอนุมัติ', 'href' => Route::has('leave-requests.dashboard') ? route('leave-requests.dashboard') : null, 'permission' => 'leave.view'],
            ['label' => 'แจ้งซ่อม', 'description' => 'รับงาน ติดตามสถานะ และดูบอร์ดงานซ่อม', 'href' => Route::has('repair-requests.kanban') ? route('repair-requests.kanban') : null, 'permission' => 'repair.view'],
            ['label' => 'เวลาเข้างาน', 'description' => 'นำเข้าเวลา สรุปเวลา และรายงานประจำเดือน', 'href' => Route::has('attendance-summaries.dashboard') ? route('attendance-summaries.dashboard') : null, 'permission' => 'attendance.view'],
            ['label' => 'พัสดุ', 'description' => 'ทะเบียนพัสดุ หมวดหมู่ และการโอนย้าย', 'href' => Route::has('assets.index') ? route('assets.index') : null, 'permission' => 'asset.view'],
            ['label' => 'คอมพิวเตอร์และซอฟต์แวร์', 'description' => 'ทะเบียนเครื่อง Agent และ License', 'href' => Route::has('software-inventory.index') ? route('software-inventory.index') : null, 'permission' => 'software.view'],
            ['label' => 'เงินเดือน', 'description' => 'รอบเงินเดือน โปรไฟล์เงินเดือน และสลิป', 'href' => Route::has('payroll-periods.index') ? route('payroll-periods.index') : null, 'permission' => 'payroll.view'],
            ['label' => 'ห้องประชุม', 'description' => 'จองห้อง อนุมัติ และพิมพ์ใบจอง', 'href' => Route::has('meeting-bookings.index') ? route('meeting-bookings.index') : null, 'permission' => 'meeting.view'],
            ['label' => 'เอกสาร ITA', 'description' => 'จัดการเอกสาร MOIT และหน้าสาธารณะ', 'href' => Route::has('ita.documents.index') ? route('ita.documents.index') : null, 'permission' => 'ita.view'],
        ];

        $visibleQuickActions = collect($quickActions)->filter(fn ($item) => $item['href'] && $can($item['permission']));
        $visibleModules = collect($moduleLinks)->filter(fn ($item) => $item['href'] && $can($item['permission']));
    @endphp

    <div class="flex w-full flex-col gap-6">
        <section class="rounded-lg border border-gray-200 bg-white px-5 py-5 shadow-sm">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">ยินดีต้อนรับ {{ $user?->name }}</p>
                    <h1 class="mt-1 text-2xl font-bold text-gray-950">Dashboard</h1>
                    <p class="mt-1 text-sm text-gray-600">{{ $hospitalName }}</p>
                </div>

                <div class="flex flex-wrap gap-2">
                    @if (Route::has('exports.dashboard-summary'))
                        <a href="{{ route('exports.dashboard-summary') }}"
                           class="inline-flex items-center rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-gray-800">
                            Export Excel
                        </a>
                    @endif

                    @if (Route::has('notifications.index'))
                        <a href="{{ route('notifications.index') }}"
                           class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                            การแจ้งเตือน
                        </a>
                    @endif
                </div>
            </div>
        </section>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($overviewCards as $card)
                @if ($card['href'] && $can($card['permission']))
                    <a href="{{ $card['href'] }}"
                       class="rounded-lg border p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md {{ $card['tone'] }}">
                        <div class="text-sm font-medium opacity-80">{{ $card['label'] }}</div>
                        <div class="mt-3 text-3xl font-bold">{{ number_format($card['value']) }}</div>
                        <div class="mt-3 text-sm font-semibold">เปิดดูรายการ</div>
                    </a>
                @endif
            @endforeach
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.4fr_0.9fr]">
            <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-bold text-gray-950">งานที่ควรติดตาม</h2>
                        <p class="mt-1 text-sm text-gray-500">รายการสำคัญที่ควรเห็นก่อนเริ่มงานวันนี้</p>
                    </div>

                    @can('attendance.view')
                        <div class="hidden rounded-md border border-gray-200 px-3 py-2 text-right sm:block">
                            <div class="text-xs text-gray-500">มาสายเดือนนี้</div>
                            <div class="text-lg font-bold text-gray-950">{{ number_format($monthlyLateSummaries) }}</div>
                        </div>
                    @endcan
                </div>

                <div class="mt-5 grid gap-3 sm:grid-cols-2">
                    @foreach ($priorityItems as $item)
                        @if ($item['href'] && $can($item['permission']))
                            <a href="{{ $item['href'] }}"
                               class="rounded-lg border p-4 transition hover:shadow-sm {{ $item['tone'] }}">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="text-sm font-semibold">{{ $item['label'] }}</div>
                                    <div class="text-2xl font-bold">{{ number_format($item['value']) }}</div>
                                </div>
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-bold text-gray-950">ทางลัด</h2>
                <p class="mt-1 text-sm text-gray-500">งานที่ใช้บ่อยตามสิทธิ์ของคุณ</p>

                <div class="mt-5 grid gap-2">
                    @forelse ($visibleQuickActions as $action)
                        <a href="{{ $action['href'] }}"
                           class="flex items-center justify-between rounded-md border border-gray-200 px-4 py-3 text-sm font-semibold text-gray-800 transition hover:border-gray-400 hover:bg-gray-50">
                            <span>{{ $action['label'] }}</span>
                            <span class="text-gray-400">›</span>
                        </a>
                    @empty
                        <div class="rounded-md border border-dashed border-gray-300 px-4 py-5 text-sm text-gray-500">
                            ยังไม่มีทางลัดสำหรับสิทธิ์ผู้ใช้นี้
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                <div>
                    <h2 class="text-lg font-bold text-gray-950">ระบบงานหลัก</h2>
                    <p class="mt-1 text-sm text-gray-500">เลือกโมดูลที่ต้องการทำงานต่อ</p>
                </div>

                @if (Route::has('exports.table'))
                    <div class="text-sm text-gray-500">รายงาน Excel อยู่ในแต่ละโมดูล</div>
                @endif
            </div>

            <div class="mt-5 grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                @forelse ($visibleModules as $module)
                    <a href="{{ $module['href'] }}"
                       class="rounded-lg border border-gray-200 p-4 transition hover:border-gray-400 hover:bg-gray-50">
                        <div class="font-bold text-gray-950">{{ $module['label'] }}</div>
                        <div class="mt-1 text-sm leading-6 text-gray-600">{{ $module['description'] }}</div>
                    </a>
                @empty
                    <div class="rounded-md border border-dashed border-gray-300 p-5 text-sm text-gray-500">
                        ยังไม่มีโมดูลที่เปิดให้ใช้งานสำหรับสิทธิ์ผู้ใช้นี้
                    </div>
                @endforelse
            </div>
        </section>

        <section class="grid gap-4 md:grid-cols-3">
            @can('department.view')
                <a href="{{ route('departments.index') }}"
                   class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm transition hover:border-gray-400">
                    <div class="text-sm text-gray-500">หน่วยงาน</div>
                    <div class="mt-2 text-2xl font-bold text-gray-950">{{ number_format($modelCount(\App\Models\Department::class)) }}</div>
                </a>
            @endcan

            @can('position.view')
                <a href="{{ route('positions.index') }}"
                   class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm transition hover:border-gray-400">
                    <div class="text-sm text-gray-500">ตำแหน่ง</div>
                    <div class="mt-2 text-2xl font-bold text-gray-950">{{ number_format($modelCount(\App\Models\Position::class)) }}</div>
                </a>
            @endcan

            @can('setting.view')
                <a href="{{ route('system-settings.index') }}"
                   class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm transition hover:border-gray-400">
                    <div class="text-sm text-gray-500">ตั้งค่าระบบ</div>
                    <div class="mt-2 text-base font-bold text-gray-950">ข้อมูลโรงพยาบาลและค่ากลาง</div>
                </a>
            @endcan
        </section>
    </div>
</x-layouts.app>
