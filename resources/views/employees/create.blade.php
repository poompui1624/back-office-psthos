<x-layouts.app title="เพิ่มบุคลากร">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">เพิ่มบุคลากร</h1>
        <p class="text-sm text-gray-600">เพิ่มข้อมูลบุคลากรเข้าสู่ระบบกลาง</p>
    </div>

    <div class="rounded bg-white p-6 shadow">
        <form method="POST" action="{{ route('employees.store') }}" class="space-y-4">
            @csrf

            @include('employees._form')

            <div class="flex gap-2">
                <button type="submit"
                        class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                    บันทึก
                </button>

                <a href="{{ route('employees.index') }}"
                   class="rounded bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">
                    ย้อนกลับ
                </a>
            </div>
        </form>
    </div>
</x-layouts.app>
