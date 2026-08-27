@props([
    'label',
    'value',
    'icon' => 'dot',
    'tone' => 'brand',      // brand | emerald | amber | rose | violet
    'delta' => null,        // ['text' =>, 'direction' => 'up'|'down'|'flat']
    'helper' => null,
    'href' => null,
    'featured' => false,
])

@php
    $tones = [
        'brand' => ['bg-brand-50', 'text-brand-600'],
        'emerald' => ['bg-emerald-50', 'text-emerald-600'],
        'amber' => ['bg-amber-50', 'text-amber-600'],
        'rose' => ['bg-rose-50', 'text-rose-600'],
        'violet' => ['bg-violet-50', 'text-violet-600'],
        'slate' => ['bg-slate-100', 'text-slate-600'],
    ];

    [$iconBg, $iconText] = $tones[$tone] ?? $tones['brand'];

    $deltaTone = match ($delta['direction'] ?? 'flat') {
        'up' => 'text-emerald-600',
        'down' => 'text-rose-600',
        default => 'text-slate-500',
    };

    $tag = $href ? 'a' : 'div';
@endphp

<{{ $tag }} @if ($href) href="{{ $href }}" @endif
    @class([
        'group relative overflow-hidden rounded-2xl p-5 transition',
        'border border-slate-200/80 bg-white shadow-[0_1px_2px_rgba(15,23,42,0.04),0_8px_24px_-12px_rgba(15,23,42,0.12)]' => ! $featured,
        'bg-gradient-to-br from-brand-500 to-brand-700 text-white shadow-lg shadow-brand-500/25' => $featured,
        'hover:-translate-y-0.5 hover:shadow-[0_1px_2px_rgba(15,23,42,0.04),0_16px_36px_-16px_rgba(2,171,255,0.35)]' => $href && ! $featured,
    ])>
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <div @class([
                'text-[11px] font-bold uppercase tracking-wider',
                'text-slate-500' => ! $featured,
                'text-white/80' => $featured,
            ])>{{ $label }}</div>

            <div @class([
                'mt-2 text-3xl font-bold leading-none',
                'text-slate-900' => ! $featured,
                'text-white' => $featured,
            ])>{{ $value }}</div>
        </div>

        <div @class([
            'flex h-11 w-11 shrink-0 items-center justify-center rounded-xl',
            "$iconBg $iconText" => ! $featured,
            'bg-white/20 text-white' => $featured,
        ])>
            <x-icon :name="$icon" />
        </div>
    </div>

    @if ($delta)
        <div @class(['mt-3 flex items-center gap-1.5 text-xs font-semibold', $deltaTone => ! $featured, 'text-white/85' => $featured])>
            @if (($delta['direction'] ?? null) === 'up')
                <x-icon name="trend-up" class="h-3.5 w-3.5" />
            @elseif (($delta['direction'] ?? null) === 'down')
                <x-icon name="trend-down" class="h-3.5 w-3.5" />
            @endif
            {{ $delta['text'] }}
        </div>
    @elseif ($helper)
        <div @class(['mt-3 text-xs', 'text-slate-500' => ! $featured, 'text-white/80' => $featured])>{{ $helper }}</div>
    @endif
</{{ $tag }}>
