<x-layouts.app title="ยื่นคำขอลา">
    <x-page-header title="ยื่นคำขอลา" subtitle="บันทึกคำขอลาของบุคลากร" />

    <div class="card card-pad max-w-4xl">
        <form method="POST" action="{{ route('leave-requests.store') }}" class="space-y-6">
            @csrf

            @include('leave-requests._form')

            <x-form.actions :cancel="route('leave-requests.index')" submit-label="บันทึกคำขอ" />
        </form>
    </div>
</x-layouts.app>
