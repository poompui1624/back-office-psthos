<div class="grid gap-4 md:grid-cols-2">
    <x-form.field label="ชื่อ - สกุล" name="name" required>
        <x-form.input name="name" :value="old('name', $executive?->name)" required />
    </x-form.field>

    <x-form.field label="ตำแหน่ง" name="position">
        <x-form.input name="position" :value="old('position', $executive?->position)" placeholder="เช่น ผู้อำนวยการโรงพยาบาล" />
    </x-form.field>
</div>

<div class="grid gap-4 md:grid-cols-2">
    <x-form.field label="โทรศัพท์" name="phone">
        <x-form.input name="phone" :value="old('phone', $executive?->phone)" />
    </x-form.field>

    <x-form.field label="อีเมล" name="email">
        <x-form.input type="email" name="email" :value="old('email', $executive?->email)" />
    </x-form.field>
</div>

<x-form.field label="รูปถ่าย" name="photo"
              hint="JPG, PNG หรือ WEBP ไม่เกิน 2MB — แนะนำรูปแนวตั้งประมาณ 600×800 พิกเซล">
    @if ($executive?->photo_url)
        <div class="mb-3">
            <img src="{{ $executive->photo_url }}" alt="" class="h-32 w-24 rounded-xl object-cover ring-1 ring-slate-200">
            <p class="mt-1.5 text-xs text-slate-500">เลือกไฟล์ใหม่เพื่อแทนที่รูปเดิม</p>
        </div>
    @endif

    <input type="file" name="photo" accept="image/jpeg,image/png,image/webp"
           class="w-full rounded-xl border border-slate-200 bg-white p-2 text-sm shadow-sm file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-slate-700">
</x-form.field>

<x-form.field label="ลำดับแสดงผล" name="sort_order" hint="เลขน้อยอยู่ก่อน" class="max-w-xs">
    <x-form.input type="number" name="sort_order" :value="old('sort_order', $executive?->sort_order ?? 0)" min="0" />
</x-form.field>

<div class="space-y-3">
    <div>
        <x-form.checkbox name="is_featured" label="แสดงเด่นบนหน้าแรก"
                         :checked="old('is_featured', $executive?->is_featured ?? false)" />

        <p class="mt-1 pl-6 text-xs text-slate-500">
            มีได้คนเดียว &mdash; เมื่อติ๊กที่นี่ ระบบจะเอาเครื่องหมายออกจากคนเดิมให้เอง
        </p>
    </div>

    <x-form.checkbox name="is_active" label="แสดงบนหน้าเว็บ"
                     :checked="old('is_active', $executive?->is_active ?? true)" />
</div>
