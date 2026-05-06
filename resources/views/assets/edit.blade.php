<x-layouts.app title="แก้ไขพัสดุ">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">แก้ไขพัสดุ</h1>
        <p class="text-sm text-gray-600">{{ $asset->asset_code }} - {{ $asset->name }}</p>
    </div>

    <div class="rounded bg-white p-6 shadow">
        <form method="POST" action="{{ route('assets.update', $asset) }}" class="space-y-4">
            @csrf
            @method('PUT')
            @include('assets._form')

            <div class="flex gap-2">
                <button class="rounded bg-blue-600 px-4 py-2 text-white">บันทึกการแก้ไข</button>
                <a href="{{ route('assets.index') }}"
                   class="rounded bg-gray-200 px-4 py-2 text-gray-700">ย้อนกลับ</a>
            </div>
        </form>
    </div>
</x-layouts.app>
