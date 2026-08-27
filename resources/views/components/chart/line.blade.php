@props([
    'series' => [],   // [['label' =>, 'color' => '#hex', 'points' => [n, n, ...]], ...]
    'labels' => [],
    'format' => 'number',
])

@php
    $sets = collect($series)->filter(fn ($s) => ! empty($s['points']))->values();

    $all = $sets->flatMap(fn ($s) => $s['points']);
    $max = (float) ($all->max() ?? 0);
    $min = (float) ($all->min() ?? 0);

    // Pad the band so the line never sits flat on an edge, and never divide by zero.
    $span = $max - $min;
    $pad = $span > 0 ? $span * 0.15 : max($max * 0.1, 1);
    $top = $max + $pad;
    $bottom = max(0, $min - $pad);
    $range = ($top - $bottom) ?: 1;

    $w = 720;
    $h = 240;
    $padX = 8;

    $project = function (array $points) use ($w, $h, $padX, $bottom, $range) {
        $count = max(count($points) - 1, 1);
        $usable = $w - ($padX * 2);

        return collect($points)->map(function ($value, $i) use ($count, $usable, $padX, $h, $bottom, $range) {
            return [
                'x' => round($padX + ($i / $count) * $usable, 2),
                'y' => round($h - (((float) $value - $bottom) / $range) * $h, 2),
                'v' => $value,
            ];
        })->all();
    };

    $rendered = $sets->map(function (array $set) use ($project) {
        $pts = $project($set['points']);

        return [
            'label' => $set['label'],
            'color' => $set['color'],
            'points' => $pts,
            'line' => collect($pts)->map(fn ($p) => "{$p['x']},{$p['y']}")->implode(' '),
        ];
    });

    $fmt = fn ($v) => $format === 'money'
        ? '฿' . number_format((float) $v, 0)
        : number_format((float) $v, 0);
@endphp

<div>
    <div class="mb-3 flex flex-wrap items-center gap-4">
        @foreach ($rendered as $set)
            <div class="flex items-center gap-2 text-xs font-medium text-slate-600">
                <span class="h-2.5 w-2.5 rounded-full" style="background: {{ $set['color'] }}"></span>
                {{ $set['label'] }}
            </div>
        @endforeach
    </div>

    @if ($rendered->isEmpty())
        <p class="py-16 text-center text-sm text-slate-500">ยังไม่มีข้อมูลย้อนหลัง</p>
    @else
        <div class="overflow-x-auto">
            <svg viewBox="0 0 {{ $w }} {{ $h + 26 }}" class="h-64 w-full min-w-[560px]" role="img">
                <defs>
                    @foreach ($rendered as $set)
                        <linearGradient id="fill-{{ $loop->index }}" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="{{ $set['color'] }}" stop-opacity="0.18" />
                            <stop offset="100%" stop-color="{{ $set['color'] }}" stop-opacity="0" />
                        </linearGradient>
                    @endforeach
                </defs>

                @foreach ([0, 0.25, 0.5, 0.75, 1] as $g)
                    <line x1="0" y1="{{ $h * $g }}" x2="{{ $w }}" y2="{{ $h * $g }}"
                          stroke="#e2e8f0" stroke-width="1" stroke-dasharray="4 4" />
                @endforeach

                @foreach ($rendered as $set)
                    @php
                        $first = $set['points'][0];
                        $last = $set['points'][count($set['points']) - 1];
                    @endphp

                    <polygon points="{{ $first['x'] }},{{ $h }} {{ $set['line'] }} {{ $last['x'] }},{{ $h }}"
                             fill="url(#fill-{{ $loop->index }})" />

                    <polyline points="{{ $set['line'] }}"
                              fill="none"
                              stroke="{{ $set['color'] }}"
                              stroke-width="2.5"
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              class="spark-path"
                              style="--dash: {{ $w * 2 }}" />

                    @foreach ($set['points'] as $p)
                        <circle cx="{{ $p['x'] }}" cy="{{ $p['y'] }}" r="7" fill="transparent">
                            <title>{{ $labels[$loop->index] ?? '' }} — {{ $set['label'] }}: {{ $fmt($p['v']) }}</title>
                        </circle>
                    @endforeach
                @endforeach

                @foreach ($labels as $i => $label)
                    @if ($i % 2 === 0 || count($labels) <= 7)
                        @php
                            $count = max(count($labels) - 1, 1);
                            $x = 8 + ($i / $count) * ($w - 16);
                        @endphp

                        <text x="{{ round($x, 2) }}" y="{{ $h + 18 }}"
                              text-anchor="middle" font-size="11" fill="#94a3b8">{{ $label }}</text>
                    @endif
                @endforeach
            </svg>
        </div>
    @endif
</div>
