<section class="card card-pad mt-6">
    <div class="mb-4">
        <h2 class="section-title">ไฟล์แนบ</h2>
        <p class="muted mt-1">เอกสารที่เกี่ยวข้องกับบุคลากรนี้</p>
    </div>

    @can('attachment.upload')
        <form method="POST"
              action="{{ route('attachments.store') }}"
              enctype="multipart/form-data"
              class="mb-6 rounded-xl border border-dashed border-slate-300 bg-slate-50/60 p-4">
            @csrf

            <input type="hidden" name="module" value="employee">
            <input type="hidden" name="attachable_type" value="employee">
            <input type="hidden" name="attachable_id" value="{{ $employee->id }}">

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <input type="file" name="file"
                       class="w-full rounded-xl border border-slate-200 bg-white p-2 text-sm shadow-sm file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-slate-700">

                <x-btn type="submit" icon="upload" class="shrink-0">อัปโหลด</x-btn>
            </div>

            @error('file')
                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
            @enderror

            <p class="mt-2 text-xs text-slate-500">
                รองรับไฟล์: PDF, JPG, PNG, DOC, DOCX, XLS, XLSX ขนาดไม่เกิน 10MB
            </p>
        </form>
    @endcan

    <x-data-table>
        <x-slot:head>
            <x-data-table.th>ชื่อไฟล์</x-data-table.th>
            <x-data-table.th>ขนาด</x-data-table.th>
            <x-data-table.th>ผู้อัปโหลด</x-data-table.th>
            <x-data-table.th>วันที่</x-data-table.th>
            <x-data-table.th align="center">จัดการ</x-data-table.th>
        </x-slot:head>

        @forelse ($attachments as $attachment)
            <x-data-table.row>
                <x-data-table.td>
                    <div class="font-medium text-slate-900">{{ $attachment->original_name }}</div>
                    <div class="mt-0.5 text-xs text-slate-500">{{ $attachment->mime_type }}</div>
                </x-data-table.td>

                <x-data-table.td class="whitespace-nowrap tabular-nums">{{ $attachment->file_size_text }}</x-data-table.td>
                <x-data-table.td>{{ $attachment->uploader?->name ?? '-' }}</x-data-table.td>

                <x-data-table.td class="whitespace-nowrap tabular-nums">
                    {{ $attachment->created_at->format('Y-m-d H:i') }}
                </x-data-table.td>

                <x-data-table.td align="center">
                    <div class="flex flex-wrap justify-center gap-2">
                        @can('attachment.download')
                            <x-btn :href="route('attachments.download', $attachment)" variant="secondary" size="sm">
                                ดาวน์โหลด
                            </x-btn>
                        @endcan

                        @can('attachment.delete')
                            <form method="POST"
                                  action="{{ route('attachments.destroy', $attachment) }}"
                                  onsubmit="return confirm('ยืนยันการลบไฟล์นี้?')">
                                @csrf
                                @method('DELETE')

                                <x-btn type="submit" variant="danger" size="sm">ลบ</x-btn>
                            </form>
                        @endcan
                    </div>
                </x-data-table.td>
            </x-data-table.row>
        @empty
            <x-data-table.empty :colspan="5" icon="document" title="ยังไม่มีไฟล์แนบ"
                                description="อัปโหลดเอกสารที่เกี่ยวข้องกับบุคลากรคนนี้" />
        @endforelse
    </x-data-table>
</section>
