<div class="grid gap-4 md:grid-cols-2">
    <x-form.field label="หมวด" name="category" required>
        <x-form.select name="category" required>
            @foreach (\App\Models\SitePost::CATEGORIES as $value => $label)
                <option value="{{ $value }}" @selected(old('category', $post?->category ?? 'news') === $value)>
                    {{ $label }}
                </option>
            @endforeach
        </x-form.select>
    </x-form.field>

    <x-form.field label="วันเวลาที่เผยแพร่" name="published_at"
                  hint="ตั้งเป็นอนาคตเพื่อให้ขึ้นเองตามเวลา เว้นว่างแล้วติ๊กเผยแพร่ = ขึ้นทันที">
        <x-form.input type="datetime-local" name="published_at"
                      :value="old('published_at', $post?->published_at?->format('Y-m-d\TH:i'))" />
    </x-form.field>
</div>

<x-form.field label="ชื่อเรื่อง" name="title" required>
    <x-form.input name="title" :value="old('title', $post?->title)" required />
</x-form.field>

@if ($post)
    <x-form.field label="ลิงก์ของเรื่องนี้" name="slug"
                  hint="ลิงก์ที่แชร์ออกไปแล้วจะยังใช้ได้ ตราบใดที่ไม่สร้างใหม่">
        <div class="flex flex-wrap items-center gap-3">
            <code class="min-w-0 flex-1 truncate rounded-xl bg-slate-100 px-3 py-2 text-xs text-slate-600">
                /home/posts/{{ $post->slug }}
            </code>
        </div>

        <div class="mt-2">
            <x-form.checkbox name="regenerate_slug" label="สร้างลิงก์ใหม่จากชื่อเรื่อง" :checked="false" />
        </div>
    </x-form.field>
@endif

<x-form.field label="เกริ่นนำ" name="excerpt" hint="ข้อความสั้นที่แสดงบนการ์ดและใต้หัวข้อ ไม่เกิน 500 ตัวอักษร">
    <x-form.textarea name="excerpt" rows="2" :value="old('excerpt', $post?->excerpt)" />
</x-form.field>

<x-form.field label="เนื้อหา" name="body" hint="ข้อความธรรมดา การขึ้นบรรทัดใหม่จะแสดงตามที่พิมพ์">
    <x-form.textarea name="body" rows="12" :value="old('body', $post?->body)" />
</x-form.field>

<x-form.field label="ภาพปก" name="cover_image"
              hint="JPG, PNG หรือ WEBP ไม่เกิน 2MB — แสดงในสัดส่วน 16:9 แนะนำ 1200×675 พิกเซล">
    @if ($post?->cover_image_url)
        <div class="mb-3">
            <img src="{{ $post->cover_image_url }}" alt="" class="h-28 rounded-xl object-cover ring-1 ring-slate-200">
            <p class="mt-1.5 text-xs text-slate-500">เลือกไฟล์ใหม่เพื่อแทนที่ภาพเดิม</p>
        </div>
    @endif

    <input type="file" name="cover_image" accept="image/jpeg,image/png,image/webp"
           class="w-full rounded-xl border border-slate-200 bg-white p-2 text-sm shadow-sm file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-slate-700">
</x-form.field>

<x-form.field label="ภาพประกอบเพิ่มเติม" name="gallery_images"
              hint="เลือกได้หลายไฟล์พร้อมกัน สูงสุด 20 ไฟล์ต่อครั้ง ไฟล์ละไม่เกิน 2MB">
    <input type="file" name="gallery_images[]" multiple accept="image/jpeg,image/png,image/webp"
           class="w-full rounded-xl border border-slate-200 bg-white p-2 text-sm shadow-sm file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-slate-700">
</x-form.field>

<div class="space-y-3">
    <x-form.checkbox name="is_published" label="เผยแพร่บนหน้าเว็บ"
                     :checked="old('is_published', $post?->is_published ?? false)" />

    <x-form.checkbox name="is_pinned" label="ปักหมุดไว้บนสุด"
                     :checked="old('is_pinned', $post?->is_pinned ?? false)" />
</div>
