<x-layouts.app title="แก้ไขหน่วยงาน">
    <x-page-header title="แก้ไขหน่วยงาน" :subtitle="$department->code . ' — ' . $department->name" />

    <div class="card card-pad max-w-3xl">
        <form method="POST" action="{{ route('departments.update', $department) }}" class="space-y-6">
            @csrf
            @method('PUT')

            @include('departments._form', ['department' => $department, 'parents' => $parents])

            <x-form.actions :cancel="route('departments.index')" />
        </form>
    </div>
</x-layouts.app>
