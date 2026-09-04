<x-site.shell :title="$page->title.' — '.hospital_name()" :description="Str::limit($page->body, 160)">
    <div class="mx-auto max-w-4xl px-5 py-10 sm:px-6">
        <article class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-9">
            <h1 class="text-2xl font-bold text-slate-900 sm:text-3xl">{{ $page->title }}</h1>

            @if ($page->image_url)
                <img src="{{ $page->image_url }}" alt="{{ $page->title }}"
                     class="mt-6 w-full rounded-2xl object-cover" loading="lazy">
            @endif

            @if ($page->body)
                {{-- Escaped, with newlines turned into breaks. The body is plain
                     text by design, so nothing here may be rendered as HTML. --}}
                <div class="mt-6 whitespace-pre-line text-base leading-relaxed text-slate-700">{{ $page->body }}</div>
            @else
                <p class="mt-6 text-slate-500">ยังไม่มีเนื้อหาในหน้านี้</p>
            @endif
        </article>

        @if ($executives->isNotEmpty())
            <section class="mt-8">
                <h2 class="mb-4 text-lg font-bold text-slate-900">รายนามผู้บริหาร</h2>

                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($executives as $executive)
                        <article class="rounded-2xl bg-white p-5 text-center shadow-sm ring-1 ring-slate-200">
                            @if ($executive->photo_url)
                                <img src="{{ $executive->photo_url }}" alt="{{ $executive->name }}"
                                     class="mx-auto h-32 w-28 rounded-xl object-cover ring-1 ring-slate-200" loading="lazy">
                            @else
                                <div class="mx-auto flex h-32 w-28 items-center justify-center rounded-xl bg-slate-100 text-slate-400">
                                    <x-icon name="user" class="h-9 w-9" />
                                </div>
                            @endif

                            <div class="mt-3 font-semibold text-slate-900">{{ $executive->name }}</div>

                            @if ($executive->position)
                                <div class="mt-0.5 text-sm text-slate-500">{{ $executive->position }}</div>
                            @endif

                            @if ($executive->is_featured)
                                <div class="mt-2"><x-badge tone="brand">ผู้อำนวยการ</x-badge></div>
                            @endif
                        </article>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-site.shell>
