<x-layouts.app title="แก้ไขหัวข้อย่อย MOIT">
    <div class="mx-auto w-full max-w-4xl">
        @include('ita._nav')

        <x-page-header title="แก้ไขหัวข้อย่อย MOIT" />

        <div class="card card-pad">
            <form method="POST" action="{{ route('ita.moit-sub-topics.update', $moitSubTopic) }}" class="space-y-5">
                @csrf
                @method('PUT')
                @include('ita.moit-sub-topics.form', [
                    'subTopic' => $moitSubTopic,
                    'fiscalYears' => $fiscalYears,
                    'mainTopics' => $mainTopics,
                ])

                <x-form.actions :cancel="route('ita.moit-sub-topics.index')" cancel-label="ยกเลิก" />
            </form>
        </div>
    </div>
</x-layouts.app>
