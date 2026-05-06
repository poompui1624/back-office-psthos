<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name', 'Hospital Backoffice') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900">
    <div class="min-h-screen flex">
        @php
            $hospitalLogo = class_exists(\App\Models\SystemSetting::class)
                ? \App\Models\SystemSetting::where('key', 'hospital_logo')->value('value')
                : null;

            $hospitalName = class_exists(\App\Models\SystemSetting::class)
                ? \App\Models\SystemSetting::where('key', 'hospital_name')->value('value')
                : config('app.name', 'Hospital Backoffice');
        @endphp

        {{-- Sidebar --}}
        <aside class="w-64 bg-gray-950 text-white">
            <div class="px-6 py-5 border-b border-gray-800">
                @php
                    $logoUrl = function_exists('hospital_logo_url') ? hospital_logo_url() : null;
                    $name = function_exists('hospital_name') ? hospital_name() : config('app.name', 'Hospital Backoffice');
                @endphp

                <div class="flex items-center gap-3">
                    @if ($logoUrl)
                        <img src="{{ $logoUrl }}"
                            alt="logo"
                            class="h-10 w-10 rounded bg-white object-contain p-1">
                    @endif

                    <div>
                        <div class="font-bold">
                            {{ $name }}
                        </div>
                        <div class="text-xs text-gray-400">
                            Back-office System
                        </div>
                    </div>
                </div>
                <div class="text-xs text-gray-400">
                    Core System
                </div>
            </div>

            <nav class="px-4 py-4 space-y-1">

                {{-- Dashboard --}}
                @can('dashboard.view')
                    <a href="{{ route('dashboard') }}"
                       class="block rounded px-3 py-2 text-sm hover:bg-gray-800 {{ request()->routeIs('dashboard') ? 'bg-gray-800' : '' }}">
                        Dashboard
                    </a>
                @endcan

                <div class="pt-4 pb-1 text-xs font-semibold uppercase text-blue-300">
                    Core System
                </div>

                @can('approval.view')
                    <a href="{{ route('approvals.index') }}"
                    class="block rounded px-3 py-2 text-sm hover:bg-gray-800 {{ request()->routeIs('approvals.*') ? 'bg-gray-800' : '' }}">
                        รายการอนุมัติ
                    </a>
                @endcan

                @can('department.view')
                    <a href="{{ route('departments.index') }}"
                       class="block rounded px-3 py-2 text-sm hover:bg-gray-800 {{ request()->routeIs('departments.*') ? 'bg-gray-800' : '' }}">
                        หน่วยงาน
                    </a>
                @endcan

                @can('position.view')
                    <a href="{{ route('positions.index') }}"
                       class="block rounded px-3 py-2 text-sm hover:bg-gray-800 {{ request()->routeIs('positions.*') ? 'bg-gray-800' : '' }}">
                        ตำแหน่ง
                    </a>
                @endcan

                @can('employee.view')
                    <a href="{{ route('employees.index') }}"
                       class="block rounded px-3 py-2 text-sm hover:bg-gray-800 {{ request()->routeIs('employees.*') ? 'bg-gray-800' : '' }}">
                        บุคลากร
                    </a>
                @endcan

                @can('user.view')
                    <a href="{{ route('users.index') }}"
                       class="block rounded px-3 py-2 text-sm hover:bg-gray-800 {{ request()->routeIs('users.*') ? 'bg-gray-800' : '' }}">
                        ผู้ใช้งานระบบ
                    </a>
                @endcan

                @if (Route::has('system-settings.index'))
                    @can('setting.view')
                        <a href="{{ route('system-settings.index') }}"
                           class="block rounded px-3 py-2 text-sm hover:bg-gray-800 {{ request()->routeIs('system-settings.*') ? 'bg-gray-800' : '' }}">
                            ตั้งค่าระบบ
                        </a>
                    @endcan
                @endif
                @can('audit.view')
                    <a href="{{ route('audit-logs.index') }}"
                    class="block rounded px-3 py-2 text-sm hover:bg-gray-800 {{ request()->routeIs('audit-logs.*') ? 'bg-gray-800' : '' }}">
                        Audit Log
                    </a>
                @endcan

                <div class="pt-4 pb-1 text-xs font-semibold uppercase text-blue-300">
                    Modules
                </div>

                @can('meeting.view')
                <a href="{{ route('meeting-bookings.index') }}"
                class="block rounded px-3 py-2 text-sm hover:bg-gray-800 {{ request()->routeIs('meeting-bookings.*') ? 'bg-gray-800' : '' }}">
                    จองห้องประชุม
                </a>

                <a href="{{ route('meeting-rooms.index') }}"
                class="block rounded px-3 py-2 text-sm hover:bg-gray-800 {{ request()->routeIs('meeting-rooms.*') ? 'bg-gray-800' : '' }}">
                    ห้องประชุม
                </a>
            @endcan

                @can('asset.view')
                    <a href="{{ route('assets.index') }}"
                    class="block rounded px-3 py-2 text-sm hover:bg-gray-800 {{ request()->routeIs('assets.*') ? 'bg-gray-800' : '' }}">
                        ทะเบียนพัสดุ
                    </a>

                    <a href="{{ route('asset-categories.index') }}"
                    class="block rounded px-3 py-2 text-sm hover:bg-gray-800 {{ request()->routeIs('asset-categories.*') ? 'bg-gray-800' : '' }}">
                        หมวดหมู่พัสดุ
                    </a>

                    <a href="{{ route('asset-movements.index') }}"
                    class="block rounded px-3 py-2 text-sm hover:bg-gray-800 {{ request()->routeIs('asset-movements.*') ? 'bg-gray-800' : '' }}">
                        โอนย้ายพัสดุ
                    </a>
                @endcan

                @can('computer.view')
                    <a href="{{ route('computers.index') }}"
                    class="block rounded px-3 py-2 text-sm hover:bg-gray-800 {{ request()->routeIs('computers.*') ? 'bg-gray-800' : '' }}">
                        ทะเบียนคอมพิวเตอร์
                    </a>
                @endcan

                @can('computer.agent_manage')
                    <a href="{{ route('computer-agents.index') }}"
                    class="block rounded px-3 py-2 text-sm hover:bg-gray-800 {{ request()->routeIs('computer-agents.*') ? 'bg-gray-800' : '' }}">
                        Computer Agents
                    </a>
                @endcan

                @can('software.view')
                    <a href="{{ route('software-inventory.index') }}"
                    class="block rounded px-3 py-2 text-sm hover:bg-gray-800 {{ request()->routeIs('software-inventory.*') ? 'bg-gray-800' : '' }}">
                        Software Inventory
                    </a>

                    <a href="{{ route('software-products.index') }}"
                    class="block rounded px-3 py-2 text-sm hover:bg-gray-800 {{ request()->routeIs('software-products.*') ? 'bg-gray-800' : '' }}">
                        Software Products
                    </a>

                    <a href="{{ route('software-licenses.index') }}"
                    class="block rounded px-3 py-2 text-sm hover:bg-gray-800 {{ request()->routeIs('software-licenses.*') ? 'bg-gray-800' : '' }}">
                        Software Licenses
                    </a>
                @endcan

                @can('repair.view')
                    <a href="{{ route('repair-requests.index') }}"
                    class="block rounded px-3 py-2 text-sm hover:bg-gray-800 {{ request()->routeIs('repair-requests.*') ? 'bg-gray-800' : '' }}">
                        ระบบแจ้งซ่อม
                    </a>

                    <a href="{{ route('repair-requests.kanban') }}"
                    class="block rounded px-3 py-2 text-sm hover:bg-gray-800 {{ request()->routeIs('repair-requests.kanban') ? 'bg-gray-800' : '' }}">
                        Repair Kanban
                    </a>
                @endcan

                @can('leave.view')
                    <a href="{{ route('leave-requests.dashboard') }}"
                    class="block rounded px-3 py-2 text-sm hover:bg-gray-800 {{ request()->routeIs('leave-requests.dashboard') ? 'bg-gray-800' : '' }}">
                        Leave Dashboard
                    </a>

                    <a href="{{ route('leave-requests.calendar') }}"
                    class="block rounded px-3 py-2 text-sm hover:bg-gray-800 {{ request()->routeIs('leave-requests.calendar') ? 'bg-gray-800' : '' }}">
                        ปฏิทินการลา
                    </a>
                    <a href="{{ route('leave-requests.index') }}"
                    class="block rounded px-3 py-2 text-sm hover:bg-gray-800 {{ request()->routeIs('leave-requests.*') ? 'bg-gray-800' : '' }}">
                        ระบบการลา
                    </a>

                    <a href="{{ route('leave-types.index') }}"
                    class="block rounded px-3 py-2 text-sm hover:bg-gray-800 {{ request()->routeIs('leave-types.*') ? 'bg-gray-800' : '' }}">
                        ประเภทการลา
                    </a>
                @endcan

                @can('attendance.view')
                    <a href="{{ route('attendance-logs.index') }}"
                    class="block rounded px-3 py-2 text-sm hover:bg-gray-800 {{ request()->routeIs('attendance-logs.*') ? 'bg-gray-800' : '' }}">
                        เวลาทำงาน
                    </a>

                    <a href="{{ route('attendance-devices.index') }}"
                    class="block rounded px-3 py-2 text-sm hover:bg-gray-800 {{ request()->routeIs('attendance-devices.*') ? 'bg-gray-800' : '' }}">
                        เครื่องสแกนนิ้ว
                    </a>

                    <a href="{{ route('attendance-summaries.index') }}"
                    class="block rounded px-3 py-2 text-sm hover:bg-gray-800 {{ request()->routeIs('attendance-summaries.*') ? 'bg-gray-800' : '' }}">
                        สรุปเวลาทำงาน
                    </a>

                    <a href="{{ route('attendance-summaries.dashboard') }}"
                    class="block rounded px-3 py-2 text-sm hover:bg-gray-800 {{ request()->routeIs('attendance-summaries.dashboard') ? 'bg-gray-800' : '' }}">
                        Attendance Dashboard
                    </a>
                @endcan

                @can('duty.view')
                    <a href="{{ route('duty-schedules.calendar') }}"
                    class="block rounded px-3 py-2 text-sm hover:bg-gray-800 {{ request()->routeIs('duty-schedules.calendar') ? 'bg-gray-800' : '' }}">
                        ปฏิทินตารางเวร
                    </a>

                    <a href="{{ route('duty-schedules.bulk-create') }}"
                    class="block rounded px-3 py-2 text-sm hover:bg-gray-800 {{ request()->routeIs('duty-schedules.bulk-create') ? 'bg-gray-800' : '' }}">
                        สร้างเวรหลายรายการ
                    </a>
                @endcan

                @can('payroll.view')
                    <a href="{{ route('payroll-periods.index') }}"
                    class="block rounded px-3 py-2 text-sm hover:bg-gray-800 {{ request()->routeIs('payroll-periods.*') ? 'bg-gray-800' : '' }}">
                        เงินเดือน / สลิป
                    </a>

                    <a href="{{ route('salary-profiles.index') }}"
                    class="block rounded px-3 py-2 text-sm hover:bg-gray-800 {{ request()->routeIs('salary-profiles.*') ? 'bg-gray-800' : '' }}">
                        ตั้งค่าเงินเดือน
                    </a>
                @endcan
            </nav>
        </aside>

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col">

            {{-- Topbar --}}
            <header class="bg-white border-b border-gray-200">
                <div class="px-6 py-4 flex items-center justify-between">
                    <div class="font-semibold">
                        {{ $title ?? 'Dashboard' }}
                    </div>

                    <div class="flex items-center gap-4">
                        @auth
                            <div class="text-right">
                                <div class="text-sm font-medium">
                                    {{ auth()->user()->name }}
                                </div>

                                <div class="text-xs text-gray-500">
                                    {{ auth()->user()->roles->pluck('name')->join(', ') ?: 'no role' }}
                                </div>
                            </div>

                            @if (Route::has('logout'))
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf

                                    <button type="submit"
                                            class="rounded bg-gray-900 px-3 py-2 text-sm text-white hover:bg-gray-800">
                                        ออกจากระบบ
                                    </button>
                                </form>
                            @endif
                        @endauth
                    </div>
                </div>
            </header>

            {{-- Page --}}
            <main class="flex-1 p-6">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
