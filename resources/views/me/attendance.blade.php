<x-layouts.app>
    @include('me._header', ['employee' => $employee, 'title' => 'ลงเวลาของฉัน', 'subtitle' => 'สรุปการเข้าออกงานรายเดือน'])

    @include('me._month-filter', ['route' => 'me.attendance', 'month' => $month, 'year' => $year])

    <div class="mb-4 grid gap-4 sm:grid-cols-3">
        <div class="rounded-2xl bg-white p-5 shadow-sm">
            <div class="text-sm text-slate-500">จำนวนวันที่มีบันทึก</div>
            <div class="mt-1 text-3xl font-bold text-slate-900">{{ $summaries->count() }}</div>
        </div>

        <div class="rounded-2xl bg-white p-5 shadow-sm">
            <div class="text-sm text-slate-500">รวมมาสาย (นาที)</div>
            <div class="mt-1 text-3xl font-bold {{ $lateMinutes > 0 ? 'text-amber-600' : 'text-slate-900' }}">{{ $lateMinutes }}</div>
        </div>

        <div class="rounded-2xl bg-white p-5 shadow-sm">
            <div class="text-sm text-slate-500">ขาดงาน (วัน)</div>
            <div class="mt-1 text-3xl font-bold {{ $absentDays > 0 ? 'text-rose-600' : 'text-slate-900' }}">{{ $absentDays }}</div>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-left text-slate-600">
                    <tr>
                        <th class="px-4 py-3 font-semibold">วันที่</th>
                        <th class="px-4 py-3 font-semibold">เข้า</th>
                        <th class="px-4 py-3 font-semibold">ออก</th>
                        <th class="px-4 py-3 text-center font-semibold">มาสาย</th>
                        <th class="px-4 py-3 text-center font-semibold">กลับก่อน</th>
                        <th class="px-4 py-3 text-center font-semibold">สถานะ</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse ($summaries as $summary)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $summary->work_date?->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $summary->first_in_at?->format('H:i') ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $summary->last_out_at?->format('H:i') ?? '-' }}</td>
                            <td class="px-4 py-3 text-center {{ $summary->late_minutes > 0 ? 'font-semibold text-amber-600' : 'text-slate-400' }}">
                                {{ $summary->late_minutes ?: '-' }}
                            </td>
                            <td class="px-4 py-3 text-center {{ $summary->early_leave_minutes > 0 ? 'font-semibold text-amber-600' : 'text-slate-400' }}">
                                {{ $summary->early_leave_minutes ?: '-' }}
                            </td>
                            <td class="px-4 py-3 text-center">@include('me._status', ['status' => $summary->status])</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-slate-500">
                                ยังไม่มีบันทึกการลงเวลาของคุณในเดือนนี้
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
