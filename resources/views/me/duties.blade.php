<x-layouts.app>
    @include('me._header', ['employee' => $employee, 'title' => 'เวรของฉัน', 'subtitle' => 'ตารางเวรรายเดือน'])

    @include('me._month-filter', ['route' => 'me.duties', 'month' => $month, 'year' => $year])

    <div class="overflow-hidden rounded-2xl bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-left text-slate-600">
                    <tr>
                        <th class="px-4 py-3 font-semibold">วันที่</th>
                        <th class="px-4 py-3 font-semibold">เวร</th>
                        <th class="px-4 py-3 font-semibold">เวลา</th>
                        <th class="px-4 py-3 font-semibold">หน่วยงาน</th>
                        <th class="px-4 py-3 text-center font-semibold">สถานะ</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse ($schedules as $duty)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $duty->work_date?->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $duty->shiftType?->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-700">
                                {{ $duty->start_at?->format('H:i') }} &ndash; {{ $duty->end_at?->format('H:i') }}
                            </td>
                            <td class="px-4 py-3 text-slate-700">{{ $duty->department?->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">@include('me._status', ['status' => $duty->status])</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-slate-500">
                                ยังไม่มีเวรของคุณในเดือนนี้
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
