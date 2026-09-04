@php
    $description = setting('hospital_address')
        ? hospital_name().' — '.setting('hospital_address')
        : hospital_name();
@endphp

<x-site.shell :title="hospital_name()" :description="$description">
    {{-- Carousel --}}
    @if ($banners->isNotEmpty())
        <section class="bg-white" data-carousel>
            <div class="relative mx-auto max-w-6xl">
                {{--
                    Posters, not wide strips: the images editors actually upload
                    run around 2:1, and a wider frame with object-cover was
                    silently cutting a third off the top and bottom of some.
                    Every slide is now contained so the whole image is visible,
                    with a blurred copy of itself filling whatever the frame has
                    left over.
                --}}
                <div class="relative aspect-[2/1] max-h-[460px] overflow-hidden bg-slate-900 sm:rounded-b-3xl">
                    @foreach ($banners as $index => $banner)
                        @php
                            $slide = $banner->title || $banner->subtitle;
                        @endphp

                        <div data-slide="{{ $index }}"
                             @class(['absolute inset-0 transition-opacity duration-500', 'opacity-0' => $index > 0])
                             @if ($index > 0) aria-hidden="true" @endif>
                            @if ($banner->link_url)
                                <a href="{{ $banner->link_url }}" class="block h-full w-full">
                            @endif

                            {{-- Same src as the slide, so this costs no extra request. --}}
                            <img src="{{ $banner->image_url }}" alt="" aria-hidden="true"
                                 class="absolute inset-0 h-full w-full scale-110 object-cover blur-2xl"
                                 @if ($index > 1) loading="lazy" @endif>

                            {{--
                                The second slide is shown a few seconds in, so
                                deferring it means arriving at a blank frame.
                                Only the third onward is worth deferring.
                            --}}
                            <img src="{{ $banner->image_url }}"
                                 alt="{{ $banner->title ?? '' }}"
                                 @if ($index === 0) fetchpriority="high" @elseif ($index > 1) loading="lazy" @endif
                                 class="relative h-full w-full object-contain">

                            @if ($slide)
                                <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-slate-900/80 to-transparent p-5 sm:p-8">
                                    @if ($banner->title)
                                        <h2 class="text-lg font-bold text-white sm:text-2xl">{{ $banner->title }}</h2>
                                    @endif

                                    @if ($banner->subtitle)
                                        <p class="mt-1 text-sm text-slate-100 sm:text-base">{{ $banner->subtitle }}</p>
                                    @endif
                                </div>
                            @endif

                            @if ($banner->link_url)
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>

                @if ($banners->count() > 1)
                    <div class="flex items-center justify-center gap-2 py-3">
                        @foreach ($banners as $index => $banner)
                            <button type="button" data-dot="{{ $index }}"
                                    aria-label="แบนเนอร์ที่ {{ $index + 1 }}"
                                    @class([
                                        'h-2.5 rounded-full transition-all',
                                        'w-7 bg-brand-500' => $index === 0,
                                        'w-2.5 bg-slate-300 hover:bg-slate-400' => $index > 0,
                                    ])></button>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    @endif

    <div class="mx-auto max-w-6xl px-5 py-10 sm:px-6">
        {{-- Director --}}
        @if ($director)
            <section class="mb-10 overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
                <div class="flex flex-col gap-6 p-6 sm:flex-row sm:items-center sm:p-8">
                    @if ($director->photo_url)
                        <img src="{{ $director->photo_url }}" alt="{{ $director->name }}"
                             class="h-40 w-32 shrink-0 rounded-2xl object-cover ring-1 ring-slate-200"
                             loading="lazy">
                    @endif

                    <div class="min-w-0">
                        <div class="text-xs font-semibold uppercase tracking-wide text-brand-600">
                            {{ $director->position ?: 'ผู้อำนวยการ' }}
                        </div>

                        <h2 class="mt-1.5 text-xl font-bold text-slate-900 sm:text-2xl">{{ $director->name }}</h2>

                        <div class="mt-3 flex flex-wrap gap-x-5 gap-y-1 text-sm text-slate-600">
                            @if ($director->phone)
                                <span>โทร {{ $director->phone }}</span>
                            @endif

                            @if ($director->email)
                                <span>{{ $director->email }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </section>
        @endif

        {{-- Quick links --}}
        @if ($links->isNotEmpty())
            <section class="mb-10">
                <h2 class="mb-4 text-lg font-bold text-slate-900">ลิงก์สำคัญ</h2>

                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($links as $link)
                        <a href="{{ $link->url }}"
                           @if ($link->opens_new_tab) target="_blank" rel="noopener" @endif
                           class="group flex items-start gap-3.5 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-0.5 hover:ring-brand-300">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600 transition group-hover:bg-brand-500 group-hover:text-white">
                                <x-icon :name="$link->icon ?: 'document'" class="h-5 w-5" />
                            </span>

                            <span class="min-w-0">
                                <span class="block font-semibold text-slate-900">{{ $link->label }}</span>

                                @if ($link->description)
                                    <span class="mt-0.5 block text-sm text-slate-500">{{ $link->description }}</span>
                                @endif
                            </span>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- History and vision --}}
        @php
            $intro = collect(['history', 'vision'])
                ->map(fn (string $key) => $pages->get($key))
                ->filter(fn ($page) => $page && $page->body);
        @endphp

        @if ($intro->isNotEmpty())
            <section class="grid gap-4 md:grid-cols-2">
                @foreach ($intro as $page)
                    <article class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                        <h2 class="text-lg font-bold text-slate-900">{{ $page->title }}</h2>

                        {{-- Plain text, escaped. Nothing here is trusted HTML. --}}
                        <p class="mt-3 text-sm leading-relaxed text-slate-600">
                            {{ Str::limit($page->body, 280) }}
                        </p>

                        <a href="{{ route('site.page', $page->key) }}"
                           class="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-brand-600 hover:underline">
                            อ่านทั้งหมด
                            <x-icon name="chevron-right" class="h-3.5 w-3.5" />
                        </a>
                    </article>
                @endforeach
            </section>
        @endif

        @if ($banners->isEmpty() && $links->isEmpty() && ! $director && $intro->isEmpty())
            <div class="rounded-2xl bg-white p-12 text-center shadow-sm ring-1 ring-slate-200">
                <p class="font-semibold text-slate-700">ยังไม่มีเนื้อหาบนหน้าเว็บ</p>
                <p class="mt-1.5 text-sm text-slate-500">เพิ่มแบนเนอร์ ลิงก์ หรือข้อมูลโรงพยาบาลได้จากระบบหลังบ้าน</p>
            </div>
        @endif
    </div>

    @if ($banners->count() > 1)
        <script>
            (function () {
                const root = document.querySelector('[data-carousel]');
                if (!root) return;

                const slides = Array.from(root.querySelectorAll('[data-slide]'));
                const dots = Array.from(root.querySelectorAll('[data-dot]'));
                let current = 0;
                let timer = null;

                function show(next) {
                    slides[current].classList.add('opacity-0');
                    slides[current].setAttribute('aria-hidden', 'true');
                    dots[current]?.classList.remove('w-7', 'bg-brand-500');
                    dots[current]?.classList.add('w-2.5', 'bg-slate-300', 'hover:bg-slate-400');

                    current = (next + slides.length) % slides.length;

                    slides[current].classList.remove('opacity-0');
                    slides[current].removeAttribute('aria-hidden');
                    dots[current]?.classList.add('w-7', 'bg-brand-500');
                    dots[current]?.classList.remove('w-2.5', 'bg-slate-300', 'hover:bg-slate-400');
                }

                function start() {
                    stop();
                    timer = setInterval(() => show(current + 1), 6000);
                }

                function stop() {
                    if (timer) clearInterval(timer);
                }

                dots.forEach((dot, index) => {
                    dot.addEventListener('click', () => {
                        show(index);
                        start();
                    });
                });

                // Advancing under someone reading is worse than not advancing.
                root.addEventListener('mouseenter', stop);
                root.addEventListener('mouseleave', start);
                document.addEventListener('visibilitychange', () => document.hidden ? stop() : start());

                if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                    start();
                }
            })();
        </script>
    @endif
</x-site.shell>
