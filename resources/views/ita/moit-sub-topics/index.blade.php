<x-layouts.app title="จัดการหัวข้อย่อย MOIT">
    @include('ita._nav')

    <x-page-header title="จัดการหัวข้อย่อย MOIT"
                   subtitle="เพิ่ม แก้ไข ปิดใช้งาน หรือจัดเรียงหัวข้อย่อยของแต่ละ MOIT">
        <x-btn :href="route('ita.moit-sub-topics.create')" icon="clipboard">เพิ่มหัวข้อย่อย</x-btn>
    </x-page-header>

    <x-filter-bar :action="route('ita.moit-sub-topics.index')">
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

        <x-form.field label="หัวข้อหลัก">
            <x-form.select name="main_topic_id" class="w-60">
                <option value="">-- ทุก MOIT --</option>

                @foreach ($mainTopics as $topic)
                    <option value="{{ $topic->id }}" @selected(request('main_topic_id') == $topic->id)>
                        {{ $topic->code }} {{ Str::limit($topic->title, 40) }}
                    </option>
                @endforeach
            </x-form.select>
        </x-form.field>

        <x-form.field label="ค้นหา" class="min-w-56 flex-1">
            <x-form.input name="keyword" :value="request('keyword')" placeholder="รหัส / ชื่อหัวข้อย่อย" />
        </x-form.field>
    </x-filter-bar>

    <x-data-table>
        <x-slot:head>
            <x-data-table.th>ปี</x-data-table.th>
            <x-data-table.th>หัวข้อหลัก</x-data-table.th>
            <x-data-table.th>รหัส</x-data-table.th>
            <x-data-table.th>หัวข้อย่อย</x-data-table.th>
            <x-data-table.th align="center">ไฟล์</x-data-table.th>
            <x-data-table.th align="center">สถานะ</x-data-table.th>
            <x-data-table.th align="center">จัดการ</x-data-table.th>
        </x-slot:head>

        @forelse ($subTopics as $subTopic)
            <x-data-table.row>
                <x-data-table.td class="tabular-nums">{{ $subTopic->fiscalYear?->year }}</x-data-table.td>

                <x-data-table.td>
                    <div class="font-semibold text-slate-900">{{ $subTopic->mainTopic?->code }}</div>
                    <div class="mt-0.5 max-w-xs truncate text-xs text-slate-500">{{ $subTopic->mainTopic?->title }}</div>
                </x-data-table.td>

                <x-data-table.td class="font-semibold text-slate-900">
                    {{ $subTopic->code }}

                    @if ($subTopic->is_heading)
                        <div class="mt-1"><x-badge tone="brand">หัวข้อกลุ่ม</x-badge></div>
                    @endif
                </x-data-table.td>

                <x-data-table.td>
                    <div class="max-w-xl">{{ $subTopic->title }}</div>
                    <div class="mt-0.5 text-xs text-slate-400">ลำดับ: {{ $subTopic->sort_order }}</div>
                </x-data-table.td>

                <x-data-table.td align="center">{{ $subTopic->documents_count }}</x-data-table.td>

                <x-data-table.td align="center">
                    <x-badge :tone="$subTopic->is_active ? 'success' : 'slate'" dot>
                        {{ $subTopic->is_active ? 'ใช้งาน' : 'ปิด' }}
                    </x-badge>
                </x-data-table.td>

                <x-data-table.td align="center">
                    <div class="flex flex-wrap justify-center gap-2">
                        <x-btn :href="route('ita.moit-sub-topics.edit', $subTopic)" variant="secondary" size="sm">แก้ไข</x-btn>

                        <form method="POST"
                              action="{{ route('ita.moit-sub-topics.destroy', $subTopic) }}"
                              onsubmit="return confirm('ยืนยันการลบหัวข้อย่อยนี้?')">
                            @csrf
                            @method('DELETE')

                            <x-btn type="submit" variant="danger" size="sm">ลบ</x-btn>
                        </form>
                    </div>
                </x-data-table.td>
            </x-data-table.row>
        @empty
            <x-data-table.empty :colspan="7" icon="clipboard" title="ยังไม่มีหัวข้อย่อย MOIT"
                                description="เพิ่มหัวข้อย่อยเพื่อให้แนบไฟล์ตามหัวข้อได้">
                <x-btn :href="route('ita.moit-sub-topics.create')">เพิ่มหัวข้อย่อย</x-btn>
            </x-data-table.empty>
        @endforelse
    </x-data-table>

    <div class="mt-4">{{ $subTopics->links() }}</div>
</x-layouts.app>
