<x-layouts.app title="สรุปเวลาทำงานรายวัน">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">สรุปเวลาทำงานรายวัน</h1>
            <p class="text-sm text-gray-600">
                สรุปเวลาเข้าออกงานจากข้อมูลเครื่องสแกนนิ้วมือ
            </p>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('attendance-summaries.dashboard') }}"
            class="rounded bg-gray-700 px-4 py-2 text-white hover:bg-gray-800">
                Dashboard
            </a>

            <a href="{{ route('attendance-summaries.print', request()->query()) }}"
                target="_blank"
                class="rounded bg-purple-600 px-4 py-2 text-white hover:bg-purple-700">
                    พิมพ์รายงาน
                </a>
            @can('attendance.import')
                <a href="{{ route('attendance-summaries.generate-form') }}"
                   class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                    สร้างสรุปเวลา
                </a>
            @endcan

            <a href="{{ route('attendance-logs.index') }}"
               class="rounded bg-gray-800 px-4 py-2 text-white hover:bg-gray-900">
                ข้อมูลเวลาสแกน
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded bg-green-100 px-4 py-3 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-4 rounded bg-white p-4 shadow">
        <form method="GET" action="{{ route('attendance-summaries.index') }}" class="grid gap-3 md:grid-cols-6">
            <input type="text"
                   name="search"
                   value="{{ $search }}"
                   placeholder="ค้นหารหัส / ชื่อบุคลากร"
                   class="rounded border-gray-300 md:col-span-2">

            <input type="date"
                   name="date_from"
                   value="{{ $dateFrom }}"
                   class="rounded border-gray-300">

            <input type="date"
                   name="date_to"
                   value="{{ $dateTo }}"
                   class="rounded border-gray-300">

            <select name="status" class="rounded border-gray-300">
                <option value="">ทุกสถานะ</option>
                <option value="normal" @selected($status === 'normal')>ปกติ</option>
                <option value="late" @selected($status === 'late')>มาสาย</option>
                <option value="early_leave" @selected($status === 'early_leave')>กลับก่อน</option>
                <option value="late_and_early_leave" @selected($status === 'late_and_early_leave')>สายและกลับก่อน</option>
                <option value="incomplete" @selected($status === 'incomplete')>ข้อมูลไม่ครบ</option>
            </select>

            <div class="flex gap-2">
                <button type="submit"
                        class="rounded bg-gray-800 px-4 py-2 text-white hover:bg-gray-900">
                    ค้นหา
                </button>

                <a href="{{ route('attendance-summaries.index') }}"
                   class="rounded bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">
                    ล้าง
                </a>
            </div>
        </form>
    </div>

    @php
        $statusLabels = [
            'normal' => 'ปกติ',
            'late' => 'มาสาย',
            'early_leave' => 'กลับก่อน',
            'late_and_early_leave' => 'สายและกลับก่อน',
            'incomplete' => 'ข้อมูลไม่ครบ',
            'absent' => 'ไม่พบสแกน',
            'off' => 'วันหยุด',
        ];

        $statusClasses = [
            'normal' => 'bg-green-100 text-green-800',
            'late' => 'bg-yellow-100 text-yellow-800',
            'early_leave' => 'bg-orange-100 text-orange-800',
            'late_and_early_leave' => 'bg-red-100 text-red-800',
            'incomplete' => 'bg-gray-100 text-gray-800',
            'absent' => 'bg-red-100 text-red-800',
            'off' => 'bg-gray-100 text-gray-800',
        ];
    @endphp

    <div class="overflow-hidden rounded bg-white shadow">
        <table class="w-full border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-4 py-2 text-left">วันที่</th>
                    <th class="border px-4 py-2 text-left">บุคลากร</th>
                    <th class="border px-4 py-2 text-left">หน่วยงาน</th>
                    <th class="border px-4 py-2 text-left">เวร</th>
                    <th class="border px-4 py-2 text-center">เวลาเข้า</th>
                    <th class="border px-4 py-2 text-center">เวลาออก</th>
                    <th class="border px-4 py-2 text-center">เวลาทำงาน</th>
                    <th class="border px-4 py-2 text-center">มาสาย</th>
                    <th class="border px-4 py-2 text-center">กลับก่อน</th>
                    <th class="border px-4 py-2 text-center">สถานะ</th>
                    <th class="border px-4 py-2 text-left">หมายเหตุ</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($summaries as $summary)
                    <tr>
                        <td class="border px-4 py-2 whitespace-nowrap">
                            {{ $summary->work_date?->format('Y-m-d') }}
                        </td>

                        <td class="border px-4 py-2">
                            {{ $summary->employee?->employee_code }}
                            <div class="text-xs text-gray-500">
                                {{ $summary->employee?->full_name ?? '-' }}
                            </div>
                        </td>

                        <td class="border px-4 py-2">
                            {{ $summary->employee?->department?->name ?? '-' }}
                        </td>

                        <td class="border px-4 py-2">
                            @if ($summary->dutySchedule)
                                {{ $summary->dutySchedule->shiftType?->name ?? '-' }}
                                <div class="text-xs text-gray-500">
                                    {{ $summary->dutySchedule->start_at?->format('H:i') }}
                                    -
                                    {{ $summary->dutySchedule->end_at?->format('H:i') }}
                                </div>
                            @else
                                <span class="text-gray-400">ไม่ได้ผูกตารางเวร</span>
                            @endif
                        </td>

                        <td class="border px-4 py-2 text-center whitespace-nowrap">
                            {{ $summary->first_in_at?->format('H:i:s') ?? '-' }}

                            @if ($summary->expected_in_time)
                                <div class="text-xs text-gray-500">
                                    กำหนด {{ \Carbon\Carbon::parse($summary->expected_in_time)->format('H:i') }}
                                </div>
                            @endif
                        </td>

                        <td class="border px-4 py-2 text-center whitespace-nowrap">
                            {{ $summary->last_out_at?->format('H:i:s') ?? '-' }}

                            @if ($summary->expected_out_time)
                                <div class="text-xs text-gray-500">
                                    กำหนด {{ \Carbon\Carbon::parse($summary->expected_out_time)->format('H:i') }}
                                </div>
                            @endif
                        </td>

                        <td class="border px-4 py-2 text-center">
                            {{ $summary->work_hours }}
                        </td>

                        <td class="border px-4 py-2 text-center">
                            @if ($summary->late_minutes > 0)
                                <span class="font-semibold text-yellow-700">
                                    {{ $summary->late_minutes }} นาที
                                </span>
                            @else
                                -
                            @endif
                        </td>

                        <td class="border px-4 py-2 text-center">
                            @if ($summary->early_leave_minutes > 0)
                                <span class="font-semibold text-orange-700">
                                    {{ $summary->early_leave_minutes }} นาที
                                </span>
                            @else
                                -
                            @endif
                        </td>

                        <td class="border px-4 py-2 text-center">
                            <span class="rounded px-2 py-1 text-xs {{ $statusClasses[$summary->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $statusLabels[$summary->status] ?? $summary->status }}
                            </span>
                        </td>

                        <td class="border px-4 py-2">
                            {{ $summary->remark ?? '-' }}

                            @if ($summary->generated_at)
                                <div class="text-xs text-gray-500">
                                    สร้างเมื่อ {{ $summary->generated_at->format('Y-m-d H:i') }}
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="border px-4 py-6 text-center text-gray-500">
                            ยังไม่มีข้อมูลสรุปเวลาทำงาน
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $summaries->links() }}
    </div>
</x-layouts.app>
