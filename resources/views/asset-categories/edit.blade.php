<x-layouts.app title="แก้ไขหมวดหมู่พัสดุ">
    <x-page-header title="แก้ไขหมวดหมู่พัสดุ" :subtitle="$assetCategory->code . ' - ' . $assetCategory->name" />

    <div class="card card-pad max-w-4xl">
        <form method="POST" action="{{ route('asset-categories.update', $assetCategory) }}" class="space-y-6">
            @csrf
            @method('PUT')
            @include('asset-categories._form')

            <x-form.actions :cancel="route('asset-categories.index')" />
        </form>
    </div>
</x-layouts.app>
