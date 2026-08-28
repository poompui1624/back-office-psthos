@props(['cancel' => null, 'submitLabel' => 'บันทึก', 'cancelLabel' => 'ย้อนกลับ'])

<div {{ $attributes->merge(['class' => 'flex flex-wrap items-center gap-2 border-t border-slate-100 pt-5']) }}>
    @if ($slot->isEmpty())
        <x-btn type="submit">{{ $submitLabel }}</x-btn>

        @if ($cancel)
            <x-btn :href="$cancel" variant="secondary">{{ $cancelLabel }}</x-btn>
        @endif
    @else
        {{ $slot }}
    @endif
</div>
