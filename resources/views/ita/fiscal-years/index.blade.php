<x-layouts.app title="จัดการปีงบประมาณ ITA">
    @include('ita._nav')

    <x-page-header title="จัดการปีงบประมาณ ITA"
                   subtitle="เพิ่ม แก้ไข เปิด/ปิด ปีงบประมาณ สำหรับระบบ ITA / MOIT">
        <x-btn :href="route('ita.fiscal-years.create')" icon="calendar">เพิ่มปีงบประมาณ</x-btn>
    </x-page-header>

    <x-data-table>
        <x-slot:head>
            <x-data-table.th>ปีงบประมาณ</x-data-table.th>
            <x-data-table.th>ชื่อ</x-data-table.th>
            <x-data-table.th align="center">หัวข้อหลัก</x-data-table.th>
            <x-data-table.th align="center">หัวข้อย่อย</x-data-table.th>
            <x-data-table.th align="center">ไฟล์</x-data-table.th>
            <x-data-table.th align="center">สถานะ</x-data-table.th>
            <x-data-table.th align="center">จัดการ</x-data-table.th>
        </x-slot:head>

        @forelse ($fiscalYears as $fiscalYear)
            <x-data-table.row>
                <x-data-table.td class="font-semibold text-slate-900 tabular-nums">{{ $fiscalYear->year }}</x-data-table.td>
                <x-data-table.td>{{ $fiscalYear->name }}</x-data-table.td>
                <x-data-table.td align="center">{{ $fiscalYear->topics_count }}</x-data-table.td>
                <x-data-table.td align="center">{{ $fiscalYear->sub_topics_count }}</x-data-table.td>
                <x-data-table.td align="center">{{ $fiscalYear->documents_count }}</x-data-table.td>

                <x-data-table.td align="center">
                    <x-badge :tone="$fiscalYear->is_active ? 'success' : 'slate'" dot>
                        {{ $fiscalYear->is_active ? 'ใช้งาน' : 'ปิด' }}
                    </x-badge>
                </x-data-table.td>

                <x-data-table.td align="center">
                    <div class="flex flex-wrap justify-center gap-2">
                        <x-btn :href="route('ita.moit-topics.index', ['fiscal_year_id' => $fiscalYear->id])"
                               variant="secondary" size="sm">หัวข้อ</x-btn>

                        <x-btn :href="route('ita.fiscal-years.edit', $fiscalYear)" variant="secondary" size="sm">แก้ไข</x-btn>

                        <form method="POST"
                              action="{{ route('ita.fiscal-years.destroy', $fiscalYear) }}"
                              onsubmit="return confirm('ยืนยันการลบปีงบประมาณนี้?')">
                            @csrf
                            @method('DELETE')

                            <x-btn type="submit" variant="danger" size="sm">ลบ</x-btn>
                        </form>
                    </div>
                </x-data-table.td>
            </x-data-table.row>
        @empty
            <x-data-table.empty :colspan="7" icon="calendar" title="ยังไม่มีปีงบประมาณ ITA"
                                description="เพิ่มปีงบประมาณก่อนจึงจะสร้างหัวข้อ MOIT ได้">
                <x-btn :href="route('ita.fiscal-years.create')">เพิ่มปีงบประมาณ</x-btn>
            </x-data-table.empty>
        @endforelse
    </x-data-table>

    <div class="mt-4">{{ $fiscalYears->links() }}</div>
</x-layouts.app>
