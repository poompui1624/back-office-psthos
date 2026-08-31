<x-layouts.app title="เพิ่มหัวข้อย่อย MOIT">
    <div class="mx-auto w-full max-w-4xl">
        <x-page-header title="เพิ่มหัวข้อย่อย MOIT" />

        <div class="card card-pad">
            <form method="POST" action="{{ route('ita.moit-sub-topics.store') }}" class="space-y-5">
                @csrf

                @include('ita.moit-sub-topics.form', [
                    'subTopic' => null,
                    'fiscalYears' => $fiscalYears,
                    'mainTopics' => $mainTopics,
                ])

                <x-form.actions :cancel="route('ita.moit-sub-topics.index')" cancel-label="ยกเลิก" />
            </form>
        </div>
    </div>
</x-layouts.app>
