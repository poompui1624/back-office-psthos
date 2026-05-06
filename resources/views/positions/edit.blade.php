<x-layouts.app title="แก้ไขตำแหน่ง">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">แก้ไขตำแหน่ง</h1>
        <p class="text-sm text-gray-600">{{ $position->name }}</p>
    </div>

    <div class="rounded bg-white p-6 shadow">
        <form method="POST" action="{{ route('positions.update', $position) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="mb-1 block font-medium">
                    ชื่อตำแหน่ง <span class="text-red-600">*</span>
                </label>

                <input type="text"
                       name="name"
                       value="{{ old('name', $position->name) }}"
                       class="w-full rounded border-gray-300">

                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-1 block font-medium">ระดับ</label>

                <input type="text"
                       name="level"
                       value="{{ old('level', $position->level) }}"
                       class="w-full rounded border-gray-300">

                @error('level')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox"
                       name="is_active"
                       value="1"
                       @checked(old('is_active', $position->is_active))>

                <label>เปิดใช้งาน</label>
            </div>

            <div class="flex gap-2">
                <button type="submit"
                        class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                    บันทึกการแก้ไข
                </button>

                <a href="{{ route('positions.index') }}"
                   class="rounded bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">
                    ย้อนกลับ
                </a>
            </div>
        </form>
    </div>
</x-layouts.app>
