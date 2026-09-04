@props(['title' => null, 'description' => null])

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $title ?: hospital_name() }}</title>

    @if ($description)
        <meta name="description" content="{{ $description }}">
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-slate-50 text-slate-900">
    @php
        $logoUrl = hospital_logo_url();

        $menu = [
            ['label' => 'หน้าแรก', 'route' => 'site.home', 'param' => null],
            ['label' => 'ประวัติโรงพยาบาล', 'route' => 'site.page', 'param' => 'history'],
            ['label' => 'วิสัยทัศน์ พันธกิจ', 'route' => 'site.page', 'param' => 'vision'],
            ['label' => 'โครงสร้างผู้บริหาร', 'route' => 'site.page', 'param' => 'structure'],
            ['label' => 'ITA', 'route' => 'ita.public.index', 'param' => null],
        ];
    @endphp

    <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/95 backdrop-blur">
        <div class="mx-auto max-w-6xl px-5 sm:px-6">
            <div class="flex items-center justify-between gap-4 py-3">
                <a href="{{ route('site.home') }}" class="flex min-w-0 items-center gap-3">
                    @if ($logoUrl)
                        <img src="{{ $logoUrl }}" alt="{{ hospital_name() }}"
                             class="h-11 w-11 shrink-0 rounded-xl object-contain">
                    @else
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand-500 text-sm font-bold text-white">
                            รพ
                        </div>
                    @endif

                    <div class="min-w-0">
                        <div class="truncate font-bold leading-tight text-slate-900">{{ hospital_name() }}</div>

                        @if (setting('hospital_phone'))
                            <div class="text-xs text-slate-500">โทร {{ setting('hospital_phone') }}</div>
                        @endif
                    </div>
                </a>

                <nav class="hidden items-center gap-1 lg:flex">
                    @foreach ($menu as $item)
                        @continue (! Route::has($item['route']))

                        @php
                            $href = $item['param']
                                ? route($item['route'], $item['param'])
                                : route($item['route']);

                            $isActive = $item['param']
                                ? request()->routeIs($item['route']) && request()->route('key') === $item['param']
                                : request()->routeIs($item['route']);
                        @endphp

                        <a href="{{ $href }}"
                           @class([
                               'rounded-xl px-3.5 py-2 text-sm font-semibold transition',
                               'bg-brand-500 text-white' => $isActive,
                               'text-slate-600 hover:bg-slate-100 hover:text-slate-900' => ! $isActive,
                           ])>
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </nav>

                {{-- A menu this short reads fine as a scrolling row on a phone,
                     which avoids a toggle and the script to drive it. --}}
                <details class="lg:hidden">
                    <summary class="cursor-pointer list-none rounded-xl border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700">
                        เมนู
                    </summary>

                    <div class="absolute inset-x-0 mt-3 border-y border-slate-200 bg-white p-3 shadow-lg">
                        @foreach ($menu as $item)
                            @continue (! Route::has($item['route']))

                            <a href="{{ $item['param'] ? route($item['route'], $item['param']) : route($item['route']) }}"
                               class="block rounded-xl px-3 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                {{ $item['label'] }}
                            </a>
                        @endforeach
                    </div>
                </details>
            </div>
        </div>
    </header>

    <main class="flex-1">
        {{ $slot }}
    </main>

    <footer class="mt-12 border-t border-slate-200 bg-white">
        <div class="mx-auto max-w-6xl px-5 py-9 sm:px-6">
            <div class="flex flex-col gap-6 sm:flex-row sm:items-start sm:justify-between">
                <div class="flex items-start gap-3">
                    @if ($logoUrl)
                        <img src="{{ $logoUrl }}" alt="" class="h-12 w-12 rounded-xl object-contain">
                    @endif

                    <div>
                        <div class="font-bold text-slate-900">{{ hospital_name() }}</div>

                        @if (setting('hospital_address'))
                            <p class="mt-1 max-w-md text-sm leading-relaxed text-slate-600">
                                {{ setting('hospital_address') }}
                            </p>
                        @endif
                    </div>
                </div>

                <div class="text-sm text-slate-600">
                    @if (setting('hospital_phone'))
                        <div>โทรศัพท์ {{ setting('hospital_phone') }}</div>
                    @endif

                    @if (Route::has('ita.public.index'))
                        <a href="{{ route('ita.public.index') }}" class="mt-2 inline-block font-semibold text-brand-600 hover:underline">
                            การประเมินคุณธรรมและความโปร่งใส (ITA)
                        </a>
                    @endif
                </div>
            </div>

            <div class="mt-7 border-t border-slate-100 pt-5 text-center text-xs text-slate-400">
                &copy; {{ thai_year((int) now()->year) }} {{ hospital_name() }}
            </div>
        </div>
    </footer>
</body>
</html>
