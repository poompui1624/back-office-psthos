<x-layouts.app title="แก้ไขตารางเวร">
    <x-page-header title="แก้ไขตารางเวร" :subtitle="$dutySchedule->employee?->full_name . ' / ' . $dutySchedule->work_date?->format('Y-m-d')" />

    <div class="card card-pad max-w-4xl">
        <form method="POST" action="{{ route('duty-schedules.update', $dutySchedule) }}" class="space-y-6">
            @csrf
            @method('PUT')

            @include('duty-schedules._form')

            <x-form.actions :cancel="route('duty-schedules.index')" />
        </form>
    </div>
</x-layouts.app>
