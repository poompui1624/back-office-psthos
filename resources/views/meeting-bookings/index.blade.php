<x-layouts.app title="จองห้องประชุม">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">จองห้องประชุม</h1>
            <p class="text-sm text-gray-600">รายการจองห้องประชุมทั้งหมด</p>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('meeting-rooms.index') }}"
               class="rounded bg-gray-800 px-4 py-2 text-white hover:bg-gray-900">
                ห้องประชุม
            </a>

            @can('meeting.create')
                <a href="{{ route('meeting-bookings.create') }}"
                   class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                    จองห้องประชุม
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

    <div class="mb-4 rounded bg-white p-4 shadow">
        <form method="GET" action="{{ route('meeting-bookings.index') }}" class="grid gap-3 md:grid-cols-6">
            <input type="text"
                   name="search"
                   value="{{ $search }}"
                   placeholder="ค้นหาเลขจอง / หัวข้อ / ผู้จอง"
                   class="rounded border-gray-300 md:col-span-2">

            <select name="meeting_room_id" class="rounded border-gray-300">
                <option value="">ทุกห้อง</option>
                @foreach ($rooms as $room)
                    <option value="{{ $room->id }}" @selected($roomId == $room->id)>
                        {{ $room->code }} - {{ $room->name }}
                    </option>
                @endforeach
            </select>

            <select name="status" class="rounded border-gray-300">
                <option value="">ทุกสถานะ</option>
                <option value="pending" @selected($status === 'pending')>รออนุมัติ</option>
                <option value="approved" @selected($status === 'approved')>อนุมัติแล้ว</option>
                <option value="rejected" @selected($status === 'rejected')>ไม่อนุมัติ</option>
                <option value="cancelled" @selected($status === 'cancelled')>ยกเลิก</option>
            </select>

            <input type="date"
                   name="date_from"
                   value="{{ $dateFrom }}"
                   class="rounded border-gray-300">

            <input type="date"
                   name="date_to"
                   value="{{ $dateTo }}"
                   class="rounded border-gray-300">

            <div class="flex gap-2 md:col-span-6">
                <button type="submit"
                        class="rounded bg-gray-800 px-4 py-2 text-white hover:bg-gray-900">
                    ค้นหา
                </button>

                <a href="{{ route('meeting-bookings.index') }}"
                   class="rounded bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">
                    ล้าง
                </a>
            </div>
        </form>
    </div>

    @php
        $statusLabels = [
            'pending' => 'รออนุมัติ',
            'approved' => 'อนุมัติแล้ว',
            'rejected' => 'ไม่อนุมัติ',
            'cancelled' => 'ยกเลิก',
        ];

        $statusClasses = [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'approved' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
            'cancelled' => 'bg-gray-100 text-gray-800',
        ];
    @endphp

    <div class="overflow-hidden rounded bg-white shadow">
        <table class="w-full border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-4 py-2 text-left">เลขจอง</th>
                    <th class="border px-4 py-2 text-left">หัวข้อ</th>
                    <th class="border px-4 py-2 text-left">ห้อง</th>
                    <th class="border px-4 py-2 text-left">ผู้จอง</th>
                    <th class="border px-4 py-2 text-center">วันเวลา</th>
                    <th class="border px-4 py-2 text-center">สถานะ</th>
                    <th class="border px-4 py-2 text-center">จัดการ</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($bookings as $booking)
                    <tr>
                        <td class="border px-4 py-2 whitespace-nowrap">
                            {{ $booking->booking_no }}
                        </td>

                        <td class="border px-4 py-2">
                            {{ $booking->title }}

                            @if ($booking->purpose)
                                <div class="text-xs text-gray-500">
                                    {{ Str::limit($booking->purpose, 80) }}
                                </div>
                            @endif
                        </td>

                        <td class="border px-4 py-2">
                            {{ $booking->room?->name ?? '-' }}
                            <div class="text-xs text-gray-500">
                                {{ $booking->room?->location ?? '-' }}
                            </div>
                        </td>

                        <td class="border px-4 py-2">
                            {{ $booking->employee?->full_name ?? $booking->creator?->name ?? '-' }}
                            <div class="text-xs text-gray-500">
                                {{ $booking->department?->name ?? '-' }}
                            </div>
                        </td>

                        <td class="border px-4 py-2 text-center whitespace-nowrap">
                            {{ $booking->start_at?->format('Y-m-d H:i') }}
                            <div class="text-xs text-gray-500">
                                ถึง {{ $booking->end_at?->format('Y-m-d H:i') }}
                            </div>
                        </td>

                        <td class="border px-4 py-2 text-center">
                            <span class="rounded px-2 py-1 text-xs {{ $statusClasses[$booking->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $statusLabels[$booking->status] ?? $booking->status }}
                            </span>
                        </td>

                        <td class="border px-4 py-2 text-center">
                            <div class="flex flex-wrap justify-center gap-2">
                                <a href="{{ route('meeting-bookings.show', $booking) }}"
                                   class="rounded bg-gray-800 px-3 py-1 text-sm text-white hover:bg-gray-900">
                                    รายละเอียด
                                </a>

                                @if ($booking->isPending())
                                    @can('meeting.update')
                                        <a href="{{ route('meeting-bookings.edit', $booking) }}"
                                           class="rounded bg-yellow-500 px-3 py-1 text-sm text-white hover:bg-yellow-600">
                                            แก้ไข
                                        </a>
                                    @endcan

                                    @can('meeting.delete')
                                        <form method="POST"
                                              action="{{ route('meeting-bookings.destroy', $booking) }}"
                                              onsubmit="return confirm('ยืนยันการลบรายการจองนี้?')">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="rounded bg-red-600 px-3 py-1 text-sm text-white hover:bg-red-700">
                                                ลบ
                                            </button>
                                        </form>
                                    @endcan
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="border px-4 py-6 text-center text-gray-500">
                            ยังไม่มีรายการจองห้องประชุม
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $bookings->links() }}
    </div>
</x-layouts.app>
