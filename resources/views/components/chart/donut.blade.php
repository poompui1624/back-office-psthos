@props([
    'segments' => [],   // [['label' =>, 'value' =>, 'color' => '#hex'], ...]
    'total' => null,
    'caption' => 'ทั้งหมด',
])

@php
    $items = collect($segments)->filter(fn ($s) => ($s['value'] ?? 0) > 0)->values();
    $sum = $total ?? $items->sum('value');

    // Geometry: a stroked circle whose dash pattern draws each slice in turn.
    $radius = 56;
    $circumference = 2 * M_PI * $radius;

    $offset = 0;
    $slices = $items->map(function (array $item) use (&$offset, $sum, $circumference) {
        $fraction = $sum > 0 ? $item['value'] / $sum : 0;
        $length = $fraction * $circumference;

        $slice = [
            'label' => $item['label'],
            'value' => $item['value'],
            'color' => $item['color'],
            'percent' => $sum > 0 ? round($fraction * 100, 1) : 0,
            'dash' => $length,
            'gap' => $circumference - $length,
            'offset' => -$offset,
        ];

        $offset += $length;

        return $slice;
    });
@endphp

<div class="flex flex-col items-center gap-6 sm:flex-row sm:items-center">
    <div class="relative shrink-0">
        <svg viewBox="0 0 140 140" class="h-40 w-40" role="img"
             aria-label="{{ $caption }} {{ number_format($sum) }}">
            <circle cx="70" cy="70" r="{{ $radius }}" fill="none" stroke="#f1f5f9" stroke-width="18" />

            @foreach ($slices as $slice)
                <circle cx="70" cy="70" r="{{ $radius }}"
                        fill="none"
                        stroke="{{ $slice['color'] }}"
                        stroke-width="18"
                        stroke-dasharray="{{ $slice['dash'] }} {{ $slice['gap'] }}"
                        stroke-dashoffset="{{ $slice['offset'] }}"
                        class="donut-seg"
                        style="--circ: {{ $circumference }}; animation-delay: {{ $loop->index * 70 }}ms">
                    <title>{{ $slice['label'] }}: {{ number_format($slice['value']) }} ({{ $slice['percent'] }}%)</title>
                </circle>
            @endforeach
        </svg>

        <div class="pointer-events-none absolute inset-0 flex flex-col items-center justify-center">
            <div class="text-3xl font-bold leading-none text-slate-900">{{ number_format($sum) }}</div>
            <div class="mt-1 text-xs text-slate-500">{{ $caption }}</div>
        </div>
    </div>

    <div class="w-full min-w-0 flex-1 space-y-2">
        @forelse ($slices as $slice)
            <div class="flex items-center gap-3 text-sm">
                <span class="h-2.5 w-2.5 shrink-0 rounded-full" style="background: {{ $slice['color'] }}"></span>
                <span class="min-w-0 flex-1 truncate text-slate-700">{{ $slice['label'] }}</span>
                <span class="shrink-0 font-semibold text-slate-900">{{ number_format($slice['value']) }}</span>
                <span class="w-12 shrink-0 text-right text-xs text-slate-500">{{ $slice['percent'] }}%</span>
            </div>
        @empty
            <p class="py-8 text-center text-sm text-slate-500">ยังไม่มีข้อมูล</p>
        @endforelse
    </div>
</div>
