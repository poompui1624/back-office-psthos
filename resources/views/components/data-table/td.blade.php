@props(['align' => 'left'])

@php
    $alignment = ['left' => 'text-left', 'center' => 'text-center', 'right' => 'text-right'][$align] ?? 'text-left';
@endphp

<td {{ $attributes->merge(['class' => 'px-4 py-3 text-slate-700 ' . $alignment]) }}>{{ $slot }}</td>
