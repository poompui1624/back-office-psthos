<div class="space-y-4">
    <div>
        <label class="mb-1 block font-medium">รหัสหมวดหมู่ <span class="text-red-600">*</span></label>
        <input type="text" name="code" value="{{ old('code', $assetCategory->code ?? '') }}"
               class="w-full rounded border-gray-300">
        @error('code') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">ชื่อหมวดหมู่ <span class="text-red-600">*</span></label>
        <input type="text" name="name" value="{{ old('name', $assetCategory->name ?? '') }}"
               class="w-full rounded border-gray-300">
        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">รายละเอียด</label>
        <textarea name="description" rows="3"
                  class="w-full rounded border-gray-300">{{ old('description', $assetCategory->description ?? '') }}</textarea>
        @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="flex items-center gap-2">
        <input type="checkbox" name="is_active" value="1"
               @checked(old('is_active', $assetCategory->is_active ?? true))>
        <label>เปิดใช้งาน</label>
    </div>
</div>
