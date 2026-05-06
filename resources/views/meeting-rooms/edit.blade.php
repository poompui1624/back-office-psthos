<x-layouts.app title="แก้ไขห้องประชุม">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">แก้ไขห้องประชุม</h1>
        <p class="text-sm text-gray-600">
            {{ $meetingRoom->code }} - {{ $meetingRoom->name }}
        </p>
    </div>

    <div class="rounded bg-white p-6 shadow">
        <form method="POST" action="{{ route('meeting-rooms.update', $meetingRoom) }}" class="space-y-4">
            @csrf
            @method('PUT')

            @include('meeting-rooms._form')

            <div class="flex gap-2">
                <button type="submit"
                        class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                    บันทึกการแก้ไข
                </button>

                <a href="{{ route('meeting-rooms.index') }}"
                   class="rounded bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">
                    ย้อนกลับ
                </a>
            </div>
        </form>
    </div>
</x-layouts.app>
