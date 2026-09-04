<x-site.shell :title="'ภาพกิจกรรม — '.hospital_name()"
              description="ภาพกิจกรรมของโรงพยาบาล">
    <div class="mx-auto max-w-6xl px-5 py-10 sm:px-6">
        <header class="mb-6">
            <h1 class="text-2xl font-bold text-slate-900 sm:text-3xl">ภาพกิจกรรม</h1>
            <p class="mt-1.5 text-slate-600">กิจกรรมและงานของโรงพยาบาล</p>
        </header>

        @if ($posts->isEmpty())
            <div class="rounded-2xl bg-white p-12 text-center shadow-sm ring-1 ring-slate-200">
                <p class="font-semibold text-slate-700">ยังไม่มีภาพกิจกรรม</p>
            </div>
        @else
            <div class="space-y-8">
                @foreach ($posts as $post)
                    @php
                        // The cover stands in when a post has no gallery of its
                        // own, so an activity always shows at least one image.
                        $images = $post->images->isNotEmpty()
                            ? $post->images
                            : collect($post->cover_image_url ? [(object) ['image_url' => $post->cover_image_url, 'caption' => null]] : []);
                    @endphp

                    @continue ($images->isEmpty())

                    <section>
                        <div class="mb-3 flex flex-wrap items-baseline justify-between gap-2">
                            <h2 class="font-bold text-slate-900">
                                <a href="{{ route('site.post', $post->slug) }}" class="hover:text-brand-600 hover:underline">
                                    {{ $post->title }}
                                </a>
                            </h2>

                            <time class="text-sm text-slate-500">
                                {{ $post->published_at?->format('d/m/') }}{{ $post->published_at ? thai_year((int) $post->published_at->year) : '' }}
                            </time>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-3 lg:grid-cols-4">
                            @foreach ($images->take(8) as $image)
                                <a href="{{ $image->image_url }}" target="_blank" rel="noopener"
                                   class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
                                    <img src="{{ $image->image_url }}" alt="{{ $image->caption ?? $post->title }}"
                                         class="aspect-square w-full object-cover transition hover:scale-105"
                                         loading="lazy">
                                </a>
                            @endforeach
                        </div>

                        @if ($post->images->count() > 8)
                            <a href="{{ route('site.post', $post->slug) }}"
                               class="mt-3 inline-flex items-center gap-1 text-sm font-semibold text-brand-600 hover:underline">
                                ดูอีก {{ $post->images->count() - 8 }} ภาพ
                                <x-icon name="chevron-right" class="h-3.5 w-3.5" />
                            </a>
                        @endif
                    </section>
                @endforeach
            </div>

            <div class="mt-8">{{ $posts->links() }}</div>
        @endif
    </div>
</x-site.shell>
