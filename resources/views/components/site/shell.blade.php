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

        /*
            Eight items across the top left the bar running into the hospital
            name on a laptop. The two natural groupings — what is happening, and
            who the hospital is — collapse to one entry each, taking it to five.

            An item with 'children' is a dropdown; the parent is a label, not a
            destination, so the pages under it stay one click away.
        */
        $menu = [
            ['label' => 'หน้าแรก', 'route' => 'site.home', 'param' => null],
            [
                'label' => 'ข่าวและกิจกรรม',
                'children' => [
                    ['label' => 'ข่าวสารทั้งหมด', 'route' => 'site.news', 'param' => null],
                    ['label' => 'ข่าวประชาสัมพันธ์', 'route' => 'site.news', 'param' => null, 'query' => ['category' => 'news']],
                    ['label' => 'ภาพกิจกรรม', 'route' => 'site.gallery', 'param' => null],
                    ['label' => 'ความรู้สู่ประชาชน', 'route' => 'site.news', 'param' => null, 'query' => ['category' => 'knowledge']],
                ],
            ],
            [
                'label' => 'เกี่ยวกับโรงพยาบาล',
                'children' => [
                    ['label' => 'ประวัติโรงพยาบาล', 'route' => 'site.page', 'param' => 'history'],
                    ['label' => 'วิสัยทัศน์ พันธกิจ', 'route' => 'site.page', 'param' => 'vision'],
                    ['label' => 'โครงสร้างผู้บริหาร', 'route' => 'site.page', 'param' => 'structure'],
                ],
            ],
            ['label' => 'เอกสารเผยแพร่', 'route' => 'site.documents', 'param' => null],
            ['label' => 'ITA', 'route' => 'ita.public.index', 'param' => null],
        ];

        /**
         * The URL for a menu entry, or null when its route is not registered.
         */
        $menuUrl = function (array $item): ?string {
            if (! Route::has($item['route'])) {
                return null;
            }

            $parameters = $item['param'] ? [$item['param']] : [];
            $parameters = array_merge($parameters, $item['query'] ?? []);

            return route($item['route'], $parameters);
        };

        /**
         * Whether this entry is the page being viewed.
         *
         * A category entry only counts when the query string matches too, so
         * "ข่าวสารทั้งหมด" and "ข่าวประชาสัมพันธ์" do not both light up.
         */
        $menuIsActive = function (array $item): bool {
            if (! request()->routeIs($item['route'])) {
                return false;
            }

            if (($item['param'] ?? null) && request()->route('key') !== $item['param']) {
                return false;
            }

            foreach ($item['query'] ?? [] as $key => $value) {
                if (request()->query($key) !== $value) {
                    return false;
                }
            }

            return ($item['query'] ?? []) !== [] || request()->query('category') === null;
        };

        // Drop entries whose route is missing, and any group left empty by that.
        $menu = collect($menu)
            ->map(function (array $item) use ($menuUrl) {
                if (! isset($item['children'])) {
                    return $menuUrl($item) ? $item : null;
                }

                $item['children'] = array_values(array_filter(
                    $item['children'],
                    fn (array $child) => $menuUrl($child) !== null
                ));

                return $item['children'] === [] ? null : $item;
            })
            ->filter()
            ->values()
            ->all();
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

                <nav class="hidden items-center gap-1 lg:flex" data-site-nav>
                    @foreach ($menu as $item)
                        @if (! isset($item['children']))
                            @php $isActive = $menuIsActive($item); @endphp

                            <a href="{{ $menuUrl($item) }}"
                               @class([
                                   'rounded-xl px-3.5 py-2 text-sm font-semibold transition',
                                   'bg-brand-500 text-white' => $isActive,
                                   'text-slate-600 hover:bg-slate-100 hover:text-slate-900' => ! $isActive,
                               ])>
                                {{ $item['label'] }}
                            </a>
                        @else
                            @php
                                $groupActive = collect($item['children'])->contains($menuIsActive);
                            @endphp

                            {{-- <details> so the menu still opens with no JS;
                                 the script below only adds closing behaviour. --}}
                            <details class="relative" data-nav-group>
                                <summary @class([
                                    'flex cursor-pointer list-none items-center gap-1 rounded-xl px-3.5 py-2 text-sm font-semibold transition',
                                    'bg-brand-500 text-white' => $groupActive,
                                    'text-slate-600 hover:bg-slate-100 hover:text-slate-900' => ! $groupActive,
                                ])>
                                    {{ $item['label'] }}
                                    <x-icon name="chevron-down" class="h-3.5 w-3.5" />
                                </summary>

                                <div class="absolute right-0 z-40 mt-2 w-60 overflow-hidden rounded-2xl border border-slate-200 bg-white py-1.5 shadow-lg">
                                    @foreach ($item['children'] as $child)
                                        @php $childActive = $menuIsActive($child); @endphp

                                        <a href="{{ $menuUrl($child) }}"
                                           @class([
                                               'block px-4 py-2.5 text-sm font-medium transition',
                                               'bg-brand-50 text-brand-700' => $childActive,
                                               'text-slate-700 hover:bg-slate-50 hover:text-slate-900' => ! $childActive,
                                           ])>
                                            {{ $child['label'] }}
                                        </a>
                                    @endforeach
                                </div>
                            </details>
                        @endif
                    @endforeach
                </nav>

                {{-- On a phone the groups are flattened under a heading rather
                     than nested: a dropdown inside a dropdown is awkward to
                     tap, and there is room to show everything at once. --}}
                <details class="lg:hidden">
                    <summary class="cursor-pointer list-none rounded-xl border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700">
                        เมนู
                    </summary>

                    <div class="absolute inset-x-0 mt-3 max-h-[75vh] overflow-y-auto border-y border-slate-200 bg-white p-3 shadow-lg">
                        @foreach ($menu as $item)
                            @if (! isset($item['children']))
                                <a href="{{ $menuUrl($item) }}"
                                   class="block rounded-xl px-3 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                    {{ $item['label'] }}
                                </a>
                            @else
                                <div class="mt-2 border-t border-slate-100 pt-2 first:mt-0 first:border-0 first:pt-0">
                                    <div class="px-3 pb-1 text-xs font-semibold uppercase tracking-wide text-slate-400">
                                        {{ $item['label'] }}
                                    </div>

                                    @foreach ($item['children'] as $child)
                                        <a href="{{ $menuUrl($child) }}"
                                           class="block rounded-xl px-3 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-100">
                                            {{ $child['label'] }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        @endforeach
                    </div>
                </details>
            </div>
        </div>
    </header>

    <script>
        // <details> opens and closes on its own; this only adds what it lacks —
        // closing when another opens, when a click lands outside, or on Escape.
        (function () {
            const groups = Array.from(document.querySelectorAll('[data-nav-group]'));
            if (!groups.length) return;

            function closeOthers(open) {
                groups.forEach(group => {
                    if (group !== open) group.open = false;
                });
            }

            groups.forEach(group => {
                group.addEventListener('toggle', () => {
                    if (group.open) closeOthers(group);
                });
            });

            document.addEventListener('click', event => {
                if (!event.target.closest('[data-nav-group]')) closeOthers(null);
            });

            document.addEventListener('keydown', event => {
                if (event.key === 'Escape') closeOthers(null);
            });
        })();
    </script>

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
