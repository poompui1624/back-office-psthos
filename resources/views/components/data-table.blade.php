@props(['head' => null])

{{--
    Table shell. Pass column headers in the `head` slot as <x-data-table.th>
    and rows in the default slot; the horizontal scroll and card chrome are
    handled here so a wide table never pushes the page sideways.
--}}
<div {{ $attributes->merge(['class' => 'card overflow-hidden']) }}>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            @if ($head)
                <thead class="bg-slate-50/80 text-left text-xs text-slate-500">
                    <tr>{{ $head }}</tr>
                </thead>
            @endif

            <tbody class="divide-y divide-slate-100">
                {{ $slot }}
            </tbody>
        </table>
    </div>
</div>
