<x-layouts.app title="แก้ไขห้องประชุม">
    <x-page-header title="แก้ไขห้องประชุม" :subtitle="$meetingRoom->code . ' - ' . $meetingRoom->name" />

    <div class="card card-pad max-w-4xl">
        <form method="POST" action="{{ route('meeting-rooms.update', $meetingRoom) }}" class="space-y-6">
            @csrf
            @method('PUT')

            @include('meeting-rooms._form')

            <x-form.actions :cancel="route('meeting-rooms.index')" />
        </form>
    </div>
</x-layouts.app>
