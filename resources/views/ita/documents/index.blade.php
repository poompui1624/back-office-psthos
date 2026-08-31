<x-layouts.app title="ไฟล์ ITA">
    <x-page-header title="ระบบรับไฟล์ ITA"
                   subtitle="แสดงไฟล์ที่อัปโหลดแล้ว เปิดดู แก้ไข ลบ และคัดลอกลิงก์ไฟล์">
        <x-btn :href="route('ita.public.index')" target="_blank" variant="secondary" icon="external-link">
            หน้าแสดงผล
        </x-btn>

        @can('ita.topic.manage')
            <x-btn :href="route('ita.fiscal-years.index')" variant="secondary">ปีงบประมาณ</x-btn>
            <x-btn :href="route('ita.moit-topics.index')" variant="secondary">หัวข้อหลัก</x-btn>
            <x-btn :href="route('ita.moit-sub-topics.index')" variant="secondary">หัวข้อย่อย</x-btn>
        @endcan

        @can('ita.create')
            <x-btn :href="route('ita.documents.create')" icon="upload">อัปโหลดไฟล์</x-btn>
        @endcan
    </x-page-header>

    <x-filter-bar :action="route('ita.documents.index')">
        <x-form.field label="ปีงบประมาณ">
            <x-form.select name="fiscal_year_id" class="w-48">
                <option value="">-- ทุกปีงบประมาณ --</option>

                @foreach ($fiscalYears as $year)
                    <option value="{{ $year->id }}" @selected((int) $selectedYearId === $year->id)>
                        {{ $year->year }}
                    </option>
                @endforeach
            </x-form.select>
        </x-form.field>

        <x-form.field label="ค้นหา" class="min-w-64 flex-1">
            <x-form.input name="keyword" :value="request('keyword')" placeholder="ชื่อไฟล์ / ชื่อเอกสาร" />
        </x-form.field>
    </x-filter-bar>

    <x-data-table>
        <x-slot:head>
            <x-data-table.th>ปี</x-data-table.th>
            <x-data-table.th>MOIT</x-data-table.th>
            <x-data-table.th>หัวข้อย่อย</x-data-table.th>
            <x-data-table.th>ไฟล์</x-data-table.th>
            <x-data-table.th>ขนาด</x-data-table.th>
            <x-data-table.th align="center">สถานะ</x-data-table.th>
            <x-data-table.th align="center">จัดการ</x-data-table.th>
        </x-slot:head>

        @forelse ($documents as $document)
            <x-data-table.row>
                <x-data-table.td class="tabular-nums">{{ $document->fiscalYear?->year }}</x-data-table.td>

                <x-data-table.td>
                    <div class="font-medium text-slate-900">{{ $document->mainTopic?->code }}</div>
                    <div class="mt-0.5 max-w-xs truncate text-xs text-slate-500">{{ $document->mainTopic?->title }}</div>
                </x-data-table.td>

                <x-data-table.td>
                    @if ($document->subTopic)
                        <div class="font-medium text-slate-900">{{ $document->subTopic->code }}</div>
                        <div class="mt-0.5 max-w-xs truncate text-xs text-slate-500">{{ $document->subTopic->title }}</div>
                    @else
                        <span class="text-slate-400">-</span>
                    @endif
                </x-data-table.td>

                <x-data-table.td>
                    <div class="font-medium text-slate-900">{{ $document->title }}</div>
                    <div class="mt-0.5 text-xs text-slate-500">{{ $document->file_original_name }}</div>
                </x-data-table.td>

                <x-data-table.td class="whitespace-nowrap tabular-nums">{{ $document->file_size_human }}</x-data-table.td>

                <x-data-table.td align="center">
                    <x-badge :tone="$document->is_public ? 'success' : 'slate'" dot>
                        {{ $document->is_public ? 'เผยแพร่' : 'ซ่อน' }}
                    </x-badge>
                </x-data-table.td>

                <x-data-table.td align="center">
                    <div class="flex flex-wrap justify-center gap-2">
                        <x-btn :href="$document->file_url" target="_blank" variant="secondary" size="sm">เปิดดู</x-btn>

                        <x-btn type="button" variant="secondary" size="sm"
                               onclick="copyToClipboard('{{ $document->file_url }}')">
                            คัดลอกลิงก์
                        </x-btn>

                        @can('ita.edit')
                            <x-btn :href="route('ita.documents.edit', $document)" variant="secondary" size="sm">แก้ไข</x-btn>
                        @endcan

                        @can('ita.delete')
                            <form method="POST"
                                  action="{{ route('ita.documents.destroy', $document) }}"
                                  onsubmit="return confirm('ยืนยันการลบไฟล์นี้?')">
                                @csrf
                                @method('DELETE')

                                <x-btn type="submit" variant="danger" size="sm">ลบ</x-btn>
                            </form>
                        @endcan
                    </div>
                </x-data-table.td>
            </x-data-table.row>
        @empty
            <x-data-table.empty :colspan="7" icon="document" title="ยังไม่มีไฟล์ ITA"
                                description="อัปโหลดไฟล์เพื่อเผยแพร่ตามหัวข้อ MOIT">
                @can('ita.create')
                    <x-btn :href="route('ita.documents.create')">อัปโหลดไฟล์</x-btn>
                @endcan
            </x-data-table.empty>
        @endforelse
    </x-data-table>

    <div class="mt-4">{{ $documents->links() }}</div>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function () {
                alert('คัดลอกลิงก์ไฟล์แล้ว');
            });
        }
    </script>
</x-layouts.app>
