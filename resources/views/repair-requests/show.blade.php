<x-layouts.app title="รายละเอียดแจ้งซ่อม">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">
                {{ $repairRequest->ticket_no }}
            </h1>
            <p class="text-sm text-gray-600">
                {{ $repairRequest->title }}
            </p>
        </div>

        <div class="flex gap-2">
            @can('repair.update')
                <a href="{{ route('repair-requests.edit', $repairRequest) }}"
                   class="rounded bg-yellow-500 px-4 py-2 text-white hover:bg-yellow-600">
                    แก้ไข
                </a>
            @endcan

            <a href="{{ route('repair-requests.index') }}"
               class="rounded bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">
                ย้อนกลับ
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded bg-green-100 px-4 py-3 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-6 grid gap-4 md:grid-cols-4">
        <div class="rounded bg-white p-5 shadow">
            <div class="text-sm text-gray-500">สถานะ</div>
            <div class="mt-2 text-xl font-bold">{{ $repairRequest->status }}</div>
        </div>

        <div class="rounded bg-white p-5 shadow">
            <div class="text-sm text-gray-500">ความเร่งด่วน</div>
            <div class="mt-2 text-xl font-bold">{{ $repairRequest->priority }}</div>
        </div>

        <div class="rounded bg-white p-5 shadow">
            <div class="text-sm text-gray-500">ผู้รับผิดชอบ</div>
            <div class="mt-2 text-xl font-bold">{{ $repairRequest->assignedUser?->name ?? '-' }}</div>
        </div>

        <div class="rounded bg-white p-5 shadow">
            <div class="text-sm text-gray-500">วันที่แจ้ง</div>
            <div class="mt-2 text-xl font-bold">{{ $repairRequest->created_at->format('Y-m-d') }}</div>
        </div>
    </div>

    <div class="mb-6 rounded bg-white p-6 shadow">
        <h2 class="mb-4 text-lg font-bold">รายละเอียดงานซ่อม</h2>

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <div class="text-sm text-gray-500">หัวข้อ</div>
                <div class="font-medium">{{ $repairRequest->title }}</div>
            </div>

            <div>
                <div class="text-sm text-gray-500">หมวดหมู่</div>
                <div class="font-medium">{{ $repairRequest->category }}</div>
            </div>

            <div>
                <div class="text-sm text-gray-500">ผู้แจ้ง</div>
                <div class="font-medium">
                    {{ $repairRequest->requesterEmployee?->full_name ?? $repairRequest->requester?->name ?? '-' }}
                </div>
            </div>

            <div>
                <div class="text-sm text-gray-500">หน่วยงาน</div>
                <div class="font-medium">{{ $repairRequest->department?->name ?? '-' }}</div>
            </div>

            <div>
                <div class="text-sm text-gray-500">สถานที่</div>
                <div class="font-medium">{{ $repairRequest->location ?? '-' }}</div>
            </div>

            <div>
                <div class="text-sm text-gray-500">รายการที่เกี่ยวข้อง</div>
                <div class="font-medium">
                    @if ($repairRequest->repairable)
                        {{ class_basename($repairRequest->repairable_type) }}
                        #{{ $repairRequest->repairable_id }}
                    @else
                        -
                    @endif
                </div>
            </div>

            <div class="md:col-span-2">
                <div class="text-sm text-gray-500">รายละเอียดอาการ</div>
                <div class="whitespace-pre-line font-medium">
                    {{ $repairRequest->description ?? '-' }}
                </div>
            </div>

            <div class="md:col-span-2">
                <div class="text-sm text-gray-500">วิธีแก้ไข / ผลการดำเนินการ</div>
                <div class="whitespace-pre-line font-medium">
                    {{ $repairRequest->solution ?? '-' }}
                </div>
            </div>
        </div>
    </div>

    @can('repair.update')
        <div class="mb-6 rounded bg-white p-6 shadow">
            <h2 class="mb-4 text-lg font-bold">อัปเดตสถานะ</h2>

            <form method="POST" action="{{ route('repair-requests.update-status', $repairRequest) }}" class="space-y-4">
                @csrf
                @method('PATCH')

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block font-medium">สถานะใหม่</label>
                        <select name="status" class="w-full rounded border-gray-300">
                            <option value="new" @selected($repairRequest->status === 'new')>ใหม่</option>
                            <option value="in_progress" @selected($repairRequest->status === 'in_progress')>กำลังดำเนินการ</option>
                            <option value="completed" @selected($repairRequest->status === 'completed')>เสร็จแล้ว</option>
                            <option value="cancelled" @selected($repairRequest->status === 'cancelled')>ยกเลิก</option>
                        </select>
                    </div>

                    <div>
                        <label class="mb-1 block font-medium">หมายเหตุ</label>
                        <input type="text"
                               name="note"
                               class="w-full rounded border-gray-300"
                               placeholder="เช่น รับงานแล้ว, รออะไหล่, แก้ไขเรียบร้อย">
                    </div>
                </div>

                <div>
                    <label class="mb-1 block font-medium">วิธีแก้ไข / Solution</label>
                    <textarea name="solution"
                              rows="3"
                              class="w-full rounded border-gray-300">{{ old('solution', $repairRequest->solution) }}</textarea>
                </div>

                <button type="submit"
                        class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                    บันทึกสถานะ
                </button>
            </form>
        </div>
    @endcan

    <div class="mb-6 rounded bg-white p-6 shadow">
        <h2 class="mb-4 text-lg font-bold">ไฟล์แนบ</h2>

        @can('attachment.upload')
            <form method="POST"
                  action="{{ route('attachments.store') }}"
                  enctype="multipart/form-data"
                  class="mb-4 rounded border border-dashed border-gray-300 p-4">
                @csrf

                <input type="hidden" name="module" value="repair">
                <input type="hidden" name="attachable_type" value="repair_request">
                <input type="hidden" name="attachable_id" value="{{ $repairRequest->id }}">

                <div class="flex gap-3">
                    <input type="file" name="file" class="w-full rounded border-gray-300">

                    <button type="submit"
                            class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                        อัปโหลด
                    </button>
                </div>
            </form>
        @endcan

        <div class="overflow-hidden rounded border">
            <table class="w-full border-collapse">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-4 py-2 text-left">ไฟล์</th>
                        <th class="border px-4 py-2 text-left">ขนาด</th>
                        <th class="border px-4 py-2 text-left">ผู้อัปโหลด</th>
                        <th class="border px-4 py-2 text-center">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($repairRequest->attachments as $attachment)
                        <tr>
                            <td class="border px-4 py-2">{{ $attachment->original_name }}</td>
                            <td class="border px-4 py-2">{{ $attachment->file_size_text }}</td>
                            <td class="border px-4 py-2">{{ $attachment->uploader?->name ?? '-' }}</td>
                            <td class="border px-4 py-2 text-center">
                                <div class="flex justify-center gap-2">
                                    @can('attachment.download')
                                        <a href="{{ route('attachments.download', $attachment) }}"
                                           class="rounded bg-gray-800 px-3 py-1 text-sm text-white">
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
                                                    class="rounded bg-red-600 px-3 py-1 text-sm text-white">
                                                ลบ
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="border px-4 py-6 text-center text-gray-500">
                                ยังไม่มีไฟล์แนบ
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="rounded bg-white p-6 shadow">
        <h2 class="mb-4 text-lg font-bold">ประวัติการดำเนินการ</h2>

        <div class="space-y-3">
            @forelse ($repairRequest->updates->sortByDesc('created_at') as $update)
                <div class="rounded border border-gray-200 p-4">
                    <div class="flex justify-between">
                        <div class="font-medium">
                            {{ $update->action }}
                            @if ($update->old_status || $update->new_status)
                                <span class="text-sm text-gray-500">
                                    {{ $update->old_status ?? '-' }} → {{ $update->new_status ?? '-' }}
                                </span>
                            @endif
                        </div>

                        <div class="text-sm text-gray-500">
                            {{ $update->created_at->format('Y-m-d H:i') }}
                        </div>
                    </div>

                    <div class="mt-1 text-sm text-gray-600">
                        โดย {{ $update->user?->name ?? 'system' }}
                    </div>

                    @if ($update->note)
                        <div class="mt-2 text-sm text-gray-700">
                            {{ $update->note }}
                        </div>
                    @endif
                </div>
            @empty
                <div class="rounded border border-dashed border-gray-300 p-6 text-center text-gray-500">
                    ยังไม่มีประวัติการดำเนินการ
                </div>
            @endforelse
        </div>
    </div>
</x-layouts.app>
