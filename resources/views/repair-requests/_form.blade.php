@php
    $repairType = old('repairable_type');

    if (! $repairType && isset($repairRequest)) {
        if ($repairRequest->repairable_type === \App\Models\Asset::class) {
            $repairType = 'asset';
        } elseif ($repairRequest->repairable_type === \App\Models\Computer::class) {
            $repairType = 'computer';
        } else {
            $repairType = 'other';
        }
    }

    $repairableId = old('repairable_id', $repairRequest->repairable_id ?? '');
@endphp

<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <div>
        <label class="mb-1 block font-medium">ผู้แจ้ง</label>
        <select name="requester_employee_id" class="w-full rounded border-gray-300">
            <option value="">-- เลือกบุคลากร --</option>
            @foreach ($employees as $employee)
                <option value="{{ $employee->id }}"
                    @selected(old('requester_employee_id', $repairRequest->requester_employee_id ?? '') == $employee->id)>
                    {{ $employee->employee_code }} - {{ $employee->full_name }}
                </option>
            @endforeach
        </select>
        @error('requester_employee_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">หน่วยงาน</label>
        <select name="department_id" class="w-full rounded border-gray-300">
            <option value="">-- เลือกหน่วยงาน --</option>
            @foreach ($departments as $department)
                <option value="{{ $department->id }}"
                    @selected(old('department_id', $repairRequest->department_id ?? '') == $department->id)>
                    {{ $department->code }} - {{ $department->name }}
                </option>
            @endforeach
        </select>
        @error('department_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">ประเภทสิ่งที่แจ้งซ่อม</label>
        <select name="repairable_type" id="repairable_type" class="w-full rounded border-gray-300">
            <option value="other" @selected($repairType === 'other' || ! $repairType)>อื่น ๆ</option>
            <option value="asset" @selected($repairType === 'asset')>พัสดุ</option>
            <option value="computer" @selected($repairType === 'computer')>คอมพิวเตอร์</option>
        </select>
        @error('repairable_type')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">รายการที่เกี่ยวข้อง</label>
        <select name="repairable_id" id="repairable_id" class="w-full rounded border-gray-300">
            <option value="">-- ไม่ระบุ --</option>

            <optgroup label="พัสดุ" data-type="asset">
                @foreach ($assets as $asset)
                    <option value="{{ $asset->id }}"
                            data-type="asset"
                            @selected($repairType === 'asset' && $repairableId == $asset->id)>
                        {{ $asset->asset_code }} - {{ $asset->name }}
                    </option>
                @endforeach
            </optgroup>

            <optgroup label="คอมพิวเตอร์" data-type="computer">
                @foreach ($computers as $computer)
                    <option value="{{ $computer->id }}"
                            data-type="computer"
                            @selected($repairType === 'computer' && $repairableId == $computer->id)>
                        {{ $computer->hostname }} - {{ $computer->ip_address ?? '-' }}
                    </option>
                @endforeach
            </optgroup>
        </select>
        @error('repairable_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">หมวดหมู่ <span class="text-red-600">*</span></label>
        <select name="category" class="w-full rounded border-gray-300">
            <option value="general" @selected(old('category', $repairRequest->category ?? 'general') === 'general')>ทั่วไป</option>
            <option value="it" @selected(old('category', $repairRequest->category ?? '') === 'it')>IT / คอมพิวเตอร์</option>
            <option value="network" @selected(old('category', $repairRequest->category ?? '') === 'network')>Network</option>
            <option value="asset" @selected(old('category', $repairRequest->category ?? '') === 'asset')>พัสดุ / ครุภัณฑ์</option>
            <option value="building" @selected(old('category', $repairRequest->category ?? '') === 'building')>อาคารสถานที่</option>
        </select>
        @error('category')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">ความเร่งด่วน</label>
        <select name="priority" class="w-full rounded border-gray-300">
            <option value="low" @selected(old('priority', $repairRequest->priority ?? '') === 'low')>ต่ำ</option>
            <option value="normal" @selected(old('priority', $repairRequest->priority ?? 'normal') === 'normal')>ปกติ</option>
            <option value="high" @selected(old('priority', $repairRequest->priority ?? '') === 'high')>สูง</option>
            <option value="urgent" @selected(old('priority', $repairRequest->priority ?? '') === 'urgent')>ด่วนมาก</option>
        </select>
        @error('priority')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label class="mb-1 block font-medium">หัวข้อแจ้งซ่อม <span class="text-red-600">*</span></label>
        <input type="text"
               name="title"
               value="{{ old('title', $repairRequest->title ?? '') }}"
               class="w-full rounded border-gray-300"
               placeholder="เช่น เครื่องปริ้นไม่ออก, คอมเปิดไม่ติด, อินเทอร์เน็ตใช้งานไม่ได้">
        @error('title')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label class="mb-1 block font-medium">รายละเอียดอาการ</label>
        <textarea name="description"
                  rows="4"
                  class="w-full rounded border-gray-300">{{ old('description', $repairRequest->description ?? '') }}</textarea>
        @error('description')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">สถานที่</label>
        <input type="text"
               name="location"
               value="{{ old('location', $repairRequest->location ?? '') }}"
               class="w-full rounded border-gray-300"
               placeholder="เช่น ห้องบัตร, OPD, ER">
        @error('location')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">มอบหมายให้</label>
        <select name="assigned_to" class="w-full rounded border-gray-300">
            <option value="">-- ยังไม่มอบหมาย --</option>
            @foreach ($users as $user)
                <option value="{{ $user->id }}"
                    @selected(old('assigned_to', $repairRequest->assigned_to ?? '') == $user->id)>
                    {{ $user->name }} / {{ $user->email }}
                </option>
            @endforeach
        </select>
        @error('assigned_to')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const typeSelect = document.getElementById('repairable_type');
        const itemSelect = document.getElementById('repairable_id');

        function filterItems() {
            const selectedType = typeSelect.value;

            Array.from(itemSelect.options).forEach(function (option) {
                if (!option.dataset.type) {
                    option.hidden = false;
                    return;
                }

                option.hidden = option.dataset.type !== selectedType;
            });

            const selectedOption = itemSelect.options[itemSelect.selectedIndex];

            if (selectedOption && selectedOption.dataset.type && selectedOption.dataset.type !== selectedType) {
                itemSelect.value = '';
            }

            if (selectedType === 'other') {
                itemSelect.value = '';
            }
        }

        typeSelect.addEventListener('change', filterItems);
        filterItems();
    });
</script>
