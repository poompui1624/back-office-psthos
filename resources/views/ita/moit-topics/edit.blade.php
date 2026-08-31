<x-layouts.app title="แก้ไขหัวข้อหลัก MOIT">
    <div class="mx-auto w-full max-w-4xl">
        <x-page-header title="แก้ไขหัวข้อหลัก MOIT" />

        <div class="card card-pad">
            <form method="POST" action="{{ route('ita.moit-topics.update', $moitTopic) }}" class="space-y-5">
                @csrf
                @method('PUT')
                @include('ita.moit-topics.form', [
                    'topic' => $moitTopic,
                    'fiscalYears' => $fiscalYears,
                ])

                <x-form.actions :cancel="route('ita.moit-topics.index')" cancel-label="ยกเลิก" />
            </form>
        </div>
    </div>
</x-layouts.app>
