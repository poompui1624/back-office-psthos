<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <div>
        <label class="mb-1 block font-medium">Software <span class="text-red-600">*</span></label>
        <select name="software_product_id" class="w-full rounded border-gray-300">
            <option value="">-- เลือก Software --</option>
            @foreach ($products as $product)
                <option value="{{ $product->id }}"
                    @selected(old('software_product_id', $softwareLicense->software_product_id ?? '') == $product->id)>
                    {{ $product->name }} {{ $product->vendor ? '(' . $product->vendor . ')' : '' }}
                </option>
            @endforeach
        </select>
        @error('software_product_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">ชื่อ License</label>
        <input type="text" name="license_name" value="{{ old('license_name', $softwareLicense->license_name ?? '') }}"
               class="w-full rounded border-gray-300">
    </div>

    <div>
        <label class="mb-1 block font-medium">License Key</label>
        <input type="text" name="license_key" value="{{ old('license_key', $softwareLicense->license_key ?? '') }}"
               class="w-full rounded border-gray-300">
    </div>

    <div>
        <label class="mb-1 block font-medium">ประเภท License</label>
        <input type="text" name="license_type" value="{{ old('license_type', $softwareLicense->license_type ?? '') }}"
               class="w-full rounded border-gray-300"
               placeholder="เช่น Per Device, Per User, Subscription, Volume">
    </div>

    <div>
        <label class="mb-1 block font-medium">จำนวนสิทธิ์ทั้งหมด</label>
        <input type="number" name="total_seats" value="{{ old('total_seats', $softwareLicense->total_seats ?? 1) }}"
               class="w-full rounded border-gray-300">
    </div>

    <div>
        <label class="mb-1 block font-medium">จำนวนที่ใช้แล้ว</label>
        <input type="number" name="used_seats" value="{{ old('used_seats', $softwareLicense->used_seats ?? 0) }}"
               class="w-full rounded border-gray-300">
    </div>

    <div>
        <label class="mb-1 block font-medium">วันที่ซื้อ</label>
        <input type="date" name="purchase_date"
               value="{{ old('purchase_date', isset($softwareLicense) && $softwareLicense->purchase_date ? $softwareLicense->purchase_date->format('Y-m-d') : '') }}"
               class="w-full rounded border-gray-300">
    </div>

    <div>
        <label class="mb-1 block font-medium">วันที่เริ่มใช้งาน</label>
        <input type="date" name="start_date"
               value="{{ old('start_date', isset($softwareLicense) && $softwareLicense->start_date ? $softwareLicense->start_date->format('Y-m-d') : '') }}"
               class="w-full rounded border-gray-300">
    </div>

    <div>
        <label class="mb-1 block font-medium">วันหมดอายุ</label>
        <input type="date" name="expire_date"
               value="{{ old('expire_date', isset($softwareLicense) && $softwareLicense->expire_date ? $softwareLicense->expire_date->format('Y-m-d') : '') }}"
               class="w-full rounded border-gray-300">
    </div>

    <div>
        <label class="mb-1 block font-medium">วันที่ต่ออายุ</label>
        <input type="date" name="renewed_at"
               value="{{ old('renewed_at', isset($softwareLicense) && $softwareLicense->renewed_at ? $softwareLicense->renewed_at->format('Y-m-d') : '') }}"
               class="w-full rounded border-gray-300">
    </div>

    <div>
        <label class="mb-1 block font-medium">ราคา</label>
        <input type="number" step="0.01" name="price" value="{{ old('price', $softwareLicense->price ?? '') }}"
               class="w-full rounded border-gray-300">
    </div>

    <div>
        <label class="mb-1 block font-medium">ผู้ขาย / ผู้ติดต่อ</label>
        <input type="text" name="vendor_contact" value="{{ old('vendor_contact', $softwareLicense->vendor_contact ?? '') }}"
               class="w-full rounded border-gray-300">
    </div>

    <div>
        <label class="mb-1 block font-medium">สถานะ</label>
        <select name="status" class="w-full rounded border-gray-300">
            <option value="active" @selected(old('status', $softwareLicense->status ?? 'active') === 'active')>ใช้งาน</option>
            <option value="expired" @selected(old('status', $softwareLicense->status ?? '') === 'expired')>หมดอายุ</option>
            <option value="renewed" @selected(old('status', $softwareLicense->status ?? '') === 'renewed')>ต่ออายุแล้ว</option>
            <option value="cancelled" @selected(old('status', $softwareLicense->status ?? '') === 'cancelled')>ยกเลิก</option>
        </select>
    </div>

    <div class="md:col-span-2">
        <label class="mb-1 block font-medium">หมายเหตุ</label>
        <textarea name="remark" rows="3"
                  class="w-full rounded border-gray-300">{{ old('remark', $softwareLicense->remark ?? '') }}</textarea>
    </div>
</div>
