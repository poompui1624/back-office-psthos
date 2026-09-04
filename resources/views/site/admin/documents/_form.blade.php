<div class="grid gap-4 md:grid-cols-2">
    <x-form.field label="หมวด" name="category" required>
        <x-form.select name="category" required>
            @foreach (\App\Models\SiteDocument::categories() as $value => $label)
                <option value="{{ $value }}" @selected(old('category', $document?->category ?? 'procurement') === $value)>
                    {{ $label }}
                </option>
            @endforeach
        </x-form.select>
    </x-form.field>

    <x-form.field label="วันเวลาที่เผยแพร่" name="published_at"
                  hint="ตั้งเป็นอนาคตเพื่อให้ขึ้นเองตามเวลา เว้นว่างแล้วติ๊กเผยแพร่ = ขึ้นทันที">
        <x-form.input type="datetime-local" name="published_at"
                      :value="old('published_at', $document?->published_at?->format('Y-m-d\TH:i'))" />
    </x-form.field>
</div>

<x-form.field label="ชื่อเอกสาร" name="title" required>
    <x-form.input name="title" :value="old('title', $document?->title)" required />
</x-form.field>

<x-form.field label="รายละเอียด" name="description" hint="ข้อความธรรมดา แสดงในหน้ารายละเอียดเอกสาร">
    <x-form.textarea name="description" rows="5" :value="old('description', $document?->description)" />
</x-form.field>

<x-form.field label="ไฟล์เอกสาร" name="document_file" :required="$document === null"
              hint="PDF, Word, Excel หรือ PowerPoint ไม่เกิน 20MB">
    @if ($document)
        <div class="mb-3 flex flex-wrap items-center gap-3 rounded-xl bg-slate-50 p-3">
            <x-icon :name="$document->icon" class="h-5 w-5 text-slate-500" />

            <span class="min-w-0 flex-1 truncate text-sm text-slate-700">{{ $document->file_original_name }}</span>

            <span class="text-xs uppercase text-slate-500">{{ $document->file_extension }} &middot; {{ $document->file_size_human }}</span>
        </div>

        <p class="mb-2 text-xs text-slate-500">เลือกไฟล์ใหม่เพื่อแทนที่ไฟล์เดิม</p>
    @endif

    <input type="file" name="document_file" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx"
           class="w-full rounded-xl border border-slate-200 bg-white p-2 text-sm shadow-sm file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-slate-700">
</x-form.field>

<x-form.checkbox name="is_published" label="เผยแพร่บนหน้าเว็บ"
                 :checked="old('is_published', $document?->is_published ?? false)" />
