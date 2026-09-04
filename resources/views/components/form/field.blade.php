@props([
    'label' => null,
    'name' => null,
    'required' => false,
    'hint' => null,
    'error' => null,
])

@php
    // Falls back to the validation bag so callers rarely pass $error by hand.
    $bag = $errors ?? null;

    $message = $error ?? ($name && $bag ? ($bag->first($name) ?: null) : null);
@endphp

<div {{ $attributes->merge(['class' => 'min-w-0']) }}>
    @if ($label)
        <label @if ($name) for="{{ $name }}" @endif class="mb-1.5 block text-sm font-medium text-slate-700">
            {{ $label }}

            @if ($required)
                <span class="text-rose-600">*</span>
            @endif
        </label>
    @endif

    {{ $slot }}

    @if ($message)
        <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
    @elseif ($hint)
        <p class="mt-1.5 text-xs text-slate-500">{{ $hint }}</p>
    @endif
</div>
