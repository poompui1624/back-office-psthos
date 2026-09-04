<x-layouts.app title="ข้อมูล ก.พ.7">
    @php
        $registeredAddress = old('registered_address', $registeredAddress);
        $currentAddress = old('current_address', $currentAddress);
        $familyMembers = old('family_members', $familyMembers);
        $educationHistories = old('education_histories', $educationHistories);
        $trainingHistories = old('training_histories', $trainingHistories);
        $positionHistories = old('position_histories', $positionHistories);
        $salaryHistories = old('salary_histories', $salaryHistories);
        $serviceHistories = old('service_histories', $serviceHistories);
        $disciplinaryHistories = old('disciplinary_histories', $disciplinaryHistories);
        $decorations = old('decorations', $decorations);
        $nameChangeHistories = old('name_change_histories', $nameChangeHistories);
    @endphp

    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">ข้อมูล ก.พ.7</h1>
                <p class="mt-1 text-sm text-slate-500">
                    {{ $employee->employee_code }} - {{ $employee->full_name }}
                    @if ($employee->department)
                        | {{ $employee->department->name }}
                    @endif
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('employees.edit', $employee) }}"
                   class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    ข้อมูลพื้นฐาน
                </a>
                <a href="{{ route('employees.index') }}"
                   class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    กลับรายการ
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="rounded-2xl border border-emerald-100 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        @if ($profile->exists && $profile->updatedBy)
            <div class="rounded-2xl border border-sky-100 bg-sky-50 px-5 py-4 text-sm text-sky-800">
                แก้ไขล่าสุดโดย {{ $profile->updatedBy->name }} เมื่อ {{ $profile->updated_at?->format('d/m/Y H:i') }}
            </div>
        @endif

        <form method="POST" action="{{ route('employees.personnel-profile.update', $employee) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-bold text-slate-900">ข้อมูลส่วนตัวเพิ่มเติม</h2>
                <div class="mt-4 grid gap-4 md:grid-cols-3">
                    <label>
                        <span class="mb-1 block text-sm font-semibold text-slate-600">สัญชาติ</span>
                        <input name="nationality" value="{{ old('nationality', $profile->nationality) }}" class="w-full rounded-xl border-slate-200">
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-semibold text-slate-600">เชื้อชาติ</span>
                        <input name="ethnicity" value="{{ old('ethnicity', $profile->ethnicity) }}" class="w-full rounded-xl border-slate-200">
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-semibold text-slate-600">ศาสนา</span>
                        <input name="religion" value="{{ old('religion', $profile->religion) }}" class="w-full rounded-xl border-slate-200">
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-semibold text-slate-600">กรุ๊ปเลือด</span>
                        <select name="blood_type" class="w-full rounded-xl border-slate-200">
                            <option value="">ไม่ระบุ</option>
                            @foreach (['A', 'B', 'AB', 'O'] as $bloodType)
                                <option value="{{ $bloodType }}" @selected(old('blood_type', $profile->blood_type) === $bloodType)>{{ $bloodType }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-semibold text-slate-600">สถานภาพสมรส</span>
                        <input name="marital_status" value="{{ old('marital_status', $profile->marital_status) }}" class="w-full rounded-xl border-slate-200">
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-semibold text-slate-600">สถานะทางทหาร</span>
                        <input name="military_status" value="{{ old('military_status', $profile->military_status) }}" class="w-full rounded-xl border-slate-200">
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-semibold text-slate-600">สถานที่เกิด</span>
                        <input name="birth_place" value="{{ old('birth_place', $profile->birth_place) }}" class="w-full rounded-xl border-slate-200">
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-semibold text-slate-600">เลขประจำตัวผู้เสียภาษี</span>
                        <input name="taxpayer_no" value="{{ old('taxpayer_no', $profile->taxpayer_no) }}" class="w-full rounded-xl border-slate-200">
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-semibold text-slate-600">เลขประกันสังคม</span>
                        <input name="social_security_no" value="{{ old('social_security_no', $profile->social_security_no) }}" class="w-full rounded-xl border-slate-200">
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-semibold text-slate-600">เลขใบประกอบวิชาชีพ</span>
                        <input name="professional_license_no" value="{{ old('professional_license_no', $profile->professional_license_no) }}" class="w-full rounded-xl border-slate-200">
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-semibold text-slate-600">วันหมดอายุใบประกอบวิชาชีพ</span>
                        <input type="date" name="professional_license_expired_at" value="{{ old('professional_license_expired_at', $profile->professional_license_expired_at?->format('Y-m-d')) }}" class="w-full rounded-xl border-slate-200">
                    </label>
                </div>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-bold text-slate-900">ที่อยู่</h2>
                <div class="mt-4 grid gap-4 lg:grid-cols-2">
                    @foreach (['registered_address' => ['label' => 'ที่อยู่ตามทะเบียนบ้าน', 'data' => $registeredAddress], 'current_address' => ['label' => 'ที่อยู่ปัจจุบัน', 'data' => $currentAddress]] as $field => $address)
                        <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                            <h3 class="font-semibold text-slate-800">{{ $address['label'] }}</h3>
                            <div class="mt-3 grid gap-3 md:grid-cols-2">
                                @foreach (['house_no' => 'บ้านเลขที่', 'moo' => 'หมู่', 'road' => 'ถนน', 'subdistrict' => 'ตำบล/แขวง', 'district' => 'อำเภอ/เขต', 'province' => 'จังหวัด', 'postal_code' => 'รหัสไปรษณีย์', 'phone' => 'โทรศัพท์'] as $key => $label)
                                    <label>
                                        <span class="mb-1 block text-xs font-semibold text-slate-500">{{ $label }}</span>
                                        <input name="{{ $field }}[{{ $key }}]" value="{{ $address['data'][$key] ?? '' }}" class="w-full rounded-xl border-slate-200 bg-white text-sm">
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-bold text-slate-900">ครอบครัวและผู้ติดต่อฉุกเฉิน</h2>
                <div class="mt-4 grid gap-4 md:grid-cols-3">
                    <label>
                        <span class="mb-1 block text-sm font-semibold text-slate-600">ชื่อบิดา</span>
                        <input name="father_name" value="{{ old('father_name', $profile->father_name) }}" class="w-full rounded-xl border-slate-200">
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-semibold text-slate-600">ชื่อมารดา</span>
                        <input name="mother_name" value="{{ old('mother_name', $profile->mother_name) }}" class="w-full rounded-xl border-slate-200">
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-semibold text-slate-600">ชื่อคู่สมรส</span>
                        <input name="spouse_name" value="{{ old('spouse_name', $profile->spouse_name) }}" class="w-full rounded-xl border-slate-200">
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-semibold text-slate-600">ผู้ติดต่อฉุกเฉิน</span>
                        <input name="emergency_contact_name" value="{{ old('emergency_contact_name', $profile->emergency_contact_name) }}" class="w-full rounded-xl border-slate-200">
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-semibold text-slate-600">ความสัมพันธ์</span>
                        <input name="emergency_contact_relation" value="{{ old('emergency_contact_relation', $profile->emergency_contact_relation) }}" class="w-full rounded-xl border-slate-200">
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-semibold text-slate-600">เบอร์ฉุกเฉิน</span>
                        <input name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $profile->emergency_contact_phone) }}" class="w-full rounded-xl border-slate-200">
                    </label>
                </div>

                <div class="mt-5 overflow-x-auto">
                    <table class="w-full min-w-[760px] text-sm">
                        <thead class="bg-slate-50 text-slate-500">
                            <tr>
                                <th class="px-3 py-2 text-left">ชื่อ-สกุล</th>
                                <th class="px-3 py-2 text-left">ความสัมพันธ์</th>
                                <th class="px-3 py-2 text-left">วันเกิด</th>
                                <th class="px-3 py-2 text-left">อาชีพ/สถานะ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($familyMembers as $index => $row)
                                <tr>
                                    <td class="p-2"><input name="family_members[{{ $index }}][name]" value="{{ $row['name'] ?? '' }}" class="w-full rounded-lg border-slate-200"></td>
                                    <td class="p-2"><input name="family_members[{{ $index }}][relation]" value="{{ $row['relation'] ?? '' }}" class="w-full rounded-lg border-slate-200"></td>
                                    <td class="p-2"><input type="date" name="family_members[{{ $index }}][birth_date]" value="{{ $row['birth_date'] ?? '' }}" class="w-full rounded-lg border-slate-200"></td>
                                    <td class="p-2"><input name="family_members[{{ $index }}][occupation]" value="{{ $row['occupation'] ?? '' }}" class="w-full rounded-lg border-slate-200"></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            @include('employees.personnel-profile.partials.history-table', [
                'title' => 'ประวัติการศึกษา',
                'name' => 'education_histories',
                'rows' => $educationHistories,
                'columns' => ['level' => 'ระดับ', 'institution' => 'สถาบัน', 'degree' => 'วุฒิ/สาขา', 'graduated_year' => 'ปีที่จบ'],
            ])

            @include('employees.personnel-profile.partials.history-table', [
                'title' => 'ประวัติอบรม/ดูงาน',
                'name' => 'training_histories',
                'rows' => $trainingHistories,
                'columns' => ['date' => 'วันที่', 'course' => 'หลักสูตร', 'organizer' => 'หน่วยงานจัด', 'hours' => 'ชั่วโมง'],
            ])

            @include('employees.personnel-profile.partials.history-table', [
                'title' => 'ประวัติรับราชการ/การบรรจุ/โอนย้าย',
                'name' => 'service_histories',
                'rows' => $serviceHistories,
                'columns' => ['date' => 'วันที่', 'action' => 'รายการ', 'order_no' => 'เลขคำสั่ง', 'remark' => 'หมายเหตุ'],
            ])

            @include('employees.personnel-profile.partials.history-table', [
                'title' => 'ประวัติการดำรงตำแหน่ง',
                'name' => 'position_histories',
                'rows' => $positionHistories,
                'columns' => ['start_date' => 'เริ่ม', 'end_date' => 'สิ้นสุด', 'position' => 'ตำแหน่ง', 'department' => 'หน่วยงาน'],
            ])

            @include('employees.personnel-profile.partials.history-table', [
                'title' => 'ประวัติเงินเดือน/ระดับ',
                'name' => 'salary_histories',
                'rows' => $salaryHistories,
                'columns' => ['date' => 'วันที่', 'level' => 'ระดับ/ขั้น', 'salary' => 'เงินเดือน', 'order_no' => 'เลขคำสั่ง'],
            ])

            @include('employees.personnel-profile.partials.history-table', [
                'title' => 'วินัย/โทษ/การดำเนินการทางวินัย',
                'name' => 'disciplinary_histories',
                'rows' => $disciplinaryHistories,
                'columns' => ['date' => 'วันที่', 'case' => 'เรื่อง', 'result' => 'ผลดำเนินการ', 'order_no' => 'เลขคำสั่ง'],
            ])

            @include('employees.personnel-profile.partials.history-table', [
                'title' => 'เครื่องราชอิสริยาภรณ์',
                'name' => 'decorations',
                'rows' => $decorations,
                'columns' => ['year' => 'ปี', 'decoration' => 'ชั้นตรา', 'gazette' => 'ราชกิจจาฯ', 'remark' => 'หมายเหตุ'],
            ])

            @include('employees.personnel-profile.partials.history-table', [
                'title' => 'ประวัติเปลี่ยนชื่อ/สกุล',
                'name' => 'name_change_histories',
                'rows' => $nameChangeHistories,
                'columns' => ['date' => 'วันที่', 'old_name' => 'ชื่อเดิม', 'new_name' => 'ชื่อใหม่', 'document_no' => 'เลขเอกสาร'],
            ])

            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <label>
                    <span class="mb-1 block text-sm font-semibold text-slate-600">หมายเหตุ</span>
                    <textarea name="notes" rows="4" class="w-full rounded-xl border-slate-200">{{ old('notes', $profile->notes) }}</textarea>
                </label>
            </section>

            @if ($errors->any())
                <div class="rounded-2xl border border-rose-100 bg-rose-50 px-5 py-4 text-sm text-rose-800">
                    กรุณาตรวจสอบข้อมูลที่กรอกอีกครั้ง
                </div>
            @endif

            <div class="sticky bottom-0 z-10 flex flex-wrap justify-end gap-2 border-t border-slate-200 bg-slate-100/95 py-4 backdrop-blur">
                <a href="{{ route('employees.index') }}" class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    ยกเลิก
                </a>
                @if (auth()->user()?->can('employee.sensitive.update') || auth()->user()?->can('employee.update'))
                    <button type="submit" class="rounded-xl bg-[#02abff] px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-sky-100 hover:bg-sky-600">
                        บันทึกข้อมูล ก.พ.7
                    </button>
                @endif
            </div>
        </form>
    </div>
</x-layouts.app>
