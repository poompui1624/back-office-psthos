<x-layouts.app title="แก้ไขการจองห้องประชุม">
    <x-page-header title="แก้ไขการจองห้องประชุม" :subtitle="$meetingBooking->booking_no . ' - ' . $meetingBooking->title" />

    @if (session('error'))
        <div class="mb-4 rounded bg-red-100 px-4 py-3 text-red-800">
            {{ session('error') }}
        </div>
    @endif

    <div class="card card-pad max-w-4xl">
        <form method="POST" action="{{ route('meeting-bookings.update', $meetingBooking) }}" class="space-y-6">
            @csrf
            @method('PUT')

            @include('meeting-bookings._form')

            <x-form.actions :cancel="route('meeting-bookings.show', $meetingBooking)" />
        </form>
    </div>
</x-layouts.app>
