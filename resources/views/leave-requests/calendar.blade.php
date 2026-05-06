<x-layouts.app title="ปฏิทินการลา">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">ปฏิทินการลา</h1>
            <p class="text-sm text-gray-600">
                เดือน {{ $currentMonth->format('m/Y') }}
            </p>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('leave-requests.dashboard') }}"
               class="rounded bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">
                Dashboard
            </a>

            <a href="{{ route('leave-requests.index') }}"
               class="rounded bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">
                รายการลา
            </a>

            @can('leave.create')
                <a href="{{ route('leave-requests.create') }}"
                   class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                    ยื่นคำขอลา
                </a>
            @endcan
        </div>
    </div>

    <div class="mb-4 rounded bg-white p-4 shadow">
        <form method="GET" action="{{ route('leave-requests.calendar') }}" class="grid gap-3 md:grid-cols-5">
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
                   class="rounded border-gray-300">

            <select name="status" class="rounded border-gray-300">
                <option value="">รออนุมัติ + อนุมัติแล้ว</option>
                <option value="pending" @selected($status === 'pending')>รออนุมัติ</option>
                <option value="approved" @selected($status === 'approved')>อนุมัติแล้ว</option>
                <option value="rejected" @selected($status === 'rejected')>ไม่อนุมัติ</option>
                <option value="cancelled" @selected($status === 'cancelled')>ยกเลิก</option>
            </select>

            <button type="submit"
                    class="rounded bg-gray-800 px-4 py-2 text-white hover:bg-gray-900">
                แสดงผล
            </button>

            <a href="{{ route('leave-requests.calendar') }}"
               class="rounded bg-gray-200 px-4 py-2 text-center text-gray-700 hover:bg-gray-300">
                เดือนนี้
            </a>
        </form>
    </div>

    <div class="mb-4 flex items-center justify-between">
        <a href="{{ route('leave-requests.calendar', [
            'year' => $previousMonth->year,
            'month' => $previousMonth->month,
            'status' => $status,
        ]) }}"
           class="rounded bg-white px-4 py-2 shadow hover:bg-gray-50">
            ← เดือนก่อน
        </a>

        <div class="text-xl font-bold">
            {{ $currentMonth->format('F Y') }}
        </div>

        <a href="{{ route('leave-requests.calendar', [
            'year' => $nextMonth->year,
            'month' => $nextMonth->month,
            'status' => $status,
        ]) }}"
           class="rounded bg-white px-4 py-2 shadow hover:bg-gray-50">
            เดือนถัดไป →
        </a>
    </div>

    @php
        $weekDays = ['อาทิตย์', 'จันทร์', 'อังคาร', 'พุธ', 'พฤหัส', 'ศุกร์', 'เสาร์'];

        $weeks = array_chunk($days, 7);

        $statusStyles = [
            'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
            'approved' => 'bg-green-100 text-green-800 border-green-200',
            'rejected' => 'bg-red-100 text-red-800 border-red-200',
            'cancelled' => 'bg-gray-100 text-gray-800 border-gray-200',
        ];

        $statusText = [
            'pending' => 'รออนุมัติ',
            'approved' => 'อนุมัติ',
            'rejected' => 'ไม่อนุมัติ',
            'cancelled' => 'ยกเลิก',
        ];
    @endphp

    <div class="overflow-hidden rounded bg-white shadow">
        <table class="w-full table-fixed border-collapse">
            <thead>
                <tr class="bg-gray-100">
                    @foreach ($weekDays as $dayName)
                        <th class="border px-3 py-3 text-center font-semibold text-gray-700">
                            {{ $dayName }}
                        </th>
                    @endforeach
                </tr>
            </thead>

            <tbody>
                @foreach ($weeks as $week)
                    <tr>
                        @foreach ($week as $day)
                            <td class="h-36 align-top border p-2 {{ $day['is_current_month'] ? 'bg-white' : 'bg-gray-50 text-gray-400' }}">
                                <div class="mb-2 flex items-center justify-between">
                                    <div class="{{ $day['is_today'] ? 'rounded bg-blue-600 px-2 py-1 text-white' : 'font-semibold text-gray-700' }}">
                                        {{ $day['date']->day }}
                                    </div>

                                    @if (count($day['leaves']) > 0)
                                        <div class="rounded-full bg-gray-200 px-2 py-0.5 text-xs text-gray-700">
                                            {{ count($day['leaves']) }}
                                        </div>
                                    @endif
                                </div>

                                <div class="space-y-1">
                                    @foreach ($day['leaves'] as $leave)
                                        @php
                                            $style = $statusStyles[$leave->status] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                                        @endphp

                                        <a href="{{ route('leave-requests.show', $leave) }}"
                                           class="block rounded border px-2 py-1 text-xs {{ $style }}">
                                            <div class="truncate font-medium">
                                                {{ $leave->employee?->full_name ?? '-' }}
                                            </div>

                                            <div class="truncate">
                                                {{ $leave->leaveType?->name ?? '-' }}
                                            </div>

                                            <div class="truncate">
                                                {{ $statusText[$leave->status] ?? $leave->status }}
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-layouts.app>
