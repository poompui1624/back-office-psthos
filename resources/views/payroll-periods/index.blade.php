<x-layouts.app title="รอบเงินเดือน">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">รอบเงินเดือน</h1>
            <p class="text-sm text-gray-600">
                สร้างรอบเงินเดือนและ Generate สลิปเงินเดือน
            </p>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('salary-profiles.index') }}"
               class="rounded bg-gray-800 px-4 py-2 text-white hover:bg-gray-900">
                ตั้งค่าเงินเดือน
            </a>

            @can('payroll.create')
                <a href="{{ route('payroll-periods.create') }}"
                   class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                    สร้างรอบเงินเดือน
                </a>
            @endcan
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded bg-green-100 px-4 py-3 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 rounded bg-red-100 px-4 py-3 text-red-800">
            {{ session('error') }}
        </div>
    @endif

    <div class="overflow-hidden rounded bg-white shadow">
        <table class="w-full border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-4 py-2 text-left">รอบเงินเดือน</th>
                    <th class="border px-4 py-2 text-center">ปี/เดือน</th>
                    <th class="border px-4 py-2 text-center">ช่วงวันที่</th>
                    <th class="border px-4 py-2 text-center">จำนวนสลิป</th>
                    <th class="border px-4 py-2 text-center">สถานะ</th>
                    <th class="border px-4 py-2 text-left">หมายเหตุ</th>
                    <th class="border px-4 py-2 text-center">จัดการ</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($periods as $period)
                    <tr>
                        <td class="border px-4 py-2">
                            {{ $period->name }}

                            @if ($period->generated_at)
                                <div class="text-xs text-gray-500">
                                    Generate: {{ $period->generated_at->format('Y-m-d H:i') }}
                                </div>
                            @endif
                        </td>

                        <td class="border px-4 py-2 text-center">
                            {{ str_pad($period->month, 2, '0', STR_PAD_LEFT) }}/{{ $period->year }}
                        </td>

                        <td class="border px-4 py-2 text-center whitespace-nowrap">
                            {{ $period->start_date?->format('Y-m-d') }}
                            ถึง
                            {{ $period->end_date?->format('Y-m-d') }}
                        </td>

                        <td class="border px-4 py-2 text-center">
                            {{ $period->payslips_count }}
                        </td>

                        <td class="border px-4 py-2 text-center">
                            @if ($period->status === 'draft')
                                <span class="rounded bg-gray-100 px-2 py-1 text-xs text-gray-800">
                                    ร่าง
                                </span>
                            @elseif ($period->status === 'generated')
                                <span class="rounded bg-blue-100 px-2 py-1 text-xs text-blue-800">
                                    สร้างสลิปแล้ว
                                </span>
                            @elseif ($period->status === 'closed')
                                <span class="rounded bg-green-100 px-2 py-1 text-xs text-green-800">
                                    ปิดรอบแล้ว
                                </span>
                            @else
                                <span class="rounded bg-gray-100 px-2 py-1 text-xs text-gray-800">
                                    {{ $period->status }}
                                </span>
                            @endif
                        </td>

                        <td class="border px-4 py-2">
                            {{ $period->remark ?? '-' }}
                        </td>

                        <td class="border px-4 py-2 text-center">
                            <a href="{{ route('payroll-periods.show', $period) }}"
                               class="rounded bg-gray-800 px-3 py-1 text-sm text-white hover:bg-gray-900">
                                รายละเอียด
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="border px-4 py-6 text-center text-gray-500">
                            ยังไม่มีรอบเงินเดือน
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $periods->links() }}
    </div>
</x-layouts.app>
