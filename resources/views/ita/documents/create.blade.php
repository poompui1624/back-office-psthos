<x-layouts.app title="อัปโหลดไฟล์ ITA">
    <div class="mx-auto flex w-full max-w-4xl flex-col gap-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">อัปโหลดไฟล์ ITA</h1>
            <p class="mt-1 text-sm text-gray-500">
                เลือกปีงบประมาณ หัวข้อหลัก MOIT หัวข้อย่อย และแนบไฟล์เอกสาร
            </p>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <form method="POST"
                  action="{{ route('ita.documents.store') }}"
                  enctype="multipart/form-data"
                  class="space-y-5">
                @csrf

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">ปีงบประมาณ</label>
                        <select name="fiscal_year_id" id="fiscal_year_id" class="w-full rounded border-gray-300" required>
                            <option value="">-- เลือกปีงบประมาณ --</option>
                            @foreach ($fiscalYears as $year)
                                <option value="{{ $year->id }}" @selected(old('fiscal_year_id') == $year->id)>
                                    {{ $year->year }}
                                </option>
                            @endforeach
                        </select>
                        @error('fiscal_year_id')
                            <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">หัวข้อหลัก (MOIT)</label>
                        <select name="main_topic_id" id="main_topic_id" class="w-full rounded border-gray-300" required>
                            <option value="">-- เลือก MOIT --</option>
                            @foreach ($mainTopics as $topic)
                                <option value="{{ $topic->id }}"
                                        data-year="{{ $topic->fiscal_year_id }}"
                                        @selected(old('main_topic_id') == $topic->id)>
                                    {{ $topic->code }} {{ $topic->title }}
                                </option>
                            @endforeach
                        </select>
                        @error('main_topic_id')
                            <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">หัวข้อย่อย</label>
                    <select name="sub_topic_id" id="sub_topic_id" class="w-full rounded border-gray-300">
                        <option value="">-- เลือกหัวข้อย่อย --</option>
                    </select>
                    @error('sub_topic_id')
                        <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">ชื่อเอกสาร</label>
                    <input type="text"
                           name="title"
                           value="{{ old('title') }}"
                           class="w-full rounded border-gray-300"
                           placeholder="ไม่กรอกได้ ระบบจะใช้ชื่อไฟล์แทน">
                    @error('title')
                        <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">รายละเอียด</label>
                    <textarea name="description"
                              rows="3"
                              class="w-full rounded border-gray-300">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">ไฟล์เอกสาร</label>
                    <input type="file"
                           name="document_file"
                           class="w-full rounded border border-gray-300 p-2"
                           required>
                    <p class="mt-1 text-sm text-gray-500">
                        รองรับ PDF / DOCX / Excel / PowerPoint / รูปภาพ ขนาดไม่เกิน 20MB
                    </p>
                    @error('document_file')
                        <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                    @enderror
                </div>

                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_public" value="1" checked class="rounded border-gray-300">
                    <span class="text-sm text-gray-700">เผยแพร่ในหน้าแสดงผล</span>
                </label>

                <div class="flex justify-end gap-2">
                    <a href="{{ route('ita.documents.index') }}"
                       class="rounded bg-gray-200 px-4 py-2 text-sm text-gray-700 hover:bg-gray-300">
                        ยกเลิก
                    </a>

                    <button type="submit"
                            class="rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                        อัปโหลด
                    </button>
                </div>
            </form>
        </div>
    </div>

    @include('ita.documents.partials.topic-script')
</x-layouts.app>
