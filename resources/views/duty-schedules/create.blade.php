<x-layouts.app title="เพิ่มตารางเวร">
    <x-page-header title="เพิ่มตารางเวร" subtitle="มอบหมายเวรให้บุคลากร" />

    <div class="card card-pad max-w-4xl">
        <form method="POST" action="{{ route('duty-schedules.store') }}" class="space-y-6">
            @csrf

            @include('duty-schedules._form')

            <x-form.actions :cancel="route('duty-schedules.index')" />
        </form>
    </div>
</x-layouts.app>
