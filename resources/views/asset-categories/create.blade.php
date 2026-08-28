<x-layouts.app title="เพิ่มหมวดหมู่พัสดุ">
    <x-page-header title="เพิ่มหมวดหมู่พัสดุ" />

    <div class="card card-pad max-w-4xl">
        <form method="POST" action="{{ route('asset-categories.store') }}" class="space-y-6">
            @csrf
            @include('asset-categories._form')

            <x-form.actions :cancel="route('asset-categories.index')" />
        </form>
    </div>
</x-layouts.app>
