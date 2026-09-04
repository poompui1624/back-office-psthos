<x-layouts.app title="แก้ไขเครื่องสแกนนิ้วมือ">
    <x-page-header title="แก้ไขเครื่องสแกนนิ้วมือ" :subtitle="$attendanceDevice->code . ' - ' . $attendanceDevice->name" />

    <div class="card card-pad max-w-4xl">
        <form method="POST" action="{{ route('attendance-devices.update', $attendanceDevice) }}" class="space-y-6">
            @csrf
            @method('PUT')

            @include('attendance-devices._form')

            <x-form.actions :cancel="route('attendance-devices.index')" />
        </form>
    </div>
</x-layouts.app>
