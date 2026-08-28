<x-layouts.app title="เพิ่มพัสดุ">
    <x-page-header title="เพิ่มพัสดุ" />

    <div class="card card-pad max-w-4xl">
        <form method="POST" action="{{ route('assets.store') }}" class="space-y-6">
            @csrf
            @include('assets._form')

            <x-form.actions :cancel="route('assets.index')" />
        </form>
    </div>
</x-layouts.app>
