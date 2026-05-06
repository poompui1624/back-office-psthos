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
        <aside class="w-64 bg-gray-900 text-white">
            <div class="px-6 py-5 border-b border-gray-700">
                <div class="text-lg font-bold">
                    Hospital Backoffice
                </div>
                <div class="text-xs text-gray-400">
                    Core System
                </div>
            </div>

            <nav class="px-4 py-4 space-y-1">
                <a href="{{ url('/dashboard') }}"
                   class="block rounded px-3 py-2 text-sm hover:bg-gray-800 {{ request()->is('dashboard') ? 'bg-gray-800' : '' }}">
                    Dashboard
                </a>

                <div class="pt-4 pb-1 text-xs font-semibold uppercase text-gray-400">
                    Core System
                </div>

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

                <div class="pt-4 pb-1 text-xs font-semibold uppercase text-gray-400">
                    Modules
                </div>

                @can('asset.view')
                    <a href="#"
                       class="block rounded px-3 py-2 text-sm text-gray-400 cursor-not-allowed">
                        ทะเบียนพัสดุ
                    </a>
                @endcan

                @can('computer.view')
                    <a href="#"
                       class="block rounded px-3 py-2 text-sm text-gray-400 cursor-not-allowed">
                        ทะเบียนคอมพิวเตอร์
                    </a>
                @endcan

                @can('repair.view')
                    <a href="#"
                       class="block rounded px-3 py-2 text-sm text-gray-400 cursor-not-allowed">
                        แจ้งซ่อม
                    </a>
                @endcan

                @can('leave.view')
                    <a href="#"
                       class="block rounded px-3 py-2 text-sm text-gray-400 cursor-not-allowed">
                        ระบบลา
                    </a>
                @endcan
            </nav>
        </aside>

        <div class="flex-1 flex flex-col">
            <header class="bg-white border-b border-gray-200">
                <div class="px-6 py-4 flex items-center justify-between">
                    <div>
                        <h1 class="font-semibold">
                            {{ $title ?? 'Hospital Backoffice' }}
                        </h1>
                    </div>

                    <div class="flex items-center gap-4">
                        @auth
                            <a href="{{ route('notifications.index') }}"
                            class="relative rounded bg-gray-100 px-3 py-2 text-sm text-gray-700 hover:bg-gray-200">
                                แจ้งเตือน

                                @php
                                    $unreadCount = auth()->user()->unreadAppNotifications()->count();
                                @endphp

                                @if ($unreadCount > 0)
                                    <span class="absolute -right-2 -top-2 rounded-full bg-red-600 px-2 py-0.5 text-xs text-white">
                                        {{ $unreadCount }}
                                    </span>
                                @endif
                            </a>
                        @endauth
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
                                            class="rounded bg-gray-800 px-3 py-2 text-sm text-white hover:bg-gray-900">
                                        ออกจากระบบ
                                    </button>
                                </form>
                            @endif
                        @endauth
                    </div>
                </div>
            </header>

            <main class="flex-1 p-6">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
