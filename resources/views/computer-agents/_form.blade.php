<div class="grid gap-5">
    <x-form.field label="ชื่อ Agent" name="name" required>
        <x-form.input name="name" :value="$computerAgent->name ?? ''"
                      placeholder="เช่น Default Hospital Agent / OPD Agent / ER Agent" />
    </x-form.field>

    <x-form.checkbox name="is_active" label="เปิดใช้งาน"
                     :checked="old('is_active', $computerAgent->is_active ?? true)" />
</div>
