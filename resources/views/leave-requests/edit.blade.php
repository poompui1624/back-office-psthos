<x-layouts.app title="แก้ไขคำขอลา">
    <x-page-header title="แก้ไขคำขอลา" :subtitle="$leaveRequest->request_no" />

    @if (session('error'))
        <div class="mb-4 rounded bg-red-100 px-4 py-3 text-red-800">
            {{ session('error') }}
        </div>
    @endif

    <div class="card card-pad max-w-4xl">
        <form method="POST" action="{{ route('leave-requests.update', $leaveRequest) }}" class="space-y-6">
            @csrf
            @method('PUT')

            @include('leave-requests._form')

            <x-form.actions :cancel="route('leave-requests.show', $leaveRequest)" />
        </form>
    </div>
</x-layouts.app>
