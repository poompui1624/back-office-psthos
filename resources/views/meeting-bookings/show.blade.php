<x-layouts.app title="รายละเอียดการจองห้องประชุม">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">{{ $meetingBooking->booking_no }}</h1>
            <p class="text-sm text-gray-600">
                {{ $meetingBooking->title }}
            </p>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('meeting-bookings.print', $meetingBooking) }}"
                target="_blank"
                class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                    พิมพ์ใบจอง
            </a>
                        @if ($meetingBooking->isPending())
                @can('meeting.update')
                    <a href="{{ route('meeting-bookings.edit', $meetingBooking) }}"
                       class="rounded bg-yellow-500 px-4 py-2 text-white hover:bg-yellow-600">
                        แก้ไข
                    </a>
                @endcan
            @endif

            <a href="{{ route('meeting-bookings.index') }}"
               class="rounded bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">
                ย้อนกลับ
            </a>
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

    <div class="mb-6 grid gap-4 md:grid-cols-4">
        <div class="rounded bg-white p-5 shadow">
            <div class="text-sm text-gray-500">สถานะ</div>
            <div class="mt-2">
                <span class="rounded px-2 py-1 text-sm {{ $statusClasses[$meetingBooking->status] ?? 'bg-gray-100 text-gray-800' }}">
                    {{ $statusLabels[$meetingBooking->status] ?? $meetingBooking->status }}
                </span>
            </div>
        </div>

        <div class="rounded bg-white p-5 shadow">
            <div class="text-sm text-gray-500">ห้องประชุม</div>
            <div class="mt-2 font-bold">
                {{ $meetingBooking->room?->name ?? '-' }}
            </div>
        </div>

        <div class="rounded bg-white p-5 shadow">
            <div class="text-sm text-gray-500">เริ่ม</div>
            <div class="mt-2 font-bold">
                {{ $meetingBooking->start_at?->format('Y-m-d H:i') }}
            </div>
        </div>

        <div class="rounded bg-white p-5 shadow">
            <div class="text-sm text-gray-500">สิ้นสุด</div>
            <div class="mt-2 font-bold">
                {{ $meetingBooking->end_at?->format('Y-m-d H:i') }}
            </div>
        </div>
    </div>

    <div class="mb-6 rounded bg-white p-6 shadow">
        <h2 class="mb-4 text-lg font-bold">รายละเอียดการจอง</h2>

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <div class="text-sm text-gray-500">หัวข้อ</div>
                <div class="font-medium">{{ $meetingBooking->title }}</div>
            </div>

            <div>
                <div class="text-sm text-gray-500">จำนวนผู้เข้าร่วม</div>
                <div class="font-medium">{{ $meetingBooking->attendees_count }} คน</div>
            </div>

            <div>
                <div class="text-sm text-gray-500">ผู้จอง</div>
                <div class="font-medium">
                    {{ $meetingBooking->employee?->full_name ?? $meetingBooking->creator?->name ?? '-' }}
                </div>
            </div>

            <div>
                <div class="text-sm text-gray-500">หน่วยงาน</div>
                <div class="font-medium">{{ $meetingBooking->department?->name ?? '-' }}</div>
            </div>

            <div class="md:col-span-2">
                <div class="text-sm text-gray-500">วัตถุประสงค์</div>
                <div class="whitespace-pre-line font-medium">
                    {{ $meetingBooking->purpose ?? '-' }}
                </div>
            </div>

            <div class="md:col-span-2">
                <div class="text-sm text-gray-500">อุปกรณ์ที่ต้องการ</div>

                <div class="mt-2 flex flex-wrap gap-2">
                    @if ($meetingBooking->need_projector)
                        <span class="rounded bg-blue-100 px-2 py-1 text-sm text-blue-800">โปรเจคเตอร์</span>
                    @endif

                    @if ($meetingBooking->need_sound_system)
                        <span class="rounded bg-green-100 px-2 py-1 text-sm text-green-800">เครื่องเสียง</span>
                    @endif

                    @if ($meetingBooking->need_video_conference)
                        <span class="rounded bg-purple-100 px-2 py-1 text-sm text-purple-800">Video Conference</span>
                    @endif

                    @if ($meetingBooking->need_whiteboard)
                        <span class="rounded bg-gray-100 px-2 py-1 text-sm text-gray-800">Whiteboard</span>
                    @endif

                    @if (! $meetingBooking->need_projector && ! $meetingBooking->need_sound_system && ! $meetingBooking->need_video_conference && ! $meetingBooking->need_whiteboard)
                        <span class="text-gray-500">-</span>
                    @endif
                </div>
            </div>

            <div class="md:col-span-2">
                <div class="text-sm text-gray-500">หมายเหตุ</div>
                <div class="whitespace-pre-line font-medium">
                    {{ $meetingBooking->remark ?? '-' }}
                </div>
            </div>
        </div>
    </div>

    <div class="mb-6 rounded bg-white p-6 shadow">
        <h2 class="mb-4 text-lg font-bold">ผลการอนุมัติ</h2>

        <div class="grid gap-4 md:grid-cols-3">
            <div>
                <div class="text-sm text-gray-500">ผู้อนุมัติ</div>
                <div class="font-medium">{{ $meetingBooking->approver?->name ?? '-' }}</div>
            </div>

            <div>
                <div class="text-sm text-gray-500">วันที่อนุมัติ</div>
                <div class="font-medium">{{ $meetingBooking->approved_at?->format('Y-m-d H:i') ?? '-' }}</div>
            </div>

            <div>
                <div class="text-sm text-gray-500">ผู้ไม่อนุมัติ</div>
                <div class="font-medium">{{ $meetingBooking->rejecter?->name ?? '-' }}</div>
            </div>

            <div class="md:col-span-3">
                <div class="text-sm text-gray-500">หมายเหตุการอนุมัติ</div>
                <div class="whitespace-pre-line font-medium">
                    {{ $meetingBooking->approval_remark ?? '-' }}
                </div>
            </div>
        </div>
    </div>

    @if ($meetingBooking->isPending())
        @can('meeting.approve')
            <div class="mb-6 grid gap-4 md:grid-cols-2">
                <div class="rounded bg-white p-6 shadow">
                    <h2 class="mb-4 text-lg font-bold text-green-700">อนุมัติการจอง</h2>

                    <form method="POST" action="{{ route('meeting-bookings.approve', $meetingBooking) }}" class="space-y-3">
                        @csrf
                        @method('PATCH')

                        <textarea name="approval_remark"
                                  rows="3"
                                  class="w-full rounded border-gray-300"
                                  placeholder="หมายเหตุ">{{ old('approval_remark') }}</textarea>

                        <button type="submit"
                                class="rounded bg-green-600 px-4 py-2 text-white hover:bg-green-700">
                            อนุมัติ
                        </button>
                    </form>
                </div>

                <div class="rounded bg-white p-6 shadow">
                    <h2 class="mb-4 text-lg font-bold text-red-700">ไม่อนุมัติการจอง</h2>

                    <form method="POST" action="{{ route('meeting-bookings.reject', $meetingBooking) }}" class="space-y-3">
                        @csrf
                        @method('PATCH')

                        <textarea name="approval_remark"
                                  rows="3"
                                  class="w-full rounded border-gray-300"
                                  placeholder="เหตุผลที่ไม่อนุมัติ">{{ old('approval_remark') }}</textarea>

                        <button type="submit"
                                class="rounded bg-red-600 px-4 py-2 text-white hover:bg-red-700"
                                onclick="return confirm('ยืนยันการไม่อนุมัติรายการนี้?')">
                            ไม่อนุมัติ
                        </button>
                    </form>
                </div>
            </div>
        @endcan
    @endif

    @if (in_array($meetingBooking->status, ['pending', 'approved']))
        @can('meeting.update')
            <div class="mb-6 rounded bg-white p-6 shadow">
                <h2 class="mb-4 text-lg font-bold">ยกเลิกการจอง</h2>

                <form method="POST" action="{{ route('meeting-bookings.cancel', $meetingBooking) }}" class="space-y-3">
                    @csrf
                    @method('PATCH')

                    <textarea name="approval_remark"
                              rows="3"
                              class="w-full rounded border-gray-300"
                              placeholder="เหตุผลการยกเลิก">{{ old('approval_remark') }}</textarea>

                    <button type="submit"
                            class="rounded bg-gray-700 px-4 py-2 text-white hover:bg-gray-800"
                            onclick="return confirm('ยืนยันการยกเลิกการจองนี้?')">
                        ยกเลิกการจอง
                    </button>
                </form>
            </div>
        @endcan
    @endif

    <div class="rounded bg-white p-6 shadow">
        <h2 class="mb-4 text-lg font-bold">ประวัติการดำเนินการ</h2>

        <div class="space-y-3">
            @forelse ($meetingBooking->actions->sortByDesc('created_at') as $action)
                <div class="rounded border border-gray-200 p-4">
                    <div class="flex justify-between">
                        <div class="font-medium">
                            {{ $action->action }}

                            @if ($action->old_status || $action->new_status)
                                <span class="text-sm text-gray-500">
                                    {{ $action->old_status ?? '-' }} → {{ $action->new_status ?? '-' }}
                                </span>
                            @endif
                        </div>

                        <div class="text-sm text-gray-500">
                            {{ $action->created_at->format('Y-m-d H:i') }}
                        </div>
                    </div>

                    <div class="mt-1 text-sm text-gray-600">
                        โดย {{ $action->user?->name ?? 'system' }}
                    </div>

                    @if ($action->remark)
                        <div class="mt-2 text-sm text-gray-700">
                            {{ $action->remark }}
                        </div>
                    @endif
                </div>
            @empty
                <div class="rounded border border-dashed border-gray-300 p-6 text-center text-gray-500">
                    ยังไม่มีประวัติการดำเนินการ
                </div>
            @endforelse
        </div>
    </div>
</x-layouts.app>
