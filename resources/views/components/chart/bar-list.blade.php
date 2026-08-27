@props([
    'rows' => [],   // [['label' =>, 'value' =>, 'color' => '#hex'|null], ...]
    'format' => 'number',
    'empty' => 'ยังไม่มีข้อมูล',
])

@php
    $items = collect($rows)->values();
    // Scale against the largest row so the longest bar always fills the track.
    $max = (float) ($items->max('value') ?: 1);

    $fmt = fn ($v) => $format === 'money'
        ? '฿' . number_format((float) $v, 0)
        : number_format((float) $v, 0);
@endphp

<div class="space-y-3">
    @forelse ($items as $row)
        @php $pct = $max > 0 ? max(round(((float) $row['value'] / $max) * 100, 1), 1.5) : 0; @endphp

        <div>
            <div class="mb-1.5 flex items-baseline justify-between gap-3 text-sm">
                <span class="min-w-0 truncate text-slate-700">{{ $row['label'] }}</span>
                <span class="shrink-0 font-semibold text-slate-900">{{ $fmt($row['value']) }}</span>
            </div>

            <div class="h-2 overflow-hidden rounded-full bg-slate-100">
                <div class="h-full rounded-full transition-all duration-700"
                     style="width: {{ $pct }}%; background: {{ $row['color'] ?? '#02abff' }}"></div>
            </div>
        </div>
    @empty
        <p class="py-8 text-center text-sm text-slate-500">{{ $empty }}</p>
    @endforelse
</div>
