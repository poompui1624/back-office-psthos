<div class="space-y-4">
    <div>
        <label class="mb-1 block font-medium">ชื่อ Software <span class="text-red-600">*</span></label>
        <input type="text" name="name" value="{{ old('name', $softwareProduct->name ?? '') }}"
               class="w-full rounded border-gray-300">
        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">Vendor</label>
        <input type="text" name="vendor" value="{{ old('vendor', $softwareProduct->vendor ?? '') }}"
               class="w-full rounded border-gray-300">
    </div>

    <div>
        <label class="mb-1 block font-medium">Category</label>
        <input type="text" name="category" value="{{ old('category', $softwareProduct->category ?? '') }}"
               class="w-full rounded border-gray-300"
               placeholder="เช่น Office, Antivirus, OS, Medical">
    </div>

    <div>
        <label class="mb-1 block font-medium">รายละเอียด</label>
        <textarea name="description" rows="3"
                  class="w-full rounded border-gray-300">{{ old('description', $softwareProduct->description ?? '') }}</textarea>
    </div>

    <div class="flex items-center gap-2">
        <input type="checkbox" name="is_active" value="1"
               @checked(old('is_active', $softwareProduct->is_active ?? true))>
        <label>เปิดใช้งาน</label>
    </div>
</div>
