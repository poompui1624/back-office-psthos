<x-layouts.app title="เอกสารเผยแพร่">
    @include('site.admin._nav')

    <x-page-header title="เอกสารเผยแพร่" subtitle="จัดซื้อจัดจ้าง รับสมัครงาน รายงานประจำปี และเอกสารอื่น ๆ">
        @can('site.manage')
            <x-btn :href="route('site.documents.create')" icon="upload">อัปโหลดเอกสาร</x-btn>
        @endcan
    </x-page-header>

    <x-filter-bar :action="route('site.documents.index')">
        <x-form.field label="ค้นหา" class="min-w-64 flex-1">
            <x-form.input name="search" :value="$search" placeholder="ชื่อเอกสาร" />
        </x-form.field>

        <x-form.field label="หมวด">
            <x-form.select name="category" class="w-48">
                <option value="">ทุกหมวด</option>

                @foreach (\App\Models\SiteDocument::categories() as $value => $label)
                    <option value="{{ $value }}" @selected($category === $value)>{{ $label }}</option>
                @endforeach
            </x-form.select>
        </x-form.field>
    </x-filter-bar>

    <x-data-table>
        <x-slot:head>
            <x-data-table.th>เอกสาร</x-data-table.th>
            <x-data-table.th>หมวด</x-data-table.th>
            <x-data-table.th>เผยแพร่</x-data-table.th>
            <x-data-table.th align="center">ขนาด</x-data-table.th>
            <x-data-table.th align="center">ดาวน์โหลด</x-data-table.th>
            <x-data-table.th align="center">สถานะ</x-data-table.th>
            <x-data-table.th align="center">จัดการ</x-data-table.th>
        </x-slot:head>

        @forelse ($documents as $document)
            @php
                $isLive = $document->is_published && $document->published_at && $document->published_at->isPast();
            @endphp

            <x-data-table.row>
                <x-data-table.td>
                    <div class="flex items-center gap-2.5">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-brand-50 text-brand-600">
                            <x-icon :name="$document->icon" class="h-4 w-4" />
                        </span>

                        <div class="min-w-0">
                            <div class="font-medium text-slate-900">{{ $document->title }}</div>
                            <div class="mt-0.5 max-w-xs truncate text-xs text-slate-500">{{ $document->file_original_name }}</div>
                        </div>
                    </div>
                </x-data-table.td>

                <x-data-table.td>{{ $document->category_label }}</x-data-table.td>

                <x-data-table.td class="whitespace-nowrap text-xs">
                    {{ $document->published_at?->format('Y-m-d H:i') ?? '—' }}
                </x-data-table.td>

                <x-data-table.td align="center" class="whitespace-nowrap tabular-nums">{{ $document->file_size_human }}</x-data-table.td>

                <x-data-table.td align="center" class="tabular-nums">{{ number_format($document->download_count) }}</x-data-table.td>

                <x-data-table.td align="center">
                    @if (! $document->is_published)
                        <x-badge tone="slate" dot>ฉบับร่าง</x-badge>
                    @elseif ($isLive)
                        <x-badge tone="success" dot>เผยแพร่แล้ว</x-badge>
                    @else
                        <x-badge tone="info" dot>ตั้งเวลาไว้</x-badge>
                    @endif
                </x-data-table.td>

                <x-data-table.td align="center">
                    <div class="flex flex-wrap justify-center gap-2">
                        @if ($isLive)
                            <x-btn :href="route('site.document', $document)" target="_blank" variant="secondary" size="sm">ดู</x-btn>
                        @endif

                        @can('site.manage')
                            <x-btn :href="route('site.documents.edit', $document)" variant="secondary" size="sm">แก้ไข</x-btn>

                            <form method="POST" action="{{ route('site.documents.destroy', $document) }}"
                                  onsubmit="return confirm('ยืนยันการลบเอกสารนี้?')">
                                @csrf
                                @method('DELETE')

                                <x-btn type="submit" variant="danger" size="sm">ลบ</x-btn>
                            </form>
                        @endcan
                    </div>
                </x-data-table.td>
            </x-data-table.row>
        @empty
            <x-data-table.empty :colspan="7" icon="document" title="ยังไม่มีเอกสารเผยแพร่"
                                description="อัปโหลดประกาศจัดซื้อจัดจ้าง ประกาศรับสมัครงาน หรือรายงานประจำปี">
                @can('site.manage')
                    <x-btn :href="route('site.documents.create')">อัปโหลดเอกสาร</x-btn>
                @endcan
            </x-data-table.empty>
        @endforelse
    </x-data-table>

    <div class="mt-4">{{ $documents->links() }}</div>
</x-layouts.app>
