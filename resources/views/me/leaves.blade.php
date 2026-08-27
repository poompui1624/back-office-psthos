<x-layouts.app>
    @include('me._header', ['employee' => $employee, 'title' => 'ใบลาของฉัน', 'subtitle' => 'คำขอลาทั้งหมดที่คุณยื่นไว้'])

    @can('leave.create')
        <div class="mb-4">
            <a href="{{ route('leave-requests.create') }}"
               class="inline-block rounded-xl bg-[#02abff] px-4 py-2 text-sm font-semibold text-white transition hover:bg-sky-500">
                ยื่นใบลาใหม่
            </a>
        </div>
    @endcan

    <div class="overflow-hidden rounded-2xl bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-left text-slate-600">
                    <tr>
                        <th class="px-4 py-3 font-semibold">เลขคำขอ</th>
                        <th class="px-4 py-3 font-semibold">ประเภท</th>
                        <th class="px-4 py-3 font-semibold">ช่วงวันที่</th>
                        <th class="px-4 py-3 text-center font-semibold">จำนวนวัน</th>
                        <th class="px-4 py-3 text-center font-semibold">สถานะ</th>
                        <th class="px-4 py-3 font-semibold">ผู้อนุมัติ</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse ($leaveRequests as $leave)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $leave->request_no }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $leave->leaveType?->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-700">
                                {{ $leave->start_date?->format('d/m/Y') }} &ndash; {{ $leave->end_date?->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3 text-center text-slate-700">{{ rtrim(rtrim(number_format((float) $leave->total_days, 1), '0'), '.') }}</td>
                            <td class="px-4 py-3 text-center">@include('me._status', ['status' => $leave->status])</td>
                            <td class="px-4 py-3 text-slate-700">{{ $leave->approver?->name ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-slate-500">
                                คุณยังไม่เคยยื่นใบลา
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $leaveRequests->links() }}</div>
</x-layouts.app>
