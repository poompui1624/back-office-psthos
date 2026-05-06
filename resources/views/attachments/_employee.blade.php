<div class="mt-6 rounded bg-white p-6 shadow">
    <div class="mb-4">
        <h2 class="text-lg font-bold">ไฟล์แนบ</h2>
        <p class="text-sm text-gray-600">เอกสารที่เกี่ยวข้องกับบุคลากรนี้</p>
    </div>

    @can('attachment.upload')
        <form method="POST"
              action="{{ route('attachments.store') }}"
              enctype="multipart/form-data"
              class="mb-6 rounded border border-dashed border-gray-300 p-4">
            @csrf

            <input type="hidden" name="module" value="employee">
            <input type="hidden" name="attachable_type" value="employee">
            <input type="hidden" name="attachable_id" value="{{ $employee->id }}">

            <div class="flex items-center gap-3">
                <input type="file"
                       name="file"
                       class="w-full rounded border-gray-300">

                <button type="submit"
                        class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                    อัปโหลด
                </button>
            </div>

            @error('file')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror

            <p class="mt-2 text-xs text-gray-500">
                รองรับไฟล์: PDF, JPG, PNG, DOC, DOCX, XLS, XLSX ขนาดไม่เกิน 10MB
            </p>
        </form>
    @endcan

    <div class="overflow-hidden rounded border">
        <table class="w-full border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-4 py-2 text-left">ชื่อไฟล์</th>
                    <th class="border px-4 py-2 text-left">ขนาด</th>
                    <th class="border px-4 py-2 text-left">ผู้อัปโหลด</th>
                    <th class="border px-4 py-2 text-left">วันที่</th>
                    <th class="border px-4 py-2 text-center">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($attachments as $attachment)
                    <tr>
                        <td class="border px-4 py-2">
                            {{ $attachment->original_name }}
                            <div class="text-xs text-gray-500">
                                {{ $attachment->mime_type }}
                            </div>
                        </td>

                        <td class="border px-4 py-2">
                            {{ $attachment->file_size_text }}
                        </td>

                        <td class="border px-4 py-2">
                            {{ $attachment->uploader?->name ?? '-' }}
                        </td>

                        <td class="border px-4 py-2">
                            {{ $attachment->created_at->format('Y-m-d H:i') }}
                        </td>

                        <td class="border px-4 py-2 text-center">
                            <div class="flex justify-center gap-2">
                                @can('attachment.download')
                                    <a href="{{ route('attachments.download', $attachment) }}"
                                       class="rounded bg-gray-800 px-3 py-1 text-sm text-white hover:bg-gray-900">
                                        ดาวน์โหลด
                                    </a>
                                @endcan

                                @can('attachment.delete')
                                    <form method="POST"
                                          action="{{ route('attachments.destroy', $attachment) }}"
                                          onsubmit="return confirm('ยืนยันการลบไฟล์นี้?')">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="rounded bg-red-600 px-3 py-1 text-sm text-white hover:bg-red-700">
                                            ลบ
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="border px-4 py-6 text-center text-gray-500">
                            ยังไม่มีไฟล์แนบ
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
