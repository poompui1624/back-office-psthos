<x-layouts.app title="แก้ไขแจ้งซ่อม">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">แก้ไขแจ้งซ่อม</h1>
        <p class="text-sm text-gray-600">
            {{ $repairRequest->ticket_no }} / {{ $repairRequest->title }}
        </p>
    </div>

    <div class="rounded bg-white p-6 shadow">
        <form method="POST" action="{{ route('repair-requests.update', $repairRequest) }}" class="space-y-4">
            @csrf
            @method('PUT')

            @include('repair-requests._form')

            <div class="flex gap-2">
                <button type="submit"
                        class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                    บันทึกการแก้ไข
                </button>

                <a href="{{ route('repair-requests.show', $repairRequest) }}"
                   class="rounded bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">
                    ย้อนกลับ
                </a>
            </div>
        </form>
    </div>
</x-layouts.app>
