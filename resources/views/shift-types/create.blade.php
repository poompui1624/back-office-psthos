<x-layouts.app title="เพิ่มประเภทเวร">
    <x-page-header title="เพิ่มประเภทเวร" subtitle="เพิ่มรูปแบบเวรใหม่ เช่น เวรเช้า เวรบ่าย เวรดึก" />

    <div class="card card-pad max-w-4xl">
        <form method="POST" action="{{ route('shift-types.store') }}" class="space-y-6">
            @csrf

            @include('shift-types._form')

            <x-form.actions :cancel="route('shift-types.index')" />
        </form>
    </div>
</x-layouts.app>
