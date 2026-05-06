<x-layouts.app title="Attendance Dashboard">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">Attendance Dashboard</h1>
            <p class="text-sm text-gray-600">
                ภาพรวมเวลาทำงาน เดือน {{ $currentMonth->format('m/Y') }}
            </p>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('attendance-summaries.index') }}"
               class="rounded bg-gray-800 px-4 py-2 text-white hover:bg-gray-900">
                สรุปเวลาทำงาน
            </a>

            <a href="{{ route('attendance-summaries.generate-form') }}"
               class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                สร้างสรุป
            </a>
        </div>
    </div>

    <div class="mb-4 flex items-center justify-between">
        <a href="{{ route('attendance-summaries.dashboard', [
            'year' => $previousMonth->year,
            'month' => $previousMonth->month,
        ]) }}"
           class="rounded bg-white px-4 py-2 shadow hover:bg-gray-50">
            ← เดือนก่อน
        </a>

        <form method="GET" action="{{ route('attendance-summaries.dashboard') }}" class="flex gap-2">
            <select name="month" class="rounded border-gray-300">
                @for ($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" @selected($currentMonth->month === $m)>
                        {{ str_pad($m, 2, '0', STR_PAD_LEFT) }}
                    </option>
                @endfor
            </select>

            <input type="number"
                   name="year"
                   value="{{ $currentMonth->year }}"
                   class="w-28 rounded border-gray-300">

            <button class="rounded bg-gray-800 px-4 py-2 text-white">
                แสดงผล
            </button>
        </form>

        <a href="{{ route('attendance-summaries.dashboard', [
            'year' => $nextMonth->year,
            'month' => $nextMonth->month,
        ]) }}"
           class="rounded bg-white px-4 py-2 shadow hover:bg-gray-50">
            เดือนถัดไป →
        </a>
    </div>

    <div class="mb-6 grid gap-4 md:grid-cols-4 lg:grid-cols-8">
        <a href="{{ route('attendance-summaries.index') }}"
           class="rounded bg-white p-5 shadow hover:shadow-md">
            <div class="text-sm text-gray-500">ทั้งหมด</div>
            <div class="mt-2 text-3xl font-bold text-gray-900">
                {{ $summary['total'] }}
            </div>
        </a>

        <a href="{{ route('attendance-summaries.index', ['status' => 'normal']) }}"
           class="rounded bg-white p-5 shadow hover:shadow-md">
            <div class="text-sm text-gray-500">ปกติ</div>
            <div class="mt-2 text-3xl font-bold text-green-600">
                {{ $summary['normal'] }}
            </div>
        </a>

        <a href="{{ route('attendance-summaries.index', ['status' => 'late']) }}"
           class="rounded bg-white p-5 shadow hover:shadow-md">
            <div class="text-sm text-gray-500">มาสาย</div>
            <div class="mt-2 text-3xl font-bold text-yellow-600">
                {{ $summary['late'] }}
            </div>
        </a>

        <a href="{{ route('attendance-summaries.index', ['status' => 'early_leave']) }}"
           class="rounded bg-white p-5 shadow hover:shadow-md">
            <div class="text-sm text-gray-500">กลับก่อน</div>
            <div class="mt-2 text-3xl font-bold text-orange-600">
                {{ $summary['early_leave'] }}
            </div>
        </a>

        <a href="{{ route('attendance-summaries.index', ['status' => 'late_and_early_leave']) }}"
           class="rounded bg-white p-5 shadow hover:shadow-md">
            <div class="text-sm text-gray-500">สาย+กลับก่อน</div>
            <div class="mt-2 text-3xl font-bold text-red-600">
                {{ $summary['late_and_early_leave'] }}
            </div>
        </a>

        <a href="{{ route('attendance-summaries.index', ['status' => 'incomplete']) }}"
           class="rounded bg-white p-5 shadow hover:shadow-md">
            <div class="text-sm text-gray-500">ข้อมูลไม่ครบ</div>
            <div class="mt-2 text-3xl font-bold text-gray-600">
                {{ $summary['incomplete'] }}
            </div>
        </a>

        <a href="{{ route('attendance-summaries.index', ['status' => 'absent']) }}"
           class="rounded bg-white p-5 shadow hover:shadow-md">
            <div class="text-sm text-gray-500">ไม่พบสแกน</div>
            <div class="mt-2 text-3xl font-bold text-red-700">
                {{ $summary['absent'] }}
            </div>
        </a>

        <a href="{{ route('attendance-summaries.index', ['status' => 'off']) }}"
           class="rounded bg-white p-5 shadow hover:shadow-md">
            <div class="text-sm text-gray-500">วันหยุด</div>
            <div class="mt-2 text-3xl font-bold text-gray-500">
                {{ $summary['off'] }}
            </div>
        </a>
    </div>

    <div class="mb-6 grid gap-4 md:grid-cols-2">
        <div class="rounded bg-white p-6 shadow">
            <div class="text-sm text-gray-500">รวมเวลามาสายเดือนนี้</div>
            <div class="mt-2 text-3xl font-bold text-yellow-700">
                {{ $summary['late_minutes'] }} นาที
            </div>
        </div>

        <div class="rounded bg-white p-6 shadow">
            <div class="text-sm text-gray-500">รวมเวลากลับก่อนเดือนนี้</div>
            <div class="mt-2 text-3xl font-bold text-orange-700">
                {{ $summary['early_leave_minutes'] }} นาที
            </div>
        </div>
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

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded bg-white p-6 shadow">
            <h2 class="mb-4 text-lg font-bold">บุคลากรมาสายบ่อย</h2>

            <div class="overflow-hidden rounded border">
                <table class="w-full border-collapse">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-4 py-2 text-left">บุคลากร</th>
                            <th class="border px-4 py-2 text-center">จำนวนครั้ง</th>
                            <th class="border px-4 py-2 text-center">นาทีรวม</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($topLateEmployees as $item)
                            <tr>
                                <td class="border px-4 py-2">
                                    {{ $item->employee?->employee_code }}
                                    <div class="text-xs text-gray-500">
                                        {{ $item->employee?->full_name ?? '-' }}
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        {{ $item->employee?->department?->name ?? '-' }}
                                    </div>
                                </td>

                                <td class="border px-4 py-2 text-center">
                                    {{ $item->late_count }}
                                </td>

                                <td class="border px-4 py-2 text-center">
                                    {{ $item->total_late_minutes ?? 0 }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="border px-4 py-6 text-center text-gray-500">
                                    ไม่มีข้อมูลมาสาย
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded bg-white p-6 shadow">
            <h2 class="mb-4 text-lg font-bold">รายการผิดปกติล่าสุด</h2>

            <div class="space-y-3">
                @forelse ($problemSummaries as $summaryItem)
                    <a href="{{ route('attendance-summaries.index', [
                        'search' => $summaryItem->employee?->employee_code,
                        'date_from' => $summaryItem->work_date?->format('Y-m-d'),
                        'date_to' => $summaryItem->work_date?->format('Y-m-d'),
                    ]) }}"
                       class="block rounded border border-gray-200 p-4 hover:bg-gray-50">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <div class="font-medium">
                                    {{ $summaryItem->employee?->full_name ?? '-' }}
                                </div>

                                <div class="text-sm text-gray-500">
                                    {{ $summaryItem->work_date?->format('Y-m-d') }}
                                    |
                                    {{ $summaryItem->dutySchedule?->shiftType?->name ?? 'ไม่ได้ผูกเวร' }}
                                </div>

                                <div class="text-xs text-gray-400">
                                    เข้า:
                                    {{ $summaryItem->first_in_at?->format('H:i') ?? '-' }}
                                    /
                                    ออก:
                                    {{ $summaryItem->last_out_at?->format('H:i') ?? '-' }}
                                </div>
                            </div>

                            <span class="rounded px-2 py-1 text-xs {{ $statusClasses[$summaryItem->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $statusLabels[$summaryItem->status] ?? $summaryItem->status }}
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="rounded border border-dashed border-gray-300 p-6 text-center text-gray-500">
                        ไม่มีรายการผิดปกติ
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.app>
