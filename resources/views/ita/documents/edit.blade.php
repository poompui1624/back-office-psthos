<x-layouts.app title="แก้ไขไฟล์ ITA">
    <div class="mx-auto flex w-full max-w-4xl flex-col gap-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">แก้ไขไฟล์ ITA</h1>
            <p class="mt-1 text-sm text-gray-500">
                แก้ไขรายละเอียดไฟล์ หัวข้อ หรืออัปโหลดไฟล์ใหม่แทนไฟล์เดิม
            </p>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <form method="POST"
                  action="{{ route('ita.documents.update', $document) }}"
                  enctype="multipart/form-data"
                  class="space-y-5">
                @csrf
                @method('PUT')

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">ปีงบประมาณ</label>
                        <select name="fiscal_year_id" id="fiscal_year_id" class="w-full rounded border-gray-300" required>
                            @foreach ($fiscalYears as $year)
                                <option value="{{ $year->id }}" @selected(old('fiscal_year_id', $document->fiscal_year_id) == $year->id)>
                                    {{ $year->year }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">หัวข้อหลัก (MOIT)</label>
                        <select name="main_topic_id" id="main_topic_id" class="w-full rounded border-gray-300" required>
                            @foreach ($mainTopics as $topic)
                                <option value="{{ $topic->id }}"
                                        data-year="{{ $topic->fiscal_year_id }}"
                                        @selected(old('main_topic_id', $document->main_topic_id) == $topic->id)>
                                    {{ $topic->code }} {{ $topic->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">หัวข้อย่อย</label>
                    <select name="sub_topic_id" id="sub_topic_id" class="w-full rounded border-gray-300">
                        <option value="">-- เลือกหัวข้อย่อย --</option>
                        @foreach ($subTopics as $subTopic)
                            <option value="{{ $subTopic->id }}" @selected(old('sub_topic_id', $document->sub_topic_id) == $subTopic->id)>
                                {{ $subTopic->code }} {{ $subTopic->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">ชื่อเอกสาร</label>
                    <input type="text"
                           name="title"
                           value="{{ old('title', $document->title) }}"
                           class="w-full rounded border-gray-300">
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">รายละเอียด</label>
                    <textarea name="description"
                              rows="3"
                              class="w-full rounded border-gray-300">{{ old('description', $document->description) }}</textarea>
                </div>

                <div class="rounded border border-gray-200 bg-gray-50 p-4 text-sm">
                    <div class="font-semibold text-gray-900">ไฟล์ปัจจุบัน</div>
                    <a href="{{ $document->file_url }}" target="_blank" class="mt-1 inline-block text-blue-600 hover:underline">
                        {{ $document->file_original_name }}
                    </a>
                    <div class="mt-1 text-gray-500">{{ $document->file_size_human }}</div>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">อัปโหลดไฟล์ใหม่</label>
                    <input type="file"
                           name="document_file"
                           class="w-full rounded border border-gray-300 p-2">
                    <p class="mt-1 text-sm text-gray-500">
                        ไม่เลือกไฟล์ หากต้องการใช้ไฟล์เดิม
                    </p>
                </div>

                <label class="flex items-center gap-2">
                    <input type="checkbox"
                           name="is_public"
                           value="1"
                           @checked(old('is_public', $document->is_public))
                           class="rounded border-gray-300">
                    <span class="text-sm text-gray-700">เผยแพร่ในหน้าแสดงผล</span>
                </label>

                <div class="flex justify-end gap-2">
                    <a href="{{ route('ita.documents.index') }}"
                       class="rounded bg-gray-200 px-4 py-2 text-sm text-gray-700 hover:bg-gray-300">
                        ยกเลิก
                    </a>

                    <button type="submit"
                            class="rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                        บันทึก
                    </button>
                </div>
            </form>
        </div>
    </div>

    @include('ita.documents.partials.topic-script')
</x-layouts.app>
