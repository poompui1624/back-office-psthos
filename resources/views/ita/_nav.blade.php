@php
    /*
        One navigation bar for every ITA admin page.

        Each page used to carry its own hand-picked set of cross-links, which
        left gaps — from the sub-topic list the only way to reach the fiscal
        years was through another page, and the upload and edit forms had no
        links at all.

        'active' matches the whole section, so a tab stays highlighted while
        creating or editing inside it, not only on the index.
    */
    $itaTabs = [
        [
            'label' => 'ไฟล์ ITA',
            'route' => 'ita.documents.index',
            'active' => 'ita.documents.*',
            'permission' => 'ita.view',
            'icon' => 'document',
        ],
        [
            'label' => 'ปีงบประมาณ',
            'route' => 'ita.fiscal-years.index',
            'active' => 'ita.fiscal-years.*',
            'permission' => 'ita.topic.manage',
            'icon' => 'calendar',
        ],
        [
            'label' => 'หัวข้อหลัก',
            'route' => 'ita.moit-topics.index',
            'active' => 'ita.moit-topics.*',
            'permission' => 'ita.topic.manage',
            'icon' => 'clipboard',
        ],
        [
            'label' => 'หัวข้อย่อย',
            'route' => 'ita.moit-sub-topics.index',
            'active' => 'ita.moit-sub-topics.*',
            'permission' => 'ita.topic.manage',
            'icon' => 'clipboard',
        ],
    ];

    $visibleItaTabs = collect($itaTabs)->filter(
        fn (array $tab) => Route::has($tab['route']) && auth()->user()?->can($tab['permission'])
    );
@endphp

@if ($visibleItaTabs->isNotEmpty())
    <div class="mb-6 flex flex-wrap items-center gap-2">
        @foreach ($visibleItaTabs as $tab)
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

        {{-- The public page is what the rest of this section feeds, so it sits
             apart from the tabs and opens in its own tab. --}}
        @if (Route::has('ita.public.index'))
            <a href="{{ route('ita.public.index') }}"
               target="_blank"
               rel="noopener"
               class="ml-auto inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-slate-900">
                <x-icon name="external-link" class="h-4 w-4" />
                หน้าแสดงผล
            </a>
        @endif
    </div>
@endif
