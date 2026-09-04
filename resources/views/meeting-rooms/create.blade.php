<x-layouts.app title="เพิ่มห้องประชุม">
    <x-page-header title="เพิ่มห้องประชุม" subtitle="เพิ่มทะเบียนห้องประชุมใหม่" />

    <div class="card card-pad max-w-4xl">
        <form method="POST" action="{{ route('meeting-rooms.store') }}" class="space-y-6">
            @csrf

            @include('meeting-rooms._form')

            <x-form.actions :cancel="route('meeting-rooms.index')" />
        </form>
    </div>
</x-layouts.app>
