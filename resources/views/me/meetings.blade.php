<x-layouts.app>
    @include('me._header', ['employee' => $employee, 'title' => 'จองห้องประชุมของฉัน', 'subtitle' => 'รายการจองที่คุณเป็นผู้ขอ'])

    @can('meeting.create')
        <div class="mb-4">
            <a href="{{ route('meeting-bookings.create') }}"
               class="inline-block rounded-xl bg-[#02abff] px-4 py-2 text-sm font-semibold text-white transition hover:bg-sky-500">
                จองห้องประชุม
            </a>
        </div>
    @endcan

    <div class="overflow-hidden rounded-2xl bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-left text-slate-600">
                    <tr>
                        <th class="px-4 py-3 font-semibold">เลขที่</th>
                        <th class="px-4 py-3 font-semibold">เรื่อง</th>
                        <th class="px-4 py-3 font-semibold">ห้อง</th>
                        <th class="px-4 py-3 font-semibold">ช่วงเวลา</th>
                        <th class="px-4 py-3 text-center font-semibold">สถานะ</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse ($bookings as $booking)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $booking->booking_no }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $booking->title }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $booking->room?->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-700">
                                {{ $booking->start_at?->format('d/m/Y H:i') }} &ndash; {{ $booking->end_at?->format('H:i') }}
                            </td>
                            <td class="px-4 py-3 text-center">@include('me._status', ['status' => $booking->status])</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-slate-500">
                                คุณยังไม่เคยจองห้องประชุม
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $bookings->links() }}</div>
</x-layouts.app>
