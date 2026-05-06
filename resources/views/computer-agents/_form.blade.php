<div class="space-y-4">
    <div>
        <label class="mb-1 block font-medium">
            ชื่อ Agent <span class="text-red-600">*</span>
        </label>

        <input type="text"
               name="name"
               value="{{ old('name', $computerAgent->name ?? '') }}"
               class="w-full rounded border-gray-300"
               placeholder="เช่น Default Hospital Agent / OPD Agent / ER Agent">

        @error('name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex items-center gap-2">
        <input type="checkbox"
               name="is_active"
               value="1"
               @checked(old('is_active', $computerAgent->is_active ?? true))>

        <label>เปิดใช้งาน</label>
    </div>
</div>
