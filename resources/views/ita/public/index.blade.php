<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>การประเมินคุณธรรมและความโปร่งใส ITA</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-gray-900">
    <div class="mx-auto max-w-6xl px-6 py-10">
        <div class="text-center">
            <div class="mx-auto flex h-24 w-24 items-center justify-center rounded bg-blue-50 text-3xl font-bold text-blue-700">
                ITA
            </div>

            <h1 class="mt-8 text-3xl font-bold">
                การประเมินคุณธรรมและความโปร่งใส (ITA)
            </h1>

            <form method="GET" class="mt-4">
                <select onchange="window.location='{{ url('/ita-public') }}/' + this.value"
                        class="rounded border-gray-300">
                    @foreach ($fiscalYears as $year)
                        <option value="{{ $year->year }}" @selected($selectedYear->id === $year->id)>
                            ปีงบประมาณ {{ $year->year }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="mt-10">
            <div class="flex flex-wrap border-b border-gray-300">
                @foreach ($indicators as $indicatorNo => $items)
                    <a href="#indicator-{{ $indicatorNo }}"
                       class="border border-b-0 border-gray-300 px-5 py-3 text-sm font-medium text-emerald-600 hover:bg-gray-50">
                        ตัวชี้วัดที่ {{ $indicatorNo }}
                    </a>
                @endforeach
            </div>

            @forelse ($indicators as $indicatorNo => $items)
                @php
                    $firstTopic = $items->first();
                @endphp

                <section id="indicator-{{ $indicatorNo }}" class="pt-6">
                    <h2 class="text-2xl font-bold">
                        ตัวชี้วัดที่ {{ $indicatorNo }}
                        {{ $firstTopic?->indicator_title }}
                    </h2>

                    <div class="mt-5 space-y-6 border-t border-gray-300 pt-5">
                        @foreach ($items as $topic)
                            <div>
                                @php
                                    $topicFirstDocument = $topic->documents->first();
                                @endphp

                                @if ($topicFirstDocument && ! $topic->subTopics->count())
                                    <a href="{{ $topicFirstDocument->file_url }}"
                                       target="_blank"
                                       class="text-lg font-semibold text-emerald-600 hover:text-emerald-700 hover:underline">
                                        {{ $topic->code }} {{ $topic->title }}
                                    </a>
                                @else
                                    <h3 class="text-lg font-semibold text-emerald-600">
                                        {{ $topic->code }} {{ $topic->title }}
                                    </h3>
                                @endif

                                @if ($topic->documents->count() && $topic->subTopics->count())
                                    <ul class="mt-2 list-disc space-y-1 pl-6">
                                        @foreach ($topic->documents as $document)
                                            <li>
                                                <a href="{{ $document->file_url }}"
                                                   target="_blank"
                                                   class="text-blue-600 hover:underline">
                                                    {{ $document->title ?: $document->file_original_name }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif

                                @if ($topic->subTopics->count())
                                    <div class="mt-3 space-y-3">
                                        @foreach ($topic->subTopics as $subTopic)
                                            @php
                                                $firstDocument = $subTopic->documents->first();
                                            @endphp

                                            <div class="{{ $subTopic->is_heading ? 'pt-2' : 'pl-4' }}">
                                                @if ($subTopic->is_heading)
                                                    {{-- Introduces the items beneath it and never carries a file
                                                         of its own, so it is not marked as missing. --}}
                                                    <div class="font-semibold text-gray-700">
                                                        {{ $subTopic->code }} {{ $subTopic->title }}
                                                    </div>
                                                @elseif ($firstDocument)
                                                    <a href="{{ $firstDocument->file_url }}"
                                                       target="_blank"
                                                       class="font-medium text-emerald-600 hover:text-emerald-700 hover:underline">
                                                        {{ $subTopic->code }} {{ $subTopic->title }}
                                                    </a>

                                                    @if ($subTopic->documents->count() > 1)
                                                        <ul class="mt-1 list-disc space-y-1 pl-6 text-sm">
                                                            @foreach ($subTopic->documents as $document)
                                                                <li>
                                                                    <a href="{{ $document->file_url }}"
                                                                       target="_blank"
                                                                       class="text-blue-600 hover:underline">
                                                                        {{ $document->title ?: $document->file_original_name }}
                                                                    </a>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                @else
                                                    <div class="font-medium text-gray-400 line-through">
                                                        {{ $subTopic->code }} {{ $subTopic->title }}
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    @if (! $topic->documents->count())
                                        <div class="mt-2 text-sm text-gray-400 line-through">
                                            {{ $topic->code }} {{ $topic->title }}
                                        </div>
                                    @endif
                                @endif
                            </div>
                        @endforeach
                    </div>
                </section>
            @empty
                <div class="mt-10 rounded border border-gray-200 p-8 text-center text-gray-500">
                    ยังไม่มีข้อมูล ITA สำหรับปีงบประมาณนี้
                </div>
            @endforelse
        </div>
    </div>
</body>
</html>
