<x-layouts.app title="แบนเนอร์หน้าเว็บ">
    @include('site.admin._nav')

    <x-page-header title="แบนเนอร์หน้าเว็บ" subtitle="ภาพสไลด์บนสุดของหน้าเว็บโรงพยาบาล">
        @can('site.manage')
            <x-btn :href="route('site.banners.create')" icon="upload">เพิ่มแบนเนอร์</x-btn>
        @endcan
    </x-page-header>

    <x-data-table>
        <x-slot:head>
            <x-data-table.th>ภาพ</x-data-table.th>
            <x-data-table.th>ข้อความ</x-data-table.th>
            <x-data-table.th>ช่วงเวลาแสดง</x-data-table.th>
            <x-data-table.th align="center">ลำดับ</x-data-table.th>
            <x-data-table.th align="center">สถานะ</x-data-table.th>
            <x-data-table.th align="center">จัดการ</x-data-table.th>
        </x-slot:head>

        @forelse ($banners as $banner)
            @php
                $isLive = $banner->is_active
                    && (! $banner->starts_at || $banner->starts_at->isPast())
                    && (! $banner->ends_at || $banner->ends_at->isFuture());
            @endphp

            <x-data-table.row>
                <x-data-table.td>
                    <img src="{{ $banner->image_url }}" alt="" class="h-14 w-28 rounded-lg object-cover ring-1 ring-slate-200">
                </x-data-table.td>

                <x-data-table.td>
                    <div class="font-medium text-slate-900">{{ $banner->title ?: '—' }}</div>

                    @if ($banner->subtitle)
                        <div class="mt-0.5 text-xs text-slate-500">{{ $banner->subtitle }}</div>
                    @endif

                    @if ($banner->link_url)
                        <div class="mt-1 max-w-xs truncate text-xs text-brand-600">{{ $banner->link_url }}</div>
                    @endif
                </x-data-table.td>

                <x-data-table.td class="whitespace-nowrap text-xs">
                    @if ($banner->starts_at || $banner->ends_at)
                        <div>{{ $banner->starts_at?->format('Y-m-d') ?? 'ทันที' }}</div>
                        <div class="text-slate-500">ถึง {{ $banner->ends_at?->format('Y-m-d') ?? 'ไม่กำหนด' }}</div>
                    @else
                        <span class="text-slate-400">แสดงตลอด</span>
                    @endif
                </x-data-table.td>

                <x-data-table.td align="center" class="tabular-nums">{{ $banner->sort_order }}</x-data-table.td>

                <x-data-table.td align="center">
                    @if (! $banner->is_active)
                        <x-badge tone="slate" dot>ปิด</x-badge>
                    @elseif ($isLive)
                        <x-badge tone="success" dot>แสดงอยู่</x-badge>
                    @else
                        <x-badge tone="warning" dot>นอกช่วงเวลา</x-badge>
                    @endif
                </x-data-table.td>

                <x-data-table.td align="center">
                    @can('site.manage')
                        <div class="flex justify-center gap-2">
                            <x-btn :href="route('site.banners.edit', $banner)" variant="secondary" size="sm">แก้ไข</x-btn>

                            <form method="POST" action="{{ route('site.banners.destroy', $banner) }}"
                                  onsubmit="return confirm('ยืนยันการลบแบนเนอร์นี้?')">
                                @csrf
                                @method('DELETE')

                                <x-btn type="submit" variant="danger" size="sm">ลบ</x-btn>
                            </form>
                        </div>
                    @endcan
                </x-data-table.td>
            </x-data-table.row>
        @empty
            <x-data-table.empty :colspan="6" icon="box" title="ยังไม่มีแบนเนอร์"
                                description="เพิ่มภาพสไลด์เพื่อให้หน้าเว็บมีภาพต้อนรับ">
                @can('site.manage')
                    <x-btn :href="route('site.banners.create')">เพิ่มแบนเนอร์</x-btn>
                @endcan
            </x-data-table.empty>
        @endforelse
    </x-data-table>

    <div class="mt-4">{{ $banners->links() }}</div>
</x-layouts.app>
