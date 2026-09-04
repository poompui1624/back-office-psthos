<div class="grid gap-4 md:grid-cols-2">
    {{-- The script below finds these two by id; <x-form.select> emits id="{name}". --}}
    <x-form.field label="ปีงบประมาณ" name="fiscal_year_id" required>
        <x-form.select name="fiscal_year_id" required>
            <option value="">-- เลือกปีงบประมาณ --</option>

            @foreach ($fiscalYears as $year)
                <option value="{{ $year->id }}" @selected(old('fiscal_year_id', $subTopic?->fiscal_year_id) == $year->id)>
                    {{ $year->year }}
                </option>
            @endforeach
        </x-form.select>
    </x-form.field>

    <x-form.field label="หัวข้อหลัก MOIT" name="main_topic_id" required>
        <x-form.select name="main_topic_id" required>
            <option value="">-- เลือก MOIT --</option>

            @foreach ($mainTopics as $topic)
                <option value="{{ $topic->id }}"
                        data-year="{{ $topic->fiscal_year_id }}"
                        @selected(old('main_topic_id', $subTopic?->main_topic_id) == $topic->id)>
                    {{ $topic->code }} {{ Str::limit($topic->title, 80) }}
                </option>
            @endforeach
        </x-form.select>
    </x-form.field>
</div>

<div class="grid gap-4 md:grid-cols-2">
    <x-form.field label="รหัสหัวข้อย่อย" name="code" required>
        <x-form.input name="code" :value="old('code', $subTopic?->code)" placeholder="เช่น 1.1" required />
    </x-form.field>

    <x-form.field label="ลำดับแสดงผล" name="sort_order">
        <x-form.input type="number" name="sort_order" :value="old('sort_order', $subTopic?->sort_order ?? 0)" min="0" />
    </x-form.field>
</div>

<x-form.field label="หัวข้อย่อย" name="title" required>
    <x-form.textarea name="title" rows="4" :value="old('title', $subTopic?->title)" required />
</x-form.field>

<x-form.field label="รายละเอียดเพิ่มเติม" name="description">
    <x-form.textarea name="description" rows="3" :value="old('description', $subTopic?->description)" />
</x-form.field>

<div class="space-y-3">
    <x-form.checkbox name="is_heading" label="เป็นหัวข้อกลุ่ม (ไม่ต้องแนบไฟล์)"
                     :checked="old('is_heading', $subTopic?->is_heading ?? false)" />

    <p class="-mt-1 pl-6 text-xs text-slate-500">
        ใช้กับข้อที่เป็นหัวเรื่องของข้อย่อย เช่น &ldquo;ข้อ 3.&rdquo; ที่มี 3.1&ndash;3.3 อยู่ข้างใต้
        หน้าเผยแพร่จะไม่ขีดฆ่าและไม่นับว่าขาดเอกสาร
    </p>

    <x-form.checkbox name="is_active" label="เปิดใช้งาน" :checked="old('is_active', $subTopic?->is_active ?? true)" />
</div>

<script>
    const fiscalYearSelect = document.getElementById('fiscal_year_id');
    const mainTopicSelect = document.getElementById('main_topic_id');

    function filterMainTopicsByYear() {
        const selectedYear = fiscalYearSelect.value;

        Array.from(mainTopicSelect.options).forEach(option => {
            if (!option.value) {
                option.hidden = false;
                return;
            }

            option.hidden = option.dataset.year !== selectedYear;
        });

        if (
            mainTopicSelect.selectedOptions.length &&
            mainTopicSelect.selectedOptions[0].hidden
        ) {
            mainTopicSelect.value = '';
        }
    }

    fiscalYearSelect.addEventListener('change', filterMainTopicsByYear);
    filterMainTopicsByYear();
</script>
