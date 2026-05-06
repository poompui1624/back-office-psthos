<x-layouts.app title="แก้ไขหน่วยงาน">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">แก้ไขหน่วยงาน</h1>
        <p class="text-sm text-gray-600">{{ $department->code }} - {{ $department->name }}</p>
    </div>

    <div class="rounded bg-white p-6 shadow">
        <form method="POST" action="{{ route('departments.update', $department) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="mb-1 block font-medium">หน่วยงานแม่</label>
                <select name="parent_id" class="w-full rounded border-gray-300">
                    <option value="">-- ไม่มีหน่วยงานแม่ --</option>
                    @foreach ($parents as $parent)
                        <option value="{{ $parent->id }}"
                            @selected(old('parent_id', $department->parent_id) == $parent->id)>
                            {{ $parent->code }} - {{ $parent->name }}
                        </option>
                    @endforeach
                </select>
                @error('parent_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-1 block font-medium">รหัสหน่วยงาน <span class="text-red-600">*</span></label>
                <input type="text"
                       name="code"
                       value="{{ old('code', $department->code) }}"
                       class="w-full rounded border-gray-300">
                @error('code')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-1 block font-medium">ชื่อหน่วยงาน <span class="text-red-600">*</span></label>
                <input type="text"
                       name="name"
                       value="{{ old('name', $department->name) }}"
                       class="w-full rounded border-gray-300">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-1 block font-medium">ประเภท</label>
                <input type="text"
                       name="type"
                       value="{{ old('type', $department->type) }}"
                       class="w-full rounded border-gray-300">
                @error('type')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox"
                       name="is_active"
                       value="1"
                       @checked(old('is_active', $department->is_active))>
                <label>เปิดใช้งาน</label>
            </div>

            <div class="flex gap-2">
                <button type="submit"
                        class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                    บันทึกการแก้ไข
                </button>

                <a href="{{ route('departments.index') }}"
                   class="rounded bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">
                    ย้อนกลับ
                </a>
            </div>
        </form>
    </div>
</x-layouts.app>
