<x-site.shell :title="'ข่าวสารและกิจกรรม — '.hospital_name()"
              description="ข่าวประชาสัมพันธ์ ภาพกิจกรรม และความรู้สู่ประชาชน">
    <div class="mx-auto max-w-6xl px-5 py-10 sm:px-6">
        <header class="mb-6">
            <h1 class="text-2xl font-bold text-slate-900 sm:text-3xl">ข่าวสารและกิจกรรม</h1>
            <p class="mt-1.5 text-slate-600">ข่าวประชาสัมพันธ์ ภาพกิจกรรม และความรู้สู่ประชาชน</p>
        </header>

        <div class="mb-6 flex flex-wrap gap-2">
            @php
                $tabs = ['' => 'ทั้งหมด'] + \App\Models\SitePost::CATEGORIES;
            @endphp

            @foreach ($tabs as $value => $label)
                @php
                    $total = $value === '' ? $counts->sum() : ($counts[$value] ?? 0);
                @endphp

                <a href="{{ $value === '' ? route('site.news') : route('site.news', ['category' => $value]) }}"
                   @class([
                       'inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold transition',
                       'bg-brand-500 text-white shadow-sm' => $category === $value,
                       'bg-white text-slate-700 shadow-sm ring-1 ring-slate-200 hover:bg-slate-50' => $category !== $value,
                   ])>
                    {{ $label }}

                    <span @class([
                        'rounded-md px-1.5 text-xs',
                        'bg-white/20' => $category === $value,
                        'bg-slate-100 text-slate-500' => $category !== $value,
                    ])>{{ $total }}</span>
                </a>
            @endforeach
        </div>

        @if ($posts->isEmpty())
            <div class="rounded-2xl bg-white p-12 text-center shadow-sm ring-1 ring-slate-200">
                <p class="font-semibold text-slate-700">ยังไม่มีเนื้อหาในหมวดนี้</p>
                <p class="mt-1.5 text-sm text-slate-500">โปรดกลับมาใหม่อีกครั้ง</p>
            </div>
        @else
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($posts as $post)
                    @include('site._post-card', ['post' => $post])
                @endforeach
            </div>

            <div class="mt-8">{{ $posts->links() }}</div>
        @endif
    </div>
</x-site.shell>
