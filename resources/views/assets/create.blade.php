<x-layouts.app title="เพิ่มพัสดุ">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">เพิ่มพัสดุ</h1>
    </div>

    <div class="rounded bg-white p-6 shadow">
        <form method="POST" action="{{ route('assets.store') }}" class="space-y-4">
            @csrf
            @include('assets._form')

            <div class="flex gap-2">
                <button class="rounded bg-blue-600 px-4 py-2 text-white">บันทึก</button>
                <a href="{{ route('assets.index') }}"
                   class="rounded bg-gray-200 px-4 py-2 text-gray-700">ย้อนกลับ</a>
            </div>
        </form>
    </div>
</x-layouts.app>
