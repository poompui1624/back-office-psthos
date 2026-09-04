<x-layouts.app title="อัปโหลดเอกสาร">
    @include('site.admin._nav')

    <x-page-header title="อัปโหลดเอกสาร" subtitle="เอกสารเผยแพร่บนหน้าเว็บโรงพยาบาล" />

    <div class="card card-pad">
        <form method="POST" action="{{ route('site.documents.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            @include('site.admin.documents._form')

            <x-form.actions submit-label="อัปโหลด" :cancel="route('site.documents.index')" />
        </form>
    </div>
</x-layouts.app>
