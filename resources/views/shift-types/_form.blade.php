<div class="grid gap-5 sm:grid-cols-2">
    <x-form.field label="รหัสเวร" name="code" required>
        <x-form.input name="code" :value="$shiftType->code ?? ''" placeholder="เช่น M1, A1, N1" />
    </x-form.field>

    <x-form.field label="ชื่อเวร" name="name" required>
        <x-form.input name="name" :value="$shiftType->name ?? ''" placeholder="เช่น เวรเช้า" />
    </x-form.field>

    <x-form.field label="เวลาเริ่ม" name="start_time" required>
        <x-form.input type="time" name="start_time" :value="$shiftType->start_time ?? ''" />
    </x-form.field>

    <x-form.field label="เวลาสิ้นสุด" name="end_time" required>
        <x-form.input type="time" name="end_time" :value="$shiftType->end_time ?? ''" />
    </x-form.field>

    <x-form.field label="สี" name="color">
        @php
            $colours = ['blue' => 'น้ำเงิน', 'green' => 'เขียว', 'yellow' => 'เหลือง', 'purple' => 'ม่วง', 'red' => 'แดง', 'gray' => 'เทา'];
        @endphp

        <x-form.select name="color">
            <option value="">— ไม่ระบุ —</option>

            @foreach ($colours as $value => $label)
                <option value="{{ $value }}" @selected(old('color', $shiftType->color ?? '') === $value)>{{ $label }}</option>
            @endforeach
        </x-form.select>
    </x-form.field>

    <div class="flex flex-col justify-end gap-3">
        <x-form.checkbox name="crosses_midnight" label="เวรข้ามวัน"
                         :checked="old('crosses_midnight', $shiftType->crosses_midnight ?? false)" />

        <x-form.checkbox name="is_ot" label="นับเป็นเวรล่วงเวลา (OT)"
                         :checked="old('is_ot', $shiftType->is_ot ?? false)" />

        <x-form.checkbox name="is_active" label="เปิดใช้งาน"
                         :checked="old('is_active', $shiftType->is_active ?? true)" />
    </div>

    <x-form.field label="ตัวคูณ OT" name="ot_multiplier"
                  hint="คูณกับอัตรารายชั่วโมงของบุคลากร เช่น 1.5 คือหนึ่งเท่าครึ่ง">
        <x-form.input type="number" step="0.01" min="0" name="ot_multiplier"
                      :value="$shiftType->ot_multiplier ?? 1" />
    </x-form.field>

    <x-form.field label="ค่า OT เหมาจ่าย (บาท)" name="ot_flat_rate"
                  hint="ถ้ากรอกไว้ จะจ่ายเท่านี้ต่อเวร และไม่ใช้ตัวคูณ">
        <x-form.input type="number" step="0.01" min="0" name="ot_flat_rate"
                      :value="$shiftType->ot_flat_rate ?? ''" />
    </x-form.field>

    <x-form.field label="รายละเอียด" name="description" class="sm:col-span-2">
        <x-form.textarea name="description" :value="$shiftType->description ?? ''" rows="3" />
    </x-form.field>
</div>
