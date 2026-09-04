@props(['name' => null, 'type' => 'text', 'value' => null, 'invalid' => null])

@php
    // Guarded so the component still renders outside a request, where the
    // ShareErrorsFromSession middleware has not supplied $errors.
    $hasError = $invalid ?? ($name && isset($errors) ? $errors->has($name) : false);
@endphp

<input type="{{ $type }}"
       @if ($name) name="{{ $name }}" id="{{ $name }}" @endif
       value="{{ old($name, $value) }}"
       {{ $attributes->merge(['class' => 'w-full rounded-xl border bg-white px-3 py-2 text-sm text-slate-900 shadow-sm transition placeholder:text-slate-400 focus:outline-none focus:ring-2 ' . ($hasError
           ? 'border-rose-300 focus:border-rose-400 focus:ring-rose-100'
           : 'border-slate-200 focus:border-brand-400 focus:ring-brand-100')]) }}>
