@props(['name' => null, 'label' => null, 'checked' => false, 'value' => 1])

<label {{ $attributes->merge(['class' => 'inline-flex cursor-pointer items-center gap-2.5 text-sm text-slate-700']) }}>
    <input type="checkbox"
           @if ($name) name="{{ $name }}" id="{{ $name }}" @endif
           value="{{ $value }}"
           @checked($checked)
           class="h-4 w-4 rounded border-slate-300 text-brand-500 shadow-sm focus:ring-2 focus:ring-brand-100">

    <span>{{ $label ?? $slot }}</span>
</label>
