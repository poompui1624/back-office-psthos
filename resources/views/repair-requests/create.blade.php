<x-layouts.app title="แจ้งซ่อมใหม่">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">แจ้งซ่อมใหม่</h1>
        <p class="text-sm text-gray-600">บันทึกรายการแจ้งซ่อมเข้าสู่ระบบ</p>
    </div>

    <div class="rounded bg-white p-6 shadow">
        <form method="POST" action="{{ route('repair-requests.store') }}" class="space-y-4">
            @csrf

            @include('repair-requests._form')

            <div class="flex gap-2">
                <button type="submit"
                        class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                    บันทึกแจ้งซ่อม
                </button>

                <a href="{{ route('repair-requests.index') }}"
                   class="rounded bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">
                    ย้อนกลับ
                </a>
            </div>
        </form>
    </div>
</x-layouts.app>
