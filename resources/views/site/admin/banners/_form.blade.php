<div class="grid gap-4 md:grid-cols-2">
    <x-form.field label="หัวข้อ" name="title">
        <x-form.input name="title" :value="old('title', $banner?->title)" placeholder="เช่น สวัสดีปีใหม่ 2569" />
    </x-form.field>

    <x-form.field label="ข้อความรอง" name="subtitle">
        <x-form.input name="subtitle" :value="old('subtitle', $banner?->subtitle)" />
    </x-form.field>
</div>

<x-form.field label="ลิงก์เมื่อคลิกแบนเนอร์" name="link_url"
              hint="เว้นว่างได้ถ้าไม่ต้องการให้กดไปหน้าอื่น">
    <x-form.input type="url" name="link_url" :value="old('link_url', $banner?->link_url)" placeholder="https://" />
</x-form.field>

<x-form.field label="ภาพแบนเนอร์" name="image" :required="$banner === null"
              hint="JPG, PNG หรือ WEBP ไม่เกิน 2MB — แนะนำขนาด 1920×600 พิกเซล ระบบไม่ย่อภาพให้">
    @if ($banner?->image_url)
        <div class="mb-3">
            <img src="{{ $banner->image_url }}" alt="" class="h-28 rounded-xl object-cover ring-1 ring-slate-200">
            <p class="mt-1.5 text-xs text-slate-500">เลือกไฟล์ใหม่เพื่อแทนที่ภาพเดิม</p>
        </div>
    @endif

    <input type="file" name="image" accept="image/jpeg,image/png,image/webp"
           class="w-full rounded-xl border border-slate-200 bg-white p-2 text-sm shadow-sm file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-slate-700">
</x-form.field>

<div class="grid gap-4 md:grid-cols-3">
    <x-form.field label="แสดงตั้งแต่" name="starts_at" hint="เว้นว่าง = แสดงทันที">
        <x-form.input type="datetime-local" name="starts_at"
                      :value="old('starts_at', $banner?->starts_at?->format('Y-m-d\TH:i'))" />
    </x-form.field>

    <x-form.field label="แสดงถึง" name="ends_at" hint="เว้นว่าง = ไม่มีกำหนด">
        <x-form.input type="datetime-local" name="ends_at"
                      :value="old('ends_at', $banner?->ends_at?->format('Y-m-d\TH:i'))" />
    </x-form.field>

    <x-form.field label="ลำดับแสดงผล" name="sort_order" hint="เลขน้อยอยู่ก่อน">
        <x-form.input type="number" name="sort_order" :value="old('sort_order', $banner?->sort_order ?? 0)" min="0" />
    </x-form.field>
</div>

<x-form.checkbox name="is_active" label="เปิดใช้งาน" :checked="old('is_active', $banner?->is_active ?? true)" />
