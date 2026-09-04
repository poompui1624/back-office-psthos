<x-site.shell :title="$post->title.' — '.hospital_name()"
              :description="$post->excerpt ?: Str::limit($post->body, 160)">
    <div class="mx-auto max-w-4xl px-5 py-10 sm:px-6">
        <nav class="mb-5 text-sm text-slate-500">
            <a href="{{ route('site.news') }}" class="hover:text-brand-600 hover:underline">ข่าวสารและกิจกรรม</a>
            <span class="mx-1.5">/</span>
            <a href="{{ route('site.news', ['category' => $post->category]) }}" class="hover:text-brand-600 hover:underline">
                {{ $post->category_label }}
            </a>
        </nav>

        <article class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
            @if ($post->cover_image_url)
                <img src="{{ $post->cover_image_url }}" alt="{{ $post->title }}"
                     class="max-h-[420px] w-full object-cover" fetchpriority="high">
            @endif

            <div class="p-6 sm:p-9">
                <div class="flex flex-wrap items-center gap-3 text-sm text-slate-500">
                    <span class="rounded-lg bg-brand-50 px-2.5 py-1 text-xs font-semibold text-brand-700">
                        {{ $post->category_label }}
                    </span>

                    <time datetime="{{ $post->published_at?->toDateString() }}">
                        {{ $post->published_at?->format('d/m/') }}{{ $post->published_at ? thai_year((int) $post->published_at->year) : '' }}
                    </time>

                    <span>เปิดอ่าน {{ number_format($post->view_count) }} ครั้ง</span>
                </div>

                <h1 class="mt-3 text-2xl font-bold leading-snug text-slate-900 sm:text-3xl">{{ $post->title }}</h1>

                @if ($post->excerpt)
                    <p class="mt-4 border-l-4 border-brand-200 pl-4 text-base leading-relaxed text-slate-600">
                        {{ $post->excerpt }}
                    </p>
                @endif

                @if ($post->body)
                    {{-- Escaped, newlines preserved. The body is plain text by
                         design; this page is open to the whole internet. --}}
                    <div class="mt-6 whitespace-pre-line text-base leading-relaxed text-slate-700">{{ $post->body }}</div>
                @endif
            </div>
        </article>

        @if ($post->files->isNotEmpty())
            <section class="mt-8">
                <h2 class="mb-4 text-lg font-bold text-slate-900">เอกสารแนบ</h2>

                <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
                    @foreach ($post->files as $file)
                        <a href="{{ route('site.post.file', $file) }}"
                           class="flex items-center gap-4 border-b border-slate-100 p-4 transition last:border-0 hover:bg-slate-50">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                                <x-icon :name="$file->icon" class="h-5 w-5" />
                            </span>

                            <span class="min-w-0 flex-1">
                                <span class="block font-semibold text-slate-900">{{ $file->display_name }}</span>
                                <span class="mt-0.5 block text-xs uppercase text-slate-500">
                                    {{ $file->file_extension }} &middot; {{ $file->file_size_human }}
                                </span>
                            </span>

                            <span class="inline-flex shrink-0 items-center gap-1.5 rounded-xl bg-brand-500 px-3.5 py-2 text-sm font-semibold text-white">
                                <x-icon name="upload" class="h-4 w-4 rotate-180" />
                                ดาวน์โหลด
                            </span>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        @if ($post->images->isNotEmpty())
            <section class="mt-8">
                <h2 class="mb-4 text-lg font-bold text-slate-900">ภาพประกอบ</h2>

                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($post->images as $image)
                        <figure class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
                            <a href="{{ $image->image_url }}" target="_blank" rel="noopener">
                                <img src="{{ $image->image_url }}" alt="{{ $image->caption ?? '' }}"
                                     class="aspect-[4/3] w-full object-cover transition hover:scale-105"
                                     loading="lazy">
                            </a>

                            @if ($image->caption)
                                <figcaption class="p-3 text-sm text-slate-600">{{ $image->caption }}</figcaption>
                            @endif
                        </figure>
                    @endforeach
                </div>
            </section>
        @endif

        @if ($related->isNotEmpty())
            <section class="mt-10">
                <h2 class="mb-4 text-lg font-bold text-slate-900">เรื่องที่เกี่ยวข้อง</h2>

                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($related as $item)
                        @include('site._post-card', ['post' => $item])
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-site.shell>
