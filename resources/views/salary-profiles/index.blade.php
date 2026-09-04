<x-layouts.app title="ตั้งค่าเงินเดือน">
    <x-page-header title="ตั้งค่าเงินเดือน"
                   subtitle="ตั้งค่าเงินเดือน รายได้ รายการหัก และอัตราหักจากเวลาทำงาน">
        <x-btn :href="route('payroll-periods.index')" variant="secondary" icon="money">รอบเงินเดือน</x-btn>

        @can('payroll.create')
            <x-btn :href="route('salary-profiles.create')" icon="cog">เพิ่มข้อมูลเงินเดือน</x-btn>
        @endcan
    </x-page-header>

    <x-filter-bar :action="route('salary-profiles.index')">
        <x-form.field label="ค้นหา" class="flex-1">
            <x-form.input name="search" :value="$search" placeholder="รหัส / ชื่อบุคลากร" />
        </x-form.field>
    </x-filter-bar>

    <x-data-table>
        <x-slot:head>
            <x-data-table.th>บุคลากร</x-data-table.th>
            <x-data-table.th>หน่วยงาน</x-data-table.th>
            <x-data-table.th align="right">เงินเดือน</x-data-table.th>
            <x-data-table.th align="right">รายได้รวมตั้งต้น</x-data-table.th>
            <x-data-table.th align="right">หักประจำ</x-data-table.th>
            <x-data-table.th align="center">สถานะ</x-data-table.th>
            <x-data-table.th align="center">จัดการ</x-data-table.th>
        </x-slot:head>

        @forelse ($profiles as $profile)
            @php
                $gross = $profile->base_salary + $profile->position_allowance
                    + $profile->professional_allowance + $profile->other_allowance;

                $deduct = $profile->social_security + $profile->tax
                    + $profile->provident_fund + $profile->other_deduction;
            @endphp

            <x-data-table.row>
                <x-data-table.td>
                    <div class="font-medium text-slate-900">{{ $profile->employee?->employee_code }}</div>
                    <div class="mt-0.5 text-xs text-slate-500">{{ $profile->employee?->full_name ?? '-' }}</div>
                </x-data-table.td>

                <x-data-table.td>{{ $profile->employee?->department?->name ?? '-' }}</x-data-table.td>

                <x-data-table.td align="right" class="tabular-nums">{{ number_format($profile->base_salary, 2) }}</x-data-table.td>
                <x-data-table.td align="right" class="tabular-nums font-medium text-emerald-700">{{ number_format($gross, 2) }}</x-data-table.td>
                <x-data-table.td align="right" class="tabular-nums text-rose-700">{{ number_format($deduct, 2) }}</x-data-table.td>

                <x-data-table.td align="center">
                    <x-badge :tone="$profile->is_active ? 'success' : 'slate'" dot>
                        {{ $profile->is_active ? 'ใช้งาน' : 'ปิดใช้งาน' }}
                    </x-badge>
                </x-data-table.td>

                <x-data-table.td align="center">
                    <div class="flex justify-center gap-2">
                        @can('payroll.update')
                            <x-btn :href="route('salary-profiles.edit', $profile)" variant="secondary" size="sm">แก้ไข</x-btn>
                        @endcan

                        @can('payroll.delete')
                            <form method="POST"
                                  action="{{ route('salary-profiles.destroy', $profile) }}"
                                  onsubmit="return confirm('ยืนยันการลบข้อมูลเงินเดือนนี้?')">
                                @csrf
                                @method('DELETE')

                                <x-btn type="submit" variant="danger" size="sm">ลบ</x-btn>
                            </form>
                        @endcan
                    </div>
                </x-data-table.td>
            </x-data-table.row>
        @empty
            <x-data-table.empty :colspan="7" icon="cog" title="ยังไม่มีข้อมูลเงินเดือน"
                                description="ตั้งค่าเงินเดือนของบุคลากรก่อนจึงจะ Generate สลิปได้">
                @can('payroll.create')
                    <x-btn :href="route('salary-profiles.create')">เพิ่มข้อมูลเงินเดือน</x-btn>
                @endcan
            </x-data-table.empty>
        @endforelse
    </x-data-table>

    <div class="mt-4">{{ $profiles->links() }}</div>
</x-layouts.app>
