@props(['align' => 'left'])

@php
    $alignment = ['left' => 'text-left', 'center' => 'text-center', 'right' => 'text-right'][$align] ?? 'text-left';
@endphp

<th {{ $attributes->merge(['class' => 'px-4 py-3 font-semibold ' . $alignment]) }}>{{ $slot }}</th>
