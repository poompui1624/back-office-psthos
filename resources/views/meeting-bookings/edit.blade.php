<x-layouts.app title="แก้ไขการจองห้องประชุม">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">แก้ไขการจองห้องประชุม</h1>
        <p class="text-sm text-gray-600">
            {{ $meetingBooking->booking_no }} - {{ $meetingBooking->title }}
        </p>
    </div>

    @if (session('error'))
        <div class="mb-4 rounded bg-red-100 px-4 py-3 text-red-800">
            {{ session('error') }}
        </div>
    @endif

    <div class="rounded bg-white p-6 shadow">
        <form method="POST" action="{{ route('meeting-bookings.update', $meetingBooking) }}" class="space-y-4">
            @csrf
            @method('PUT')

            @include('meeting-bookings._form')

            <div class="flex gap-2">
                <button type="submit"
                        class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                    บันทึกการแก้ไข
                </button>

                <a href="{{ route('meeting-bookings.show', $meetingBooking) }}"
                   class="rounded bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">
                    ย้อนกลับ
                </a>
            </div>
        </form>
    </div>
</x-layouts.app>
