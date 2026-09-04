<x-site.shell :title="'เอกสารเผยแพร่ — '.hospital_name()"
              description="จัดซื้อจัดจ้าง รับสมัครงาน รายงานประจำปี และเอกสารเผยแพร่อื่น ๆ">
    <div class="mx-auto max-w-5xl px-5 py-10 sm:px-6">
        <header class="mb-6">
            <h1 class="text-2xl font-bold text-slate-900 sm:text-3xl">เอกสารเผยแพร่</h1>
            <p class="mt-1.5 text-slate-600">จัดซื้อจัดจ้าง รับสมัครงาน รายงานประจำปี และเอกสารอื่น ๆ</p>
        </header>

        <form method="GET" action="{{ route('site.documents') }}" class="mb-5">
            @if ($category)
                <input type="hidden" name="category" value="{{ $category }}">
            @endif

            <div class="flex gap-2">
                <input type="search" name="search" value="{{ $search }}" placeholder="ค้นหาชื่อเอกสาร"
                       class="w-full rounded-xl border-slate-200 bg-white text-sm shadow-sm focus:border-brand-400 focus:ring-brand-200">

                <button type="submit" class="shrink-0 rounded-xl bg-brand-500 px-5 py-2 text-sm font-semibold text-white hover:bg-brand-600">
                    ค้นหา
                </button>
            </div>
        </form>

        <div class="mb-6 flex flex-wrap gap-2">
            @php
                $tabs = ['' => 'ทั้งหมด'] + \App\Models\SiteDocument::categories();
            @endphp

            @foreach ($tabs as $value => $label)
                @php
                    $total = $value === '' ? $counts->sum() : ($counts[$value] ?? 0);
                @endphp

                @continue ($value !== '' && $total === 0 && $category !== $value)

                <a href="{{ $value === '' ? route('site.documents') : route('site.documents', ['category' => $value]) }}"
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

        @if ($documents->isEmpty())
            <div class="rounded-2xl bg-white p-12 text-center shadow-sm ring-1 ring-slate-200">
                <p class="font-semibold text-slate-700">ไม่พบเอกสาร</p>
                <p class="mt-1.5 text-sm text-slate-500">ลองเปลี่ยนคำค้นหาหรือเลือกหมวดอื่น</p>
            </div>
        @else
            <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
                @foreach ($documents as $document)
                    <div class="flex flex-col gap-3 border-b border-slate-100 p-4 last:border-0 sm:flex-row sm:items-center">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                            <x-icon :name="$document->icon" class="h-5 w-5" />
                        </span>

                        <div class="min-w-0 flex-1">
                            <a href="{{ route('site.document', $document) }}"
                               class="font-semibold text-slate-900 hover:text-brand-600 hover:underline">
                                {{ $document->title }}
                            </a>

                            <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-slate-500">
                                <span class="rounded-md bg-slate-100 px-1.5 py-0.5">{{ $document->category_label }}</span>
                                <span>{{ $document->published_at?->format('d/m/') }}{{ $document->published_at ? thai_year((int) $document->published_at->year) : '' }}</span>
                                <span class="uppercase">{{ $document->file_extension }} &middot; {{ $document->file_size_human }}</span>
                            </div>
                        </div>

                        <div class="flex shrink-0 gap-2">
                            @if ($document->isViewableInBrowser())
                                <a href="{{ route('site.document.preview', $document) }}"
                                   target="_blank" rel="noopener"
                                   class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-brand-500 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-600">
                                    <x-icon name="search" class="h-4 w-4" />
                                    แสดงไฟล์
                                </a>
                            @endif

                            <a href="{{ route('site.document.download', $document) }}"
                               @class([
                                   'inline-flex items-center justify-center gap-1.5 rounded-xl px-4 py-2 text-sm font-semibold',
                                   'bg-brand-500 text-white hover:bg-brand-600' => ! $document->isViewableInBrowser(),
                                   'border border-slate-200 bg-white text-slate-700 hover:bg-slate-50' => $document->isViewableInBrowser(),
                               ])>
                                <x-icon name="upload" class="h-4 w-4 rotate-180" />
                                ดาวน์โหลด
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">{{ $documents->links() }}</div>
        @endif
    </div>
</x-site.shell>
