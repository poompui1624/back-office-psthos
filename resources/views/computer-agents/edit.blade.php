<x-layouts.app title="แก้ไข Computer Agent">
    <x-page-header title="แก้ไข Computer Agent" :subtitle="$computerAgent->name" />

    <div class="card card-pad max-w-4xl">
        <form method="POST" action="{{ route('computer-agents.update', $computerAgent) }}" class="space-y-6">
            @csrf
            @method('PUT')

            @include('computer-agents._form')

            <x-form.actions :cancel="route('computer-agents.index')" />
        </form>
    </div>
</x-layouts.app>
