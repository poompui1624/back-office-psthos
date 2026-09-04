<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>การประเมินคุณธรรมและความโปร่งใส (ITA) {{ $selectedYear->year }} — {{ hospital_name() }}</title>

    <meta name="description"
          content="เอกสารประกอบการประเมินคุณธรรมและความโปร่งใสในการดำเนินงานของหน่วยงานภาครัฐ (ITA) ประจำปีงบประมาณ {{ $selectedYear->year }} ของ{{ hospital_name() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900">
    @php
        $logoUrl = hospital_logo_url();
    @endphp

    {{-- Header --}}
    <header class="bg-gradient-to-br from-brand-600 via-brand-500 to-sky-400 text-white">
        <div class="mx-auto max-w-6xl px-5 py-10 sm:px-6 sm:py-14">
            <div class="flex flex-col items-center gap-5 text-center">
                @if ($logoUrl)
                    <img src="{{ $logoUrl }}" alt="{{ hospital_name() }}"
                         class="h-20 w-20 rounded-2xl bg-white object-contain p-2 shadow-lg shadow-brand-900/20">
                @else
                    <div class="flex h-20 w-20 items-center justify-center rounded-2xl bg-white/15 text-2xl font-bold ring-1 ring-white/30">
                        ITA
                    </div>
                @endif

                <div>
                    <p class="text-sm font-medium text-sky-50">{{ hospital_name() }}</p>

                    <h1 class="mt-1.5 text-2xl font-bold tracking-tight sm:text-4xl">
                        การประเมินคุณธรรมและความโปร่งใส
                    </h1>

                    <p class="mt-2 text-sm text-sky-50 sm:text-base">
                        Integrity and Transparency Assessment (ITA)
                    </p>
                </div>

                <form method="GET" class="mt-1">
                    <label for="fiscal-year" class="sr-only">เลือกปีงบประมาณ</label>

                    <select id="fiscal-year"
                            onchange="window.location='{{ url('/ita-public') }}/' + this.value"
                            class="rounded-xl border-0 bg-white/15 py-2.5 pl-4 pr-10 text-sm font-semibold text-white ring-1 ring-white/30 backdrop-blur transition hover:bg-white/25 focus:ring-2 focus:ring-white">
                        @foreach ($fiscalYears as $year)
                            <option value="{{ $year->year }}" class="text-slate-900" @selected($selectedYear->id === $year->id)>
                                ปีงบประมาณ {{ $year->year }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            {{-- Overall progress. What share of the assessment is published is
                 the question this page exists to answer. --}}
            @if ($progress['total'] > 0)
                <div class="mx-auto mt-9 max-w-2xl rounded-2xl bg-white/10 p-5 ring-1 ring-white/20 backdrop-blur">
                    <div class="flex items-end justify-between gap-4">
                        <div>
                            <div class="text-xs font-medium uppercase tracking-wide text-sky-100">เอกสารที่เผยแพร่แล้ว</div>
                            <div class="mt-1 text-2xl font-bold sm:text-3xl">
                                {{ number_format($progress['published']) }}
                                <span class="text-base font-medium text-sky-100">/ {{ number_format($progress['total']) }} รายการ</span>
                            </div>
                        </div>

                        <div class="text-3xl font-bold sm:text-4xl">{{ $progress['percent'] }}%</div>
                    </div>

                    <div class="mt-4 h-2.5 overflow-hidden rounded-full bg-white/20">
                        <div class="h-full rounded-full bg-white transition-all"
                             style="width: {{ $progress['percent'] }}%"></div>
                    </div>
                </div>
            @endif
        </div>
    </header>

    {{-- Indicator navigation --}}
    <nav class="sticky top-0 z-20 border-b border-slate-200 bg-white/90 backdrop-blur">
        <div class="mx-auto max-w-6xl px-5 sm:px-6">
            <div class="flex gap-1.5 overflow-x-auto py-3">
                @foreach ($indicators as $indicatorNo => $items)
                    @php
                        $indicatorProgress = $progress['byIndicator'][$indicatorNo] ?? ['percent' => 0];
                    @endphp

                    <a href="#indicator-{{ $indicatorNo }}"
                       data-indicator-link="{{ $indicatorNo }}"
                       class="flex shrink-0 items-center gap-2 rounded-xl px-3.5 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 hover:text-slate-900">
                        ตัวชี้วัดที่ {{ $indicatorNo }}

                        <span class="rounded-full bg-slate-100 px-1.5 py-0.5 text-xs font-bold tabular-nums text-slate-500">
                            {{ $indicatorProgress['percent'] }}%
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </nav>

    <main class="mx-auto max-w-6xl px-5 py-8 sm:px-6 sm:py-10">
        {{-- Filter. With 234 items across 22 topics, finding one by scrolling
             is the slowest part of using this page. --}}
        <div class="mb-8">
            <label for="item-filter" class="sr-only">ค้นหาหัวข้อหรือเอกสาร</label>

            <div class="relative">
                <svg class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M17 11a6 6 0 1 1-12 0 6 6 0 0 1 12 0Z" />
                </svg>

                <input type="search" id="item-filter"
                       placeholder="ค้นหาหัวข้อ MOIT หรือชื่อเอกสาร…"
                       autocomplete="off"
                       class="w-full rounded-2xl border-0 bg-white py-3.5 pl-12 pr-4 text-sm shadow-sm ring-1 ring-slate-200 transition placeholder:text-slate-400 focus:ring-2 focus:ring-brand-400">
            </div>

            <p id="filter-empty" class="mt-4 hidden rounded-2xl bg-white p-8 text-center text-sm text-slate-500 ring-1 ring-slate-200">
                ไม่พบหัวข้อหรือเอกสารที่ตรงกับคำค้นหา
            </p>
        </div>

        @forelse ($indicators as $indicatorNo => $items)
            @php
                $indicatorProgress = $progress['byIndicator'][$indicatorNo] ?? ['total' => 0, 'published' => 0, 'percent' => 0];
            @endphp

            <section id="indicator-{{ $indicatorNo }}" data-indicator="{{ $indicatorNo }}" class="mb-10 scroll-mt-20">
                <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
                    {{-- indicator_title already reads "ตัวชี้วัดที่ 1 …", so the
                         number is not repeated in front of it. --}}
                    <h2 class="text-xl font-bold text-slate-900 sm:text-2xl">
                        {{ $items->first()?->indicator_title ?? 'ตัวชี้วัดที่ '.$indicatorNo }}
                    </h2>

                    <div class="flex items-center gap-2.5 text-sm">
                        <div class="h-1.5 w-24 overflow-hidden rounded-full bg-slate-200">
                            <div class="h-full rounded-full {{ $indicatorProgress['percent'] === 100 ? 'bg-emerald-500' : 'bg-brand-500' }}"
                                 style="width: {{ $indicatorProgress['percent'] }}%"></div>
                        </div>

                        <span class="font-semibold tabular-nums text-slate-600">
                            {{ $indicatorProgress['published'] }}/{{ $indicatorProgress['total'] }}
                        </span>
                    </div>
                </div>

                <div class="space-y-4">
                    @foreach ($items as $topic)
                        @php
                            $topicProgress = $progress['byTopic'][$topic->id] ?? ['total' => 0, 'published' => 0];
                            $topicComplete = $topicProgress['total'] > 0 && $topicProgress['published'] === $topicProgress['total'];
                            $topicFirstDocument = $topic->documents->first();
                        @endphp

                        <article data-topic
                                 class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
                            <div class="flex flex-wrap items-start justify-between gap-3 border-b border-slate-100 bg-slate-50/60 px-5 py-4">
                                <div class="flex min-w-0 items-start gap-3">
                                    <span class="shrink-0 rounded-lg bg-brand-500 px-2.5 py-1 text-xs font-bold text-white">
                                        {{ $topic->code }}
                                    </span>

                                    <h3 class="min-w-0 font-semibold leading-relaxed text-slate-900">
                                        @if ($topicFirstDocument && ! $topic->subTopics->count())
                                            <a href="{{ $topicFirstDocument->file_url }}" target="_blank" rel="noopener"
                                               class="text-brand-700 hover:underline">
                                                {{ $topic->title }}
                                            </a>
                                        @else
                                            {{ $topic->title }}
                                        @endif
                                    </h3>
                                </div>

                                @if ($topicProgress['total'] > 0)
                                    <span @class([
                                        'shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold',
                                        'bg-emerald-100 text-emerald-700' => $topicComplete,
                                        'bg-slate-200 text-slate-600' => ! $topicComplete,
                                    ])>
                                        {{ $topicProgress['published'] }}/{{ $topicProgress['total'] }}
                                    </span>
                                @endif
                            </div>

                            <div class="px-5 py-4">
                                {{-- Files attached to the topic itself rather than to one of its items. --}}
                                @if ($topic->documents->count() && $topic->subTopics->count())
                                    <ul class="mb-4 space-y-1.5">
                                        @foreach ($topic->documents as $document)
                                            <li data-item data-text="{{ $document->title ?: $document->file_original_name }}">
                                                @include('ita.public._file-link', ['document' => $document])
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif

                                @if ($topic->subTopics->count())
                                    <div class="space-y-2.5">
                                        @foreach ($topic->subTopics as $subTopic)
                                            @php
                                                $firstDocument = $subTopic->documents->first();
                                            @endphp

                                            @if ($subTopic->is_heading)
                                                {{-- Introduces the items beneath it and never carries a file,
                                                     so it is not shown as missing one. --}}
                                                <div data-item data-text="{{ $subTopic->code }} {{ $subTopic->title }}"
                                                     class="pt-2 text-sm font-semibold leading-relaxed text-slate-700 first:pt-0">
                                                    {{ $subTopic->code }} {{ $subTopic->title }}
                                                </div>
                                            @elseif ($firstDocument)
                                                <div data-item data-text="{{ $subTopic->code }} {{ $subTopic->title }}" class="pl-4">
                                                    <a href="{{ $firstDocument->file_url }}" target="_blank" rel="noopener"
                                                       class="group flex items-start gap-2.5 text-sm leading-relaxed">
                                                        <svg class="mt-0.5 h-4 w-4 shrink-0 text-emerald-500" fill="none"
                                                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                                        </svg>

                                                        <span class="text-emerald-700 group-hover:underline">
                                                            {{ $subTopic->code }} {{ $subTopic->title }}
                                                        </span>
                                                    </a>

                                                    @if ($subTopic->documents->count() > 1)
                                                        <ul class="mt-1.5 space-y-1 pl-7">
                                                            @foreach ($subTopic->documents as $document)
                                                                <li>@include('ita.public._file-link', ['document' => $document])</li>
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                </div>
                                            @else
                                                {{-- Not published yet. Said plainly rather than struck through:
                                                     this is a public record, and a line through the text reads
                                                     as withdrawn rather than pending. --}}
                                                <div data-item data-text="{{ $subTopic->code }} {{ $subTopic->title }}"
                                                     class="flex items-start gap-2.5 pl-4 text-sm leading-relaxed text-slate-400">
                                                    <svg class="mt-0.5 h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24"
                                                         stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                        <circle cx="12" cy="12" r="9" stroke-dasharray="3 3" />
                                                    </svg>

                                                    <span>
                                                        {{ $subTopic->code }} {{ $subTopic->title }}
                                                        <span class="ml-1 whitespace-nowrap text-xs text-slate-400">(ยังไม่เผยแพร่)</span>
                                                    </span>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @elseif (! $topic->documents->count())
                                    <p class="text-sm text-slate-400">ยังไม่เผยแพร่เอกสารสำหรับหัวข้อนี้</p>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @empty
            <div class="rounded-2xl bg-white p-12 text-center shadow-sm ring-1 ring-slate-200">
                <p class="font-semibold text-slate-700">ยังไม่มีข้อมูล ITA สำหรับปีงบประมาณนี้</p>
                <p class="mt-1.5 text-sm text-slate-500">โปรดเลือกปีงบประมาณอื่น</p>
            </div>
        @endforelse
    </main>

    <footer class="border-t border-slate-200 bg-white">
        <div class="mx-auto max-w-6xl px-5 py-8 text-center text-sm text-slate-500 sm:px-6">
            <p class="font-medium text-slate-700">{{ hospital_name() }}</p>
            <p class="mt-1">การประเมินคุณธรรมและความโปร่งใส ประจำปีงบประมาณ {{ $selectedYear->year }}</p>
        </div>
    </footer>

    <script>
        (function () {
            // Filter items in place. The whole assessment is already on the page,
            // so this needs no request and works with the browser's own find too.
            const filter = document.getElementById('item-filter');
            const emptyNotice = document.getElementById('filter-empty');
            const topics = Array.from(document.querySelectorAll('[data-topic]'));
            const sections = Array.from(document.querySelectorAll('[data-indicator]'));

            if (filter) {
                filter.addEventListener('input', function () {
                    const term = this.value.trim().toLowerCase();

                    topics.forEach(topic => {
                        const items = Array.from(topic.querySelectorAll('[data-item]'));
                        const heading = topic.textContent.toLowerCase();
                        let anyVisible = false;

                        if (term === '') {
                            items.forEach(item => item.hidden = false);
                            topic.hidden = false;
                            anyVisible = true;
                        } else {
                            items.forEach(item => {
                                const matches = (item.dataset.text || '').toLowerCase().includes(term);
                                item.hidden = !matches;
                                if (matches) anyVisible = true;
                            });

                            // Keep a topic whose own heading matches, even when no
                            // single item does.
                            if (!anyVisible && heading.includes(term)) {
                                items.forEach(item => item.hidden = false);
                                anyVisible = true;
                            }

                            topic.hidden = !anyVisible;
                        }
                    });

                    // Hide an indicator once everything under it is filtered out.
                    sections.forEach(section => {
                        section.hidden = !section.querySelector('[data-topic]:not([hidden])');
                    });

                    if (emptyNotice) {
                        emptyNotice.classList.toggle('hidden', topics.some(topic => !topic.hidden));
                    }
                });
            }

            // Mark the indicator currently in view.
            const links = Array.from(document.querySelectorAll('[data-indicator-link]'));
            const active = ['bg-brand-500', 'text-white', 'shadow-sm'];
            const idle = ['text-slate-600'];

            function highlight(indicatorNo) {
                links.forEach(link => {
                    const isActive = link.dataset.indicatorLink === String(indicatorNo);
                    link.classList.toggle('bg-brand-500', isActive);
                    link.classList.toggle('text-white', isActive);
                    link.classList.toggle('shadow-sm', isActive);
                    link.classList.toggle('text-slate-600', !isActive);

                    const badge = link.querySelector('span');
                    if (badge) {
                        badge.classList.toggle('bg-white/20', isActive);
                        badge.classList.toggle('text-white', isActive);
                        badge.classList.toggle('bg-slate-100', !isActive);
                        badge.classList.toggle('text-slate-500', !isActive);
                    }
                });
            }

            if ('IntersectionObserver' in window && sections.length) {
                const observer = new IntersectionObserver(entries => {
                    entries
                        .filter(entry => entry.isIntersecting)
                        .forEach(entry => highlight(entry.target.dataset.indicator));
                }, { rootMargin: '-72px 0px -70% 0px' });

                sections.forEach(section => observer.observe(section));
            }
        })();
    </script>
</body>
</html>
