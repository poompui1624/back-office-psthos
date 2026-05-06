<x-layouts.app title="เพิ่มตารางเวร">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">เพิ่มตารางเวร</h1>
        <p class="text-sm text-gray-600">มอบหมายเวรให้บุคลากร</p>
    </div>

    <div class="rounded bg-white p-6 shadow">
        <form method="POST" action="{{ route('duty-schedules.store') }}" class="space-y-4">
            @csrf

            @include('duty-schedules._form')

            <div class="flex gap-2">
                <button type="submit"
                        class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                    บันทึก
                </button>

                <a href="{{ route('duty-schedules.index') }}"
                   class="rounded bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">
                    ย้อนกลับ
                </a>
            </div>
        </form>
    </div>
</x-layouts.app>
