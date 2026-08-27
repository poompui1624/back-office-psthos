<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name', 'Hospital Backoffice') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 text-slate-900">
    @php
        $logoUrl = function_exists('hospital_logo_url') ? hospital_logo_url() : null;
        $name = function_exists('hospital_name') ? hospital_name() : config('app.name', 'Hospital Backoffice');

        $navSections = [
            [
                'label' => 'ภาพรวม',
                'items' => [
                    ['label' => 'Dashboard', 'route' => 'dashboard', 'active' => 'dashboard', 'permission' => 'dashboard.view'],
                    ['label' => 'รายการอนุมัติ', 'route' => 'approvals.index', 'active' => 'approvals.*', 'permission' => 'approval.view'],
                ],
            ],
            [
                'label' => 'ข้อมูลหลัก',
                'items' => [
                    ['label' => 'หน่วยงาน', 'route' => 'departments.index', 'active' => 'departments.*', 'permission' => 'department.view'],
                    ['label' => 'ตำแหน่ง', 'route' => 'positions.index', 'active' => 'positions.*', 'permission' => 'position.view'],
                    ['label' => 'บุคลากร', 'route' => 'employees.index', 'active' => 'employees.*', 'permission' => 'employee.view'],
                    ['label' => 'ผู้ใช้งานระบบ', 'route' => 'users.index', 'active' => 'users.*', 'permission' => 'user.view'],
                    ['label' => 'ตั้งค่าระบบ', 'route' => 'system-settings.index', 'active' => 'system-settings.*', 'permission' => 'setting.view'],
                    ['label' => 'Audit Log', 'route' => 'audit-logs.index', 'active' => 'audit-logs.*', 'permission' => 'audit.view'],
                ],
            ],
            [
                'label' => 'งานบุคคล',
                'items' => [
                    ['label' => 'Dashboard การลา', 'route' => 'leave-requests.dashboard', 'active' => 'leave-requests.dashboard', 'permission' => 'leave.view'],
                    ['label' => 'รายการลา', 'route' => 'leave-requests.index', 'active' => 'leave-requests.index', 'permission' => 'leave.view'],
                    ['label' => 'ปฏิทินลา', 'route' => 'leave-requests.calendar', 'active' => 'leave-requests.calendar', 'permission' => 'leave.view'],
                    ['label' => 'ประเภทการลา', 'route' => 'leave-types.index', 'active' => 'leave-types.*', 'permission' => 'leave.view'],
                    ['label' => 'Dashboard เวลา', 'route' => 'attendance-summaries.dashboard', 'active' => 'attendance-summaries.dashboard', 'permission' => 'attendance.view'],
                    ['label' => 'เวลาเข้างาน', 'route' => 'attendance-logs.index', 'active' => 'attendance-logs.*', 'permission' => 'attendance.view'],
                    ['label' => 'สรุปเวลาทำงาน', 'route' => 'attendance-summaries.index', 'active' => 'attendance-summaries.index', 'permission' => 'attendance.view'],
                    ['label' => 'เครื่องสแกนนิ้ว', 'route' => 'attendance-devices.index', 'active' => 'attendance-devices.*', 'permission' => 'attendance.view'],
                    ['label' => 'Dashboard ตารางเวร', 'route' => 'duty-schedules.index', 'active' => 'duty-schedules.index', 'permission' => 'duty.view'],
                    ['label' => 'ปฏิทินตารางเวร', 'route' => 'duty-schedules.calendar', 'active' => 'duty-schedules.calendar', 'permission' => 'duty.view'],
                    ['label' => 'สร้างเวรหลายรายการ', 'route' => 'duty-schedules.bulk-create', 'active' => 'duty-schedules.bulk-create', 'permission' => 'duty.create'],
                    ['label' => 'ประเภทเวร', 'route' => 'shift-types.index', 'active' => 'shift-types.*', 'permission' => 'duty.view'],
                ],
            ],
            [
                'label' => 'บริการและทรัพย์สิน',
                'items' => [
                    ['label' => 'จองห้องประชุม', 'route' => 'meeting-bookings.index', 'active' => 'meeting-bookings.*', 'permission' => 'meeting.view'],
                    ['label' => 'ห้องประชุม', 'route' => 'meeting-rooms.index', 'active' => 'meeting-rooms.*', 'permission' => 'meeting.view'],
                    ['label' => 'แจ้งซ่อม', 'route' => 'repair-requests.index', 'active' => 'repair-requests.*', 'permission' => 'repair.view'],
                    ['label' => 'Repair Kanban', 'route' => 'repair-requests.kanban', 'active' => 'repair-requests.kanban', 'permission' => 'repair.view'],
                    ['label' => 'ทะเบียนพัสดุ', 'route' => 'assets.index', 'active' => 'assets.*', 'permission' => 'asset.view'],
                    ['label' => 'หมวดหมู่พัสดุ', 'route' => 'asset-categories.index', 'active' => 'asset-categories.*', 'permission' => 'asset.view'],
                    ['label' => 'โอนย้ายพัสดุ', 'route' => 'asset-movements.index', 'active' => 'asset-movements.*', 'permission' => 'asset.view'],
                    ['label' => 'ทะเบียนคอมพิวเตอร์', 'route' => 'computers.index', 'active' => 'computers.*', 'permission' => 'computer.view'],
                    ['label' => 'Computer Agents', 'route' => 'computer-agents.index', 'active' => 'computer-agents.*', 'permission' => 'computer.agent_manage'],
                    ['label' => 'Software Inventory', 'route' => 'software-inventory.index', 'active' => 'software-inventory.*', 'permission' => 'software.view'],
                    ['label' => 'Software Products', 'route' => 'software-products.index', 'active' => 'software-products.*', 'permission' => 'software.view'],
                    ['label' => 'Software Licenses', 'route' => 'software-licenses.index', 'active' => 'software-licenses.*', 'permission' => 'software.view'],
                ],
            ],
            [
                'label' => 'การเงินและเอกสาร',
                'items' => [
                    ['label' => 'เงินเดือน / สลิป', 'route' => 'payroll-periods.index', 'active' => 'payroll-periods.*', 'permission' => 'payroll.view'],
                    ['label' => 'ตั้งค่าเงินเดือน', 'route' => 'salary-profiles.index', 'active' => 'salary-profiles.*', 'permission' => 'payroll.view'],
                    ['label' => 'เอกสาร ITA', 'route' => 'ita.documents.index', 'active' => 'ita.documents.*', 'permission' => 'ita.view'],
                ],
            ],
        ];
    @endphp

    <div class="min-h-screen lg:flex">
        <input id="app-sidebar-toggle" type="checkbox" class="peer sr-only">
        <label for="app-sidebar-toggle" class="fixed inset-0 z-30 hidden bg-slate-950/50 peer-checked:block lg:hidden" aria-label="ปิดเมนู"></label>

        <aside class="fixed inset-y-0 left-0 z-40 flex w-72 -translate-x-full flex-col bg-slate-950 text-white shadow-2xl transition-transform duration-200 peer-checked:translate-x-0 lg:static lg:w-64 lg:translate-x-0 lg:shadow-none">
            <div class="border-b border-white/10 px-5 py-5">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex min-w-0 items-center gap-3">
                        @if ($logoUrl)
                            <img src="{{ $logoUrl }}" alt="logo" class="h-10 w-10 rounded-lg bg-white object-contain p-1">
                        @else
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#02abff] text-sm font-bold text-white">
                                H
                            </div>
                        @endif

                        <div class="min-w-0">
                            <div class="truncate font-bold">{{ $name }}</div>
                            <div class="text-xs text-slate-400">Back-office System</div>
                        </div>
                    </div>

                    <label for="app-sidebar-toggle" class="rounded-lg p-2 text-slate-300 hover:bg-white/10 lg:hidden" aria-label="ปิดเมนู">
                        <span class="block h-4 w-4 text-center leading-4">×</span>
                    </label>
                </div>
            </div>

            <nav class="flex-1 space-y-5 overflow-y-auto px-3 py-4">
                @foreach ($navSections as $section)
                    @php
                        $visibleItems = collect($section['items'])
                            ->filter(fn ($item) => Route::has($item['route']) && auth()->user()?->can($item['permission']));
                    @endphp

                    @if ($visibleItems->isNotEmpty())
                        <div>
                            <div class="px-3 pb-2 text-xs font-semibold uppercase tracking-wide text-sky-300">
                                {{ $section['label'] }}
                            </div>

                            <div class="space-y-1">
                                @foreach ($visibleItems as $item)
                                    <a href="{{ route($item['route']) }}"
                                       class="block rounded-xl px-3 py-2 text-sm font-medium transition {{ request()->routeIs($item['active']) ? 'bg-[#02abff] text-white shadow-sm shadow-sky-950/30' : 'text-slate-200 hover:bg-white/10 hover:text-white' }}">
                                        {{ $item['label'] }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </nav>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">
            <header class="sticky top-0 z-20 border-b border-slate-200 bg-white/95 backdrop-blur">
                <div class="flex items-center justify-between gap-3 px-4 py-3 sm:px-6">
                    <div class="flex min-w-0 items-center gap-3">
                        <label for="app-sidebar-toggle" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 lg:hidden" aria-label="เปิดเมนู">
                            เมนู
                        </label>

                        <div class="min-w-0">
                            <div class="truncate text-sm font-semibold text-slate-900 sm:text-base">
                                {{ $title ?? 'Dashboard' }}
                            </div>
                            <div class="hidden text-xs text-slate-500 sm:block">
                                {{ now()->format('d/m/Y H:i') }}
                            </div>
                        </div>
                    </div>

                    @auth
                        <div class="flex min-w-0 items-center gap-3">
                            <div class="hidden text-right sm:block">
                                <div class="text-sm font-semibold text-slate-900">{{ auth()->user()->name }}</div>
                                <div class="max-w-44 truncate text-xs text-slate-500">
                                    {{ auth()->user()->roles->pluck('name')->join(', ') ?: 'no role' }}
                                </div>
                            </div>

                            @if (Route::has('logout'))
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf

                                    <button type="submit" class="rounded-xl bg-slate-900 px-3 py-2 text-sm font-semibold text-white transition hover:bg-slate-700">
                                        ออกจากระบบ
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endauth
                </div>
            </header>

            <main class="min-w-0 flex-1 p-4 sm:p-6">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
