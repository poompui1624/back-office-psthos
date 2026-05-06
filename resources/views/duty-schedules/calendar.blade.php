<x-layouts.app title="ปฏิทินตารางเวร">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">ปฏิทินตารางเวร</h1>
            <p class="text-sm text-gray-600">
                เดือน {{ $currentMonth->format('m/Y') }}
            </p>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('duty-schedules.index') }}"
               class="rounded bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">
                แบบตาราง
            </a>

            @can('duty.create')
                <a href="{{ route('duty-schedules.create') }}"
                   class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                    เพิ่มตารางเวร
                </a>
            @endcan
        </div>
    </div>

    <div class="mb-4 rounded bg-white p-4 shadow">
        <form method="GET" action="{{ route('duty-schedules.calendar') }}" class="grid gap-3 md:grid-cols-6">
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

            <select name="department_id" class="rounded border-gray-300">
                <option value="">ทุกหน่วยงาน</option>
                @foreach ($departments as $department)
                    <option value="{{ $department->id }}" @selected($departmentId == $department->id)>
                        {{ $department->code }} - {{ $department->name }}
                    </option>
                @endforeach
            </select>

            <input type="text"
                   name="role_group"
                   value="{{ $roleGroup }}"
                   placeholder="กลุ่มงาน เช่น พยาบาล"
                   class="rounded border-gray-300">

            <button type="submit"
                    class="rounded bg-gray-800 px-4 py-2 text-white hover:bg-gray-900">
                แสดงผล
            </button>

            <a href="{{ route('duty-schedules.calendar') }}"
               class="rounded bg-gray-200 px-4 py-2 text-center text-gray-700 hover:bg-gray-300">
                เดือนนี้
            </a>
        </form>
    </div>

    <div class="mb-4 flex items-center justify-between">
        <a href="{{ route('duty-schedules.calendar', [
            'year' => $previousMonth->year,
            'month' => $previousMonth->month,
            'department_id' => $departmentId,
            'role_group' => $roleGroup,
        ]) }}"
           class="rounded bg-white px-4 py-2 shadow hover:bg-gray-50">
            ← เดือนก่อน
        </a>

        <div class="text-xl font-bold">
            {{ $currentMonth->format('F Y') }}
        </div>

        <a href="{{ route('duty-schedules.calendar', [
            'year' => $nextMonth->year,
            'month' => $nextMonth->month,
            'department_id' => $departmentId,
            'role_group' => $roleGroup,
        ]) }}"
           class="rounded bg-white px-4 py-2 shadow hover:bg-gray-50">
            เดือนถัดไป →
        </a>
    </div>

    @php
        $weekDays = ['อาทิตย์', 'จันทร์', 'อังคาร', 'พุธ', 'พฤหัส', 'ศุกร์', 'เสาร์'];
        $weeks = array_chunk($days, 7);

        $colorClasses = [
            'blue' => 'bg-blue-100 text-blue-800 border-blue-200',
            'green' => 'bg-green-100 text-green-800 border-green-200',
            'yellow' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
            'purple' => 'bg-purple-100 text-purple-800 border-purple-200',
            'red' => 'bg-red-100 text-red-800 border-red-200',
            'gray' => 'bg-gray-100 text-gray-800 border-gray-200',
        ];

        $statusLabels = [
            'assigned' => 'มอบหมายแล้ว',
            'confirmed' => 'ยืนยันแล้ว',
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
                            <td class="h-44 align-top border p-2 {{ $day['is_current_month'] ? 'bg-white' : 'bg-gray-50 text-gray-400' }}">
                                <div class="mb-2 flex items-center justify-between">
                                    <div class="{{ $day['is_today'] ? 'rounded bg-blue-600 px-2 py-1 text-white' : 'font-semibold text-gray-700' }}">
                                        {{ $day['date']->day }}
                                    </div>

                                    @if ($day['schedules']->count() > 0)
                                        <div class="rounded-full bg-gray-200 px-2 py-0.5 text-xs text-gray-700">
                                            {{ $day['schedules']->count() }}
                                        </div>
                                    @endif
                                </div>

                                <div class="space-y-1">
                                    @foreach ($day['schedules'] as $schedule)
                                        @php
                                            $color = $schedule->shiftType?->color ?? 'gray';
                                            $style = $colorClasses[$color] ?? $colorClasses['gray'];
                                        @endphp

                                        <div class="rounded border px-2 py-1 text-xs {{ $style }}">
                                            <div class="truncate font-semibold">
                                                {{ $schedule->employee?->full_name ?? '-' }}
                                            </div>

                                            <div class="truncate">
                                                {{ $schedule->shiftType?->name ?? '-' }}
                                                |
                                                {{ $schedule->start_at?->format('H:i') }}-{{ $schedule->end_at?->format('H:i') }}
                                            </div>

                                            <div class="truncate text-[11px]">
                                                {{ $schedule->department?->name ?? '-' }}
                                            </div>

                                            @if ($schedule->role_group)
                                                <div class="truncate text-[11px]">
                                                    {{ $schedule->role_group }}
                                                </div>
                                            @endif

                                            @if ($schedule->status !== 'assigned')
                                                <div class="mt-1 text-[11px]">
                                                    {{ $statusLabels[$schedule->status] ?? $schedule->status }}
                                                </div>
                                            @endif

                                            @can('duty.update')
                                                <div class="mt-1">
                                                    <a href="{{ route('duty-schedules.edit', $schedule) }}"
                                                       class="underline">
                                                        แก้ไข
                                                    </a>
                                                </div>
                                            @endcan
                                        </div>
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
