<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="mb-1 block text-sm font-medium text-gray-700">ปีงบประมาณ</label>
        <select name="fiscal_year_id" id="fiscal_year_id" class="w-full rounded border-gray-300" required>
            <option value="">-- เลือกปีงบประมาณ --</option>
            @foreach ($fiscalYears as $year)
                <option value="{{ $year->id }}"
                    @selected(old('fiscal_year_id', $subTopic?->fiscal_year_id) == $year->id)>
                    {{ $year->year }}
                </option>
            @endforeach
        </select>
        @error('fiscal_year_id')
            <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium text-gray-700">หัวข้อหลัก MOIT</label>
        <select name="main_topic_id" id="main_topic_id" class="w-full rounded border-gray-300" required>
            <option value="">-- เลือก MOIT --</option>
            @foreach ($mainTopics as $topic)
                <option value="{{ $topic->id }}"
                        data-year="{{ $topic->fiscal_year_id }}"
                        @selected(old('main_topic_id', $subTopic?->main_topic_id) == $topic->id)>
                    {{ $topic->code }} {{ Str::limit($topic->title, 80) }}
                </option>
            @endforeach
        </select>
        @error('main_topic_id')
            <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="mb-1 block text-sm font-medium text-gray-700">รหัสหัวข้อย่อย</label>
        <input type="text"
               name="code"
               value="{{ old('code', $subTopic?->code) }}"
               class="w-full rounded border-gray-300"
               placeholder="เช่น 1.1"
               required>
        @error('code')
            <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium text-gray-700">ลำดับแสดงผล</label>
        <input type="number"
               name="sort_order"
               value="{{ old('sort_order', $subTopic?->sort_order ?? 0) }}"
               class="w-full rounded border-gray-300"
               min="0">
        @error('sort_order')
            <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
        @enderror
    </div>
</div>

<div>
    <label class="mb-1 block text-sm font-medium text-gray-700">หัวข้อย่อย</label>
    <textarea name="title"
              rows="4"
              class="w-full rounded border-gray-300"
              required>{{ old('title', $subTopic?->title) }}</textarea>
    @error('title')
        <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
    @enderror
</div>

<div>
    <label class="mb-1 block text-sm font-medium text-gray-700">รายละเอียดเพิ่มเติม</label>
    <textarea name="description"
              rows="3"
              class="w-full rounded border-gray-300">{{ old('description', $subTopic?->description) }}</textarea>
    @error('description')
        <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
    @enderror
</div>

<label class="flex items-center gap-2">
    <input type="checkbox"
           name="is_active"
           value="1"
           @checked(old('is_active', $subTopic?->is_active ?? true))
           class="rounded border-gray-300">
    <span class="text-sm text-gray-700">เปิดใช้งาน</span>
</label>

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
