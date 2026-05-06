<x-layouts.app title="ห้องประชุม">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">ห้องประชุม</h1>
            <p class="text-sm text-gray-600">จัดการทะเบียนห้องประชุมของโรงพยาบาล</p>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('meeting-bookings.index') }}"
               class="rounded bg-gray-800 px-4 py-2 text-white hover:bg-gray-900">
                รายการจอง
            </a>

            @can('meeting.create')
                <a href="{{ route('meeting-rooms.create') }}"
                   class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                    เพิ่มห้องประชุม
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
        <form method="GET" action="{{ route('meeting-rooms.index') }}" class="flex gap-2">
            <input type="text"
                   name="search"
                   value="{{ $search }}"
                   placeholder="ค้นหารหัส / ชื่อห้อง / สถานที่"
                   class="w-full rounded border-gray-300">

            <button type="submit"
                    class="rounded bg-gray-800 px-4 py-2 text-white hover:bg-gray-900">
                ค้นหา
            </button>

            <a href="{{ route('meeting-rooms.index') }}"
               class="rounded bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">
                ล้าง
            </a>
        </form>
    </div>

    <div class="overflow-hidden rounded bg-white shadow">
        <table class="w-full border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-4 py-2 text-left">รหัส</th>
                    <th class="border px-4 py-2 text-left">ชื่อห้อง</th>
                    <th class="border px-4 py-2 text-left">สถานที่</th>
                    <th class="border px-4 py-2 text-center">ความจุ</th>
                    <th class="border px-4 py-2 text-left">อุปกรณ์</th>
                    <th class="border px-4 py-2 text-center">จำนวนจอง</th>
                    <th class="border px-4 py-2 text-center">สถานะ</th>
                    <th class="border px-4 py-2 text-center">จัดการ</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($rooms as $room)
                    <tr>
                        <td class="border px-4 py-2">{{ $room->code }}</td>

                        <td class="border px-4 py-2">
                            {{ $room->name }}

                            @if ($room->description)
                                <div class="text-xs text-gray-500">
                                    {{ $room->description }}
                                </div>
                            @endif
                        </td>

                        <td class="border px-4 py-2">
                            {{ $room->location ?? '-' }}
                        </td>

                        <td class="border px-4 py-2 text-center">
                            {{ $room->capacity }}
                        </td>

                        <td class="border px-4 py-2">
                            <div class="flex flex-wrap gap-1 text-xs">
                                @if ($room->has_projector)
                                    <span class="rounded bg-blue-100 px-2 py-1 text-blue-800">โปรเจคเตอร์</span>
                                @endif

                                @if ($room->has_sound_system)
                                    <span class="rounded bg-green-100 px-2 py-1 text-green-800">เครื่องเสียง</span>
                                @endif

                                @if ($room->has_video_conference)
                                    <span class="rounded bg-purple-100 px-2 py-1 text-purple-800">Video</span>
                                @endif

                                @if ($room->has_whiteboard)
                                    <span class="rounded bg-gray-100 px-2 py-1 text-gray-800">Whiteboard</span>
                                @endif

                                @if (! $room->has_projector && ! $room->has_sound_system && ! $room->has_video_conference && ! $room->has_whiteboard)
                                    <span class="text-gray-400">-</span>
                                @endif
                            </div>
                        </td>

                        <td class="border px-4 py-2 text-center">
                            {{ $room->bookings_count }}
                        </td>

                        <td class="border px-4 py-2 text-center">
                            @if ($room->is_active)
                                <span class="rounded bg-green-100 px-2 py-1 text-xs text-green-800">
                                    ใช้งาน
                                </span>
                            @else
                                <span class="rounded bg-gray-100 px-2 py-1 text-xs text-gray-700">
                                    ปิดใช้งาน
                                </span>
                            @endif
                        </td>

                        <td class="border px-4 py-2 text-center">
                            <div class="flex justify-center gap-2">
                                @can('meeting.update')
                                    <a href="{{ route('meeting-rooms.edit', $room) }}"
                                       class="rounded bg-yellow-500 px-3 py-1 text-sm text-white hover:bg-yellow-600">
                                        แก้ไข
                                    </a>
                                @endcan

                                @can('meeting.delete')
                                    <form method="POST"
                                          action="{{ route('meeting-rooms.destroy', $room) }}"
                                          onsubmit="return confirm('ยืนยันการลบห้องประชุมนี้?')">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="rounded bg-red-600 px-3 py-1 text-sm text-white hover:bg-red-700">
                                            ลบ
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="border px-4 py-6 text-center text-gray-500">
                            ยังไม่มีข้อมูลห้องประชุม
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $rooms->links() }}
    </div>
</x-layouts.app>
