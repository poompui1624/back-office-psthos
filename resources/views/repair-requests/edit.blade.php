<x-layouts.app title="แก้ไขแจ้งซ่อม">
    <x-page-header title="แก้ไขแจ้งซ่อม" :subtitle="$repairRequest->ticket_no . ' / ' . $repairRequest->title" />

    <div class="card card-pad max-w-4xl">
        <form method="POST" action="{{ route('repair-requests.update', $repairRequest) }}" class="space-y-6">
            @csrf
            @method('PUT')

            @include('repair-requests._form')

            <x-form.actions :cancel="route('repair-requests.show', $repairRequest)" />
        </form>
    </div>
</x-layouts.app>
