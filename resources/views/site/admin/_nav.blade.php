@php
    /* One navigation bar for the public-site admin pages, matching ita/_nav. */
    $siteTabs = [
        ['label' => 'แบนเนอร์', 'route' => 'site.banners.index', 'active' => 'site.banners.*', 'icon' => 'box'],
        ['label' => 'ลิงก์สำคัญ', 'route' => 'site.links.index', 'active' => 'site.links.*', 'icon' => 'external-link'],
        ['label' => 'ข้อมูลโรงพยาบาล', 'route' => 'site.pages.index', 'active' => 'site.pages.*', 'icon' => 'document'],
        ['label' => 'ผู้บริหาร', 'route' => 'site.executives.index', 'active' => 'site.executives.*', 'icon' => 'users'],
    ];

    $visibleSiteTabs = collect($siteTabs)->filter(
        fn (array $tab) => Route::has($tab['route']) && auth()->user()?->can('site.view')
    );
@endphp

@if ($visibleSiteTabs->isNotEmpty())
    <div class="mb-6 flex flex-wrap items-center gap-2">
        @foreach ($visibleSiteTabs as $tab)
            <a href="{{ route($tab['route']) }}"
               @class([
                   'inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold transition',
                   'bg-slate-900 text-white shadow-sm' => request()->routeIs($tab['active']),
                   'bg-white text-slate-700 shadow-sm hover:bg-slate-50 hover:text-slate-900' => ! request()->routeIs($tab['active']),
               ])>
                <x-icon :name="$tab['icon']" class="h-4 w-4" />
                {{ $tab['label'] }}
            </a>
        @endforeach

        @if (Route::has('site.home'))
            <a href="{{ route('site.home') }}" target="_blank" rel="noopener"
               class="ml-auto inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-slate-900">
                <x-icon name="external-link" class="h-4 w-4" />
                ดูหน้าเว็บ
            </a>
        @endif
    </div>
@endif
