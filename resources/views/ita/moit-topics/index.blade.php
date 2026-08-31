<x-layouts.app title="จัดการหัวข้อหลัก MOIT">
    <x-page-header title="จัดการหัวข้อหลัก MOIT"
                   subtitle="เพิ่ม แก้ไข ปิดใช้งาน หรือจัดเรียงหัวข้อหลักตามปีงบประมาณ">
        <x-btn :href="route('ita.documents.index')" variant="secondary" icon="document">กลับไฟล์ ITA</x-btn>
        <x-btn :href="route('ita.moit-sub-topics.index')" variant="secondary">หัวข้อย่อย</x-btn>
        <x-btn :href="route('ita.moit-topics.create')" icon="clipboard">เพิ่มหัวข้อหลัก</x-btn>
    </x-page-header>

    <x-filter-bar :action="route('ita.moit-topics.index')">
        <x-form.field label="ปีงบประมาณ">
            <x-form.select name="fiscal_year_id" class="w-40">
                <option value="">-- ทุกปี --</option>

                @foreach ($fiscalYears as $year)
                    <option value="{{ $year->id }}" @selected(request('fiscal_year_id') == $year->id)>
                        {{ $year->year }}
                    </option>
                @endforeach
            </x-form.select>
        </x-form.field>

        <x-form.field label="ค้นหา" class="min-w-64 flex-1">
            <x-form.input name="keyword" :value="request('keyword')" placeholder="MOIT / ชื่อหัวข้อ / ตัวชี้วัด" />
        </x-form.field>
    </x-filter-bar>

    <x-data-table>
        <x-slot:head>
            <x-data-table.th>ปี</x-data-table.th>
            <x-data-table.th>ตัวชี้วัด</x-data-table.th>
            <x-data-table.th>MOIT</x-data-table.th>
            <x-data-table.th>หัวข้อหลัก</x-data-table.th>
            <x-data-table.th align="center">หัวข้อย่อย</x-data-table.th>
            <x-data-table.th align="center">ไฟล์</x-data-table.th>
            <x-data-table.th align="center">สถานะ</x-data-table.th>
            <x-data-table.th align="center">จัดการ</x-data-table.th>
        </x-slot:head>

        @forelse ($topics as $topic)
            <x-data-table.row>
                <x-data-table.td class="tabular-nums">{{ $topic->fiscalYear?->year }}</x-data-table.td>

                <x-data-table.td>
                    <div>ตัวชี้วัดที่ {{ $topic->indicator_no }}</div>
                    <div class="mt-0.5 text-xs text-slate-500">{{ $topic->indicator_title }}</div>
                </x-data-table.td>

                <x-data-table.td class="font-semibold text-slate-900">{{ $topic->code }}</x-data-table.td>

                <x-data-table.td>
                    <div class="max-w-xl">{{ $topic->title }}</div>
                    <div class="mt-0.5 text-xs text-slate-400">ลำดับ: {{ $topic->sort_order }}</div>
                </x-data-table.td>

                <x-data-table.td align="center">{{ $topic->sub_topics_count }}</x-data-table.td>
                <x-data-table.td align="center">{{ $topic->documents_count }}</x-data-table.td>

                <x-data-table.td align="center">
                    <x-badge :tone="$topic->is_active ? 'success' : 'slate'" dot>
                        {{ $topic->is_active ? 'ใช้งาน' : 'ปิด' }}
                    </x-badge>
                </x-data-table.td>

                <x-data-table.td align="center">
                    <div class="flex flex-wrap justify-center gap-2">
                        <x-btn :href="route('ita.moit-sub-topics.index', ['main_topic_id' => $topic->id])"
                               variant="secondary" size="sm">หัวข้อย่อย</x-btn>

                        <x-btn :href="route('ita.moit-topics.edit', $topic)" variant="secondary" size="sm">แก้ไข</x-btn>

                        <form method="POST"
                              action="{{ route('ita.moit-topics.destroy', $topic) }}"
                              onsubmit="return confirm('ยืนยันการลบหัวข้อหลักนี้?')">
                            @csrf
                            @method('DELETE')

                            <x-btn type="submit" variant="danger" size="sm">ลบ</x-btn>
                        </form>
                    </div>
                </x-data-table.td>
            </x-data-table.row>
        @empty
            <x-data-table.empty :colspan="8" icon="clipboard" title="ยังไม่มีหัวข้อหลัก MOIT"
                                description="เพิ่มหัวข้อหลักเพื่อให้แนบไฟล์ ITA ได้">
                <x-btn :href="route('ita.moit-topics.create')">เพิ่มหัวข้อหลัก</x-btn>
            </x-data-table.empty>
        @endforelse
    </x-data-table>

    <div class="mt-4">{{ $topics->links() }}</div>
</x-layouts.app>
