@props([
    'variant' => 'primary',   // primary | secondary | success | danger | warning | ghost
    'size' => 'md',           // sm | md
    'icon' => null,
    'href' => null,
    'type' => 'submit',
])

@php
    $variants = [
        'primary' => 'bg-brand-500 text-white shadow-sm shadow-brand-500/25 hover:bg-brand-600 focus-visible:outline-brand-500',
        'secondary' => 'border border-slate-200 bg-white text-slate-700 shadow-sm hover:bg-slate-50 hover:text-slate-900 focus-visible:outline-slate-400',
        'success' => 'bg-emerald-600 text-white shadow-sm shadow-emerald-600/20 hover:bg-emerald-700 focus-visible:outline-emerald-500',
        'danger' => 'bg-rose-600 text-white shadow-sm shadow-rose-600/20 hover:bg-rose-700 focus-visible:outline-rose-500',
        'warning' => 'bg-amber-500 text-white shadow-sm shadow-amber-500/20 hover:bg-amber-600 focus-visible:outline-amber-500',
        'ghost' => 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 focus-visible:outline-slate-400',
    ];

    $sizes = [
        'sm' => 'gap-1.5 px-3 py-1.5 text-xs',
        'md' => 'gap-2 px-4 py-2 text-sm',
    ];

    $classes = implode(' ', [
        'inline-flex items-center justify-center rounded-xl font-semibold transition',
        'focus-visible:outline-2 focus-visible:outline-offset-2',
        'disabled:cursor-not-allowed disabled:opacity-50',
        $variants[$variant] ?? $variants['primary'],
        $sizes[$size] ?? $sizes['md'],
    ]);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)
            <x-icon :name="$icon" class="{{ $size === 'sm' ? 'h-3.5 w-3.5' : 'h-4 w-4' }}" />
        @endif

        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)
            <x-icon :name="$icon" class="{{ $size === 'sm' ? 'h-3.5 w-3.5' : 'h-4 w-4' }}" />
        @endif

        {{ $slot }}
    </button>
@endif
