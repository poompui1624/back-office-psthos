@php
    $licenseStatuses = [
        'active' => 'ใช้งาน',
        'expired' => 'หมดอายุ',
        'renewed' => 'ต่ออายุแล้ว',
        'cancelled' => 'ยกเลิก',
    ];

    $dateValue = fn ($value) => $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '';
@endphp

<div class="grid gap-5 sm:grid-cols-2">
    <x-form.field label="Software" name="software_product_id" required>
        <x-form.select name="software_product_id">
            <option value="">— เลือก Software —</option>

            @foreach ($products as $product)
                <option value="{{ $product->id }}"
                    @selected(old('software_product_id', $softwareLicense->software_product_id ?? '') == $product->id)>
                    {{ $product->name }}
                </option>
            @endforeach
        </x-form.select>
    </x-form.field>

    <x-form.field label="ชื่อ License" name="license_name">
        <x-form.input name="license_name" :value="$softwareLicense->license_name ?? ''" />
    </x-form.field>

    <x-form.field label="License Key" name="license_key" class="sm:col-span-2">
        <x-form.input name="license_key" :value="$softwareLicense->license_key ?? ''" />
    </x-form.field>

    <x-form.field label="ประเภท License" name="license_type">
        <x-form.input name="license_type" :value="$softwareLicense->license_type ?? ''"
                      placeholder="เช่น subscription, perpetual, oem" />
    </x-form.field>

    <x-form.field label="สถานะ" name="status">
        <x-form.select name="status">
            @foreach ($licenseStatuses as $value => $label)
                <option value="{{ $value }}"
                    @selected(old('status', $softwareLicense->status ?? 'active') === $value)>{{ $label }}</option>
            @endforeach
        </x-form.select>
    </x-form.field>

    <x-form.field label="จำนวนสิทธิ์ทั้งหมด" name="total_seats">
        <x-form.input type="number" min="0" name="total_seats" :value="$softwareLicense->total_seats ?? ''" />
    </x-form.field>

    <x-form.field label="จำนวนที่ใช้แล้ว" name="used_seats">
        <x-form.input type="number" min="0" name="used_seats" :value="$softwareLicense->used_seats ?? ''" />
    </x-form.field>

    <x-form.field label="วันที่ซื้อ" name="purchase_date">
        <x-form.input type="date" name="purchase_date" :value="$dateValue($softwareLicense->purchase_date ?? null)" />
    </x-form.field>

    <x-form.field label="วันที่เริ่มใช้งาน" name="start_date">
        <x-form.input type="date" name="start_date" :value="$dateValue($softwareLicense->start_date ?? null)" />
    </x-form.field>

    <x-form.field label="วันหมดอายุ" name="expire_date">
        <x-form.input type="date" name="expire_date" :value="$dateValue($softwareLicense->expire_date ?? null)" />
    </x-form.field>

    <x-form.field label="วันที่ต่ออายุ" name="renewed_at">
        <x-form.input type="date" name="renewed_at" :value="$dateValue($softwareLicense->renewed_at ?? null)" />
    </x-form.field>

    <x-form.field label="ราคา" name="price">
        <x-form.input type="number" step="0.01" min="0" name="price" :value="$softwareLicense->price ?? ''" />
    </x-form.field>

    <x-form.field label="ผู้ขาย / ผู้ติดต่อ" name="vendor_contact">
        <x-form.input name="vendor_contact" :value="$softwareLicense->vendor_contact ?? ''" />
    </x-form.field>

    <x-form.field label="หมายเหตุ" name="remark" class="sm:col-span-2">
        <x-form.textarea name="remark" :value="$softwareLicense->remark ?? ''" rows="3" />
    </x-form.field>
</div>
