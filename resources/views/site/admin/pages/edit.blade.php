<x-layouts.app title="แก้ไข{{ $page->title }}">
    @include('site.admin._nav')

    <x-page-header :title="'แก้ไข: '.$page->title" subtitle="เนื้อหาที่แสดงบนหน้าเว็บโรงพยาบาล">
        <x-btn :href="route('site.page', $page->key)" target="_blank" variant="secondary" icon="external-link">
            ดูหน้านี้
        </x-btn>
    </x-page-header>

    <div class="card card-pad">
        <form method="POST" action="{{ route('site.pages.update', $page) }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            <x-form.field label="หัวข้อ" name="title" required>
                <x-form.input name="title" :value="old('title', $page->title)" required />
            </x-form.field>

            <x-form.field label="เนื้อหา" name="body"
                          hint="ข้อความธรรมดา การขึ้นบรรทัดใหม่จะแสดงตามที่พิมพ์">
                <x-form.textarea name="body" rows="14" :value="old('body', $page->body)" />
            </x-form.field>

            <x-form.field label="ภาพประกอบ" name="image"
                          hint="JPG, PNG หรือ WEBP ไม่เกิน 2MB — ระบบไม่ย่อภาพให้">
                @if ($page->image_url)
                    <div class="mb-3">
                        <img src="{{ $page->image_url }}" alt="" class="h-32 rounded-xl object-cover ring-1 ring-slate-200">
                        <p class="mt-1.5 text-xs text-slate-500">เลือกไฟล์ใหม่เพื่อแทนที่ภาพเดิม</p>
                    </div>
                @endif

                <input type="file" name="image" accept="image/jpeg,image/png,image/webp"
                       class="w-full rounded-xl border border-slate-200 bg-white p-2 text-sm shadow-sm file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-slate-700">
            </x-form.field>

            <x-form.checkbox name="is_active" label="แสดงบนหน้าเว็บ"
                             :checked="old('is_active', $page->is_active)" />

            <x-form.actions :cancel="route('site.pages.index')" />
        </form>
    </div>
</x-layouts.app>
