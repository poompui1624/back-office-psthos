<x-layouts.app title="เพิ่มเครื่องสแกนนิ้วมือ">
    <x-page-header title="เพิ่มเครื่องสแกนนิ้วมือ" subtitle="เพิ่มอุปกรณ์บันทึกเวลาทำงาน" />

    <div class="card card-pad max-w-4xl">
        <form method="POST" action="{{ route('attendance-devices.store') }}" class="space-y-6">
            @csrf

            @include('attendance-devices._form')

            <x-form.actions :cancel="route('attendance-devices.index')" />
        </form>
    </div>
</x-layouts.app>
