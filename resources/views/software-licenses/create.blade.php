<x-layouts.app title="เพิ่ม Software License">
    <x-page-header title="เพิ่ม Software License" />

    <div class="card card-pad max-w-4xl">
        <form method="POST" action="{{ route('software-licenses.store') }}" class="space-y-6">
            @csrf
            @include('software-licenses._form')

            <x-form.actions :cancel="route('software-licenses.index')" />
        </form>
    </div>
</x-layouts.app>
