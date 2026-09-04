<x-layouts.app title="จองห้องประชุม">
    <x-page-header title="จองห้องประชุม" subtitle="กรอกข้อมูลการจองห้องประชุม" />

    @if (session('error'))
        <div class="mb-4 rounded bg-red-100 px-4 py-3 text-red-800">
            {{ session('error') }}
        </div>
    @endif

    <div class="card card-pad max-w-4xl">
        <form method="POST" action="{{ route('meeting-bookings.store') }}" class="space-y-6">
            @csrf

            @include('meeting-bookings._form')

            <x-form.actions :cancel="route('meeting-bookings.index')" submit-label="บันทึกการจอง" />
        </form>
    </div>
</x-layouts.app>
