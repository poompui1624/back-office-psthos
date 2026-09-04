@props(['title' => null, 'subtitle' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name', 'Hospital Backoffice') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 text-slate-900 antialiased">
@php
    $logoUrl = function_exists('hospital_logo_url') ? hospital_logo_url() : null;
    $hospital = function_exists('hospital_name') ? hospital_name() : config('app.name', 'Hospital Backoffice');

    $user = auth()->user();
    $unreadCount = $user?->unreadAppNotifications()->count() ?? 0;
    $roleLabel = $user?->roles->pluck('name')->join(', ') ?: 'ยังไม่กำหนดสิทธิ์';

    $navSections = collect(config('navigation', []))
        ->map(function (array $section) use ($user) {
            $section['items'] = collect($section['items'])
                ->filter(fn (array $item) => Route::has($item['route'])
                    && ($item['permission'] === null || $user?->can($item['permission'])))
                ->values();

            return $section;
        })
        ->filter(fn (array $section) => $section['items']->isNotEmpty());
@endphp

<div class="min-h-screen lg:flex">
    <input id="app-sidebar-toggle" type="checkbox" class="peer sr-only">

    <label for="app-sidebar-toggle"
           class="fixed inset-0 z-30 hidden bg-slate-950/60 backdrop-blur-sm peer-checked:block lg:hidden"
           aria-label="ปิดเมนู"></label>

    <aside class="fixed inset-y-0 left-0 z-40 flex w-72 -translate-x-full flex-col bg-slate-950 text-white shadow-2xl transition-transform duration-200 peer-checked:translate-x-0 lg:static lg:translate-x-0 lg:shadow-none">
        <div class="flex items-center gap-3 border-b border-white/10 px-5 py-4">
            @if ($logoUrl)
                <img src="{{ $logoUrl }}" alt="" class="h-10 w-10 shrink-0 rounded-xl bg-white object-contain p-1">
            @else
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-500 text-base font-bold text-white shadow-lg shadow-brand-500/30">
                    {{ mb_substr($hospital, 0, 1) }}
                </div>
            @endif

            <div class="min-w-0 flex-1">
                <div class="truncate text-sm font-bold leading-tight">{{ $hospital }}</div>
                <div class="text-[11px] uppercase tracking-wider text-slate-400">Back-office System</div>
            </div>

            <label for="app-sidebar-toggle" class="rounded-lg p-1.5 text-slate-400 hover:bg-white/10 hover:text-white lg:hidden" aria-label="ปิดเมนู">
                <x-icon name="close" class="h-5 w-5" />
            </label>
        </div>

        <nav class="flex-1 space-y-6 overflow-y-auto px-3 py-4">
            @foreach ($navSections as $section)
                <div>
                    <div class="px-3 pb-2 text-[11px] font-bold uppercase tracking-widest text-slate-500">
                        {{ $section['label'] }}
                    </div>

                    <div class="space-y-0.5">
                        @foreach ($section['items'] as $item)
                            @php $isActive = request()->routeIs($item['active']); @endphp

                            <a href="{{ route($item['route']) }}"
                               @class([
                                   'group flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition',
                                   'bg-brand-500 text-white shadow-lg shadow-brand-500/25' => $isActive,
                                   'text-slate-300 hover:bg-white/10 hover:text-white' => ! $isActive,
                               ])>
                                <x-icon :name="$item['icon']" class="h-[18px] w-[18px] shrink-0 {{ $isActive ? 'text-white' : 'text-slate-400 group-hover:text-brand-300' }}" />
                                <span class="truncate">{{ $item['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </nav>

        @auth
            <div class="border-t border-white/10 p-3">
                <div class="flex items-center gap-3 rounded-xl bg-white/5 px-3 py-2.5">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-500 text-xs font-bold text-white">
                        {{ $user->initials() }}
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="truncate text-sm font-semibold leading-tight">{{ $user->name }}</div>
                        <div class="truncate text-[11px] text-slate-400">{{ $roleLabel }}</div>
                    </div>

                    @if (Route::has('logout'))
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="rounded-lg p-1.5 text-slate-400 transition hover:bg-white/10 hover:text-rose-300"
                                    title="ออกจากระบบ">
                                <x-icon name="logout" class="h-[18px] w-[18px]" />
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endauth
    </aside>

    <div class="flex min-w-0 flex-1 flex-col">
        <header class="sticky top-0 z-20 border-b border-slate-200/80 bg-white/85 backdrop-blur-md">
            <div class="flex items-center gap-3 px-4 py-3 sm:px-6">
                <label for="app-sidebar-toggle"
                       class="rounded-xl border border-slate-200 bg-white p-2 text-slate-600 shadow-sm transition hover:bg-slate-50 lg:hidden"
                       aria-label="เปิดเมนู">
                    <x-icon name="menu" />
                </label>

                <div class="min-w-0 flex-1">
                    <h1 class="truncate text-base font-bold leading-tight text-slate-900 sm:text-lg">
                        {{ $title ?? 'Dashboard' }}
                    </h1>

                    <div class="hidden items-center gap-1.5 text-xs text-slate-500 sm:flex">
                        @if ($subtitle)
                            <span class="h-1.5 w-1.5 rounded-full bg-brand-500"></span>
                            <span class="truncate">{{ $subtitle }}</span>
                        @else
                            <span>{{ now()->format('d/m/Y H:i') }}</span>
                        @endif
                    </div>
                </div>

                @auth
                    <div class="flex items-center gap-2">
                        @if (Route::has('notifications.index'))
                            <a href="{{ route('notifications.index') }}"
                               class="relative rounded-xl border border-slate-200 bg-white p-2 text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-brand-600"
                               title="การแจ้งเตือน">
                                <x-icon name="bell" />

                                @if ($unreadCount > 0)
                                    <span class="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-rose-500 px-1 text-[10px] font-bold text-white ring-2 ring-white">
                                        {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                                    </span>
                                @endif
                            </a>
                        @endif

                        <div class="hidden items-center gap-2.5 rounded-xl border border-slate-200 bg-white py-1.5 pl-2 pr-3 shadow-sm sm:flex">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-500 text-[11px] font-bold text-white">
                                {{ $user->initials() }}
                            </div>

                            <div class="min-w-0 leading-tight">
                                <div class="truncate text-sm font-semibold text-slate-900">{{ $user->name }}</div>
                                <div class="max-w-40 truncate text-[11px] text-slate-500">{{ $roleLabel }}</div>
                            </div>
                        </div>
                    </div>
                @endauth
            </div>
        </header>

        <main class="min-w-0 flex-1 p-4 sm:p-6">
            {{-- Rendered once here so pages do not each repeat it. --}}
            @if (session('success'))
                <x-alert type="success" class="mb-4">{{ session('success') }}</x-alert>
            @endif

            @if (session('error'))
                <x-alert type="error" class="mb-4">{{ session('error') }}</x-alert>
            @endif

            @if (session('warning'))
                <x-alert type="warning" class="mb-4">{{ session('warning') }}</x-alert>
            @endif

            {{ $slot }}
        </main>
    </div>
</div>
</body>
</html>
