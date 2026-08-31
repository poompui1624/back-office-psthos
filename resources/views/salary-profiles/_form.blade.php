@php
    // Every money field below is the same control: a non-negative amount with
    // two decimals. Only the label, name, and optional hint differ.
    $moneyGroups = [
        'รายได้' => [
            'base_salary' => ['label' => 'เงินเดือนพื้นฐาน', 'required' => true],
            'position_allowance' => ['label' => 'เงินประจำตำแหน่ง'],
            'professional_allowance' => ['label' => 'เงินวิชาชีพ'],
            'other_allowance' => ['label' => 'รายได้อื่น'],
        ],
        'รายการหักประจำ' => [
            'social_security' => ['label' => 'ประกันสังคม'],
            'tax' => ['label' => 'ภาษี'],
            'provident_fund' => ['label' => 'กองทุนสำรองเลี้ยงชีพ'],
            'other_deduction' => ['label' => 'รายการหักอื่น'],
        ],
        'อัตราหักจากเวลาทำงาน และ OT' => [
            'late_deduction_per_minute' => ['label' => 'หักมาสาย / นาที'],
            'early_leave_deduction_per_minute' => ['label' => 'หักกลับก่อน / นาที'],
            'absent_deduction_per_day' => ['label' => 'หักขาดงาน / วัน'],
            'ot_rate_per_hour' => ['label' => 'อัตรา OT / ชั่วโมง', 'hint' => 'ใช้คูณกับชั่วโมงเวร OT ที่ยืนยันแล้ว'],
        ],
    ];
@endphp

<div class="space-y-6">
    <x-form.field label="บุคลากร" name="employee_id" required>
        <x-form.select name="employee_id">
            <option value="">-- เลือกบุคลากร --</option>

            @foreach ($employees as $employee)
                <option value="{{ $employee->id }}"
                    @selected(old('employee_id', $salaryProfile->employee_id ?? '') == $employee->id)>
                    {{ $employee->employee_code }} - {{ $employee->full_name }}@if ($employee->department) / {{ $employee->department->name }}@endif
                </option>
            @endforeach
        </x-form.select>
    </x-form.field>

    @foreach ($moneyGroups as $heading => $fields)
        <div>
            <h2 class="section-title border-b border-slate-200 pb-2">{{ $heading }}</h2>

            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                @foreach ($fields as $name => $field)
                    <x-form.field :label="$field['label']" :name="$name"
                                  :required="$field['required'] ?? false"
                                  :hint="$field['hint'] ?? null">
                        <x-form.input type="number" step="0.01" min="0" :name="$name"
                                      :value="old($name, $salaryProfile->{$name} ?? 0)" />
                    </x-form.field>
                @endforeach
            </div>
        </div>
    @endforeach

    <div>
        <h2 class="section-title border-b border-slate-200 pb-2">อื่น ๆ</h2>

        <div class="mt-4 space-y-4">
            <x-form.checkbox name="is_active" label="เปิดใช้งาน"
                             :checked="old('is_active', $salaryProfile->is_active ?? true)" />

            <x-form.field label="หมายเหตุ" name="remark">
                <x-form.textarea name="remark" rows="3"
                                 :value="old('remark', $salaryProfile->remark ?? '')" />
            </x-form.field>
        </div>
    </div>
</div>
