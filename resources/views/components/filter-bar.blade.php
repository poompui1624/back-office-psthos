@props(['action', 'method' => 'GET', 'reset' => true])

{{--
    The search card every list page carries. Fields go in the default slot;
    the submit and clear buttons are supplied here so they stay identical.
--}}
<form method="{{ $method }}" action="{{ $action }}"
      {{ $attributes->merge(['class' => 'card card-pad mb-4 flex flex-wrap items-end gap-3']) }}>
    {{ $slot }}

    <div class="flex gap-2">
        <x-btn type="submit" icon="search">ค้นหา</x-btn>

        @if ($reset)
            <x-btn :href="$action" variant="secondary">ล้าง</x-btn>
        @endif
    </div>
</form>
