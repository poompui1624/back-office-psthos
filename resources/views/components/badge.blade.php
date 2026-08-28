@props(['tone' => 'slate', 'dot' => false])

@php
    $tones = [
        'brand' => 'bg-brand-50 text-brand-700',
        'success' => 'bg-emerald-100 text-emerald-700',
        'warning' => 'bg-amber-100 text-amber-700',
        'danger' => 'bg-rose-100 text-rose-700',
        'info' => 'bg-sky-100 text-sky-700',
        'violet' => 'bg-violet-100 text-violet-700',
        'slate' => 'bg-slate-100 text-slate-600',
    ];

    $dots = [
        'brand' => 'bg-brand-500',
        'success' => 'bg-emerald-500',
        'warning' => 'bg-amber-500',
        'danger' => 'bg-rose-500',
        'info' => 'bg-sky-500',
        'violet' => 'bg-violet-500',
        'slate' => 'bg-slate-400',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'pill ' . ($tones[$tone] ?? $tones['slate'])]) }}>
    @if ($dot)
        <span class="h-1.5 w-1.5 rounded-full {{ $dots[$tone] ?? $dots['slate'] }}"></span>
    @endif

    {{ $slot }}
</span>
