<x-layouts.app title="อัปโหลดไฟล์ ITA">
    <div class="mx-auto w-full max-w-4xl">
        @include('ita._nav')

        <x-page-header title="อัปโหลดไฟล์ ITA"
                       subtitle="เลือกปีงบประมาณ หัวข้อหลัก MOIT หัวข้อย่อย และแนบไฟล์เอกสาร" />

        <div class="card card-pad">
            <form method="POST" action="{{ route('ita.documents.store') }}"
                  enctype="multipart/form-data" class="space-y-5">
                @csrf

                {{-- topic-script pairs these three by id; <x-form.select> emits id="{name}". --}}
                <div class="grid gap-4 md:grid-cols-2">
                    <x-form.field label="ปีงบประมาณ" name="fiscal_year_id" required>
                        <x-form.select name="fiscal_year_id" required>
                            <option value="">-- เลือกปีงบประมาณ --</option>

                            @foreach ($fiscalYears as $year)
                                <option value="{{ $year->id }}" @selected(old('fiscal_year_id') == $year->id)>
                                    {{ $year->year }}
                                </option>
                            @endforeach
                        </x-form.select>
                    </x-form.field>

                    <x-form.field label="หัวข้อหลัก (MOIT)" name="main_topic_id" required>
                        <x-form.select name="main_topic_id" required>
                            <option value="">-- เลือก MOIT --</option>

                            @foreach ($mainTopics as $topic)
                                <option value="{{ $topic->id }}"
                                        data-year="{{ $topic->fiscal_year_id }}"
                                        @selected(old('main_topic_id') == $topic->id)>
                                    {{ $topic->code }} {{ $topic->title }}
                                </option>
                            @endforeach
                        </x-form.select>
                    </x-form.field>
                </div>

                {{-- Filled by topic-script once a year and MOIT are chosen. --}}
                <x-form.field label="หัวข้อย่อย" name="sub_topic_id">
                    <x-form.select name="sub_topic_id">
                        <option value="">-- เลือกหัวข้อย่อย --</option>
                    </x-form.select>
                </x-form.field>

                <x-form.field label="ชื่อเอกสาร" name="title">
                    <x-form.input name="title" :value="old('title')"
                                  placeholder="ไม่กรอกได้ ระบบจะใช้ชื่อไฟล์แทน" />
                </x-form.field>

                <x-form.field label="รายละเอียด" name="description">
                    <x-form.textarea name="description" rows="3" :value="old('description')" />
                </x-form.field>

                <x-form.field label="ไฟล์เอกสาร" name="document_file" required
                              hint="รองรับ PDF / DOCX / Excel / PowerPoint / รูปภาพ ขนาดไม่เกิน 20MB">
                    <input type="file" name="document_file" class="w-full rounded-xl border border-slate-200 bg-white p-2 text-sm shadow-sm file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-slate-700" required>
                </x-form.field>

                <x-form.checkbox name="is_public" label="เผยแพร่ในหน้าแสดงผล"
                                 :checked="old('is_public', true)" />

                <x-form.actions submit-label="อัปโหลด" :cancel="route('ita.documents.index')" cancel-label="ยกเลิก" />
            </form>
        </div>
    </div>

    @include('ita.documents.partials.topic-script')
</x-layouts.app>
