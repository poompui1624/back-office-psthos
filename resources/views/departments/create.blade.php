<x-layouts.app title="เพิ่มหน่วยงาน">
    <x-page-header title="เพิ่มหน่วยงาน" subtitle="เพิ่มกลุ่มงาน แผนก หรือหน่วยงานภายในโรงพยาบาล" />

    <div class="card card-pad max-w-3xl">
        <form method="POST" action="{{ route('departments.store') }}" class="space-y-6">
            @csrf

            @include('departments._form', ['department' => null, 'parents' => $parents])

            <x-form.actions :cancel="route('departments.index')" />
        </form>
    </div>
</x-layouts.app>
