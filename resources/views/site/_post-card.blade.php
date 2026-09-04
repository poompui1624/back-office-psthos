@props(['post'])

<article class="group flex flex-col overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-0.5 hover:ring-brand-300">
    <a href="{{ route('site.post', $post->slug) }}" class="block">
        <div class="relative aspect-[16/9] overflow-hidden bg-slate-100">
            @if ($post->cover_image_url)
                <img src="{{ $post->cover_image_url }}" alt="{{ $post->title }}"
                     class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                     loading="lazy">
            @else
                <div class="flex h-full w-full items-center justify-center text-slate-300">
                    <x-icon name="document" class="h-10 w-10" />
                </div>
            @endif

            <span class="absolute left-3 top-3 rounded-lg bg-slate-900/75 px-2.5 py-1 text-xs font-semibold text-white backdrop-blur">
                {{ $post->category_label }}
            </span>

            @if ($post->is_pinned)
                <span class="absolute right-3 top-3 rounded-lg bg-amber-500 px-2.5 py-1 text-xs font-semibold text-white">
                    ปักหมุด
                </span>
            @endif
        </div>
    </a>

    <div class="flex flex-1 flex-col p-5">
        <time datetime="{{ $post->published_at?->toDateString() }}" class="text-xs text-slate-500">
            {{ $post->published_at?->format('d/m/') }}{{ $post->published_at ? thai_year((int) $post->published_at->year) : '' }}
        </time>

        <h3 class="mt-1.5 font-bold leading-snug text-slate-900">
            <a href="{{ route('site.post', $post->slug) }}" class="hover:text-brand-600">{{ $post->title }}</a>
        </h3>

        @if ($post->excerpt)
            <p class="mt-2 flex-1 text-sm leading-relaxed text-slate-600">{{ Str::limit($post->excerpt, 120) }}</p>
        @endif

        <a href="{{ route('site.post', $post->slug) }}"
           class="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-brand-600 hover:underline">
            อ่านต่อ
            <x-icon name="chevron-right" class="h-3.5 w-3.5" />
        </a>
    </div>
</article>
