@props(['type' => 'info', 'title' => null])

@php
    $types = [
        'success' => ['border-emerald-200 bg-emerald-50 text-emerald-800', 'text-emerald-600', 'approvals'],
        'error' => ['border-rose-200 bg-rose-50 text-rose-800', 'text-rose-600', 'close'],
        'warning' => ['border-amber-200 bg-amber-50 text-amber-800', 'text-amber-600', 'bell'],
        'info' => ['border-sky-200 bg-sky-50 text-sky-800', 'text-sky-600', 'inbox'],
    ];

    [$shell, $iconTone, $icon] = $types[$type] ?? $types['info'];
@endphp

<div {{ $attributes->merge(['class' => 'flex items-start gap-3 rounded-2xl border px-5 py-4 text-sm ' . $shell]) }}>
    <x-icon :name="$icon" class="mt-0.5 h-5 w-5 shrink-0 {{ $iconTone }}" />

    <div class="min-w-0 flex-1">
        @if ($title)
            <div class="font-semibold">{{ $title }}</div>
        @endif

        <div @class(['mt-1' => $title])>{{ $slot }}</div>
    </div>
</div>
