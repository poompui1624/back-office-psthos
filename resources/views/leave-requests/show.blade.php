<x-layouts.app title="รายละเอียดคำขอลา">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">
                {{ $leaveRequest->request_no }}
            </h1>
            <p class="text-sm text-gray-600">
                {{ $leaveRequest->employee?->full_name }} / {{ $leaveRequest->leaveType?->name }}
            </p>
        </div>

        <div class="flex gap-2">
            @if ($leaveRequest->isPending())
                @can('leave.update')
                    <a href="{{ route('leave-requests.edit', $leaveRequest) }}"
                       class="rounded bg-yellow-500 px-4 py-2 text-white hover:bg-yellow-600">
                        แก้ไข
                    </a>
                @endcan
            @endif

            <a href="{{ route('leave-requests.index') }}"
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

    @if (session('error'))
        <div class="mb-4 rounded bg-red-100 px-4 py-3 text-red-800">
            {{ session('error') }}
        </div>
    @endif

    <div class="mb-6 grid gap-4 md:grid-cols-4">
        <div class="rounded bg-white p-5 shadow">
            <div class="text-sm text-gray-500">สถานะ</div>
            <div class="mt-2 text-xl font-bold">
                {{ $leaveRequest->status }}
            </div>
        </div>

        <div class="rounded bg-white p-5 shadow">
            <div class="text-sm text-gray-500">จำนวนวัน</div>
            <div class="mt-2 text-xl font-bold">
                {{ $leaveRequest->total_days }}
            </div>
        </div>

        <div class="rounded bg-white p-5 shadow">
            <div class="text-sm text-gray-500">วันที่เริ่ม</div>
            <div class="mt-2 text-xl font-bold">
                {{ $leaveRequest->start_date?->format('Y-m-d') }}
            </div>
        </div>

        <div class="rounded bg-white p-5 shadow">
            <div class="text-sm text-gray-500">วันที่สิ้นสุด</div>
            <div class="mt-2 text-xl font-bold">
                {{ $leaveRequest->end_date?->format('Y-m-d') }}
            </div>
        </div>
    </div>

    <div class="mb-6 rounded bg-white p-6 shadow">
        <h2 class="mb-4 text-lg font-bold">รายละเอียดคำขอ</h2>

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <div class="text-sm text-gray-500">บุคลากร</div>
                <div class="font-medium">{{ $leaveRequest->employee?->full_name ?? '-' }}</div>
            </div>

            <div>
                <div class="text-sm text-gray-500">หน่วยงาน</div>
                <div class="font-medium">{{ $leaveRequest->department?->name ?? '-' }}</div>
            </div>

            <div>
                <div class="text-sm text-gray-500">ประเภทการลา</div>
                <div class="font-medium">{{ $leaveRequest->leaveType?->name ?? '-' }}</div>
            </div>

            <div>
                <div class="text-sm text-gray-500">ช่วงเวลา</div>
                <div class="font-medium">
                    {{ $leaveRequest->start_period }} ถึง {{ $leaveRequest->end_period }}
                </div>
            </div>

            <div class="md:col-span-2">
                <div class="text-sm text-gray-500">เหตุผล</div>
                <div class="whitespace-pre-line font-medium">
                    {{ $leaveRequest->reason ?? '-' }}
                </div>
            </div>

            <div class="md:col-span-2">
                <div class="text-sm text-gray-500">ติดต่อระหว่างลา</div>
                <div class="font-medium">
                    {{ $leaveRequest->contact_during_leave ?? '-' }}
                </div>
            </div>
        </div>
    </div>

    <div class="mb-6 rounded bg-white p-6 shadow">
        <h2 class="mb-4 text-lg font-bold">ผลการอนุมัติ</h2>

        <div class="grid gap-4 md:grid-cols-3">
            <div>
                <div class="text-sm text-gray-500">ผู้อนุมัติ</div>
                <div class="font-medium">{{ $leaveRequest->approver?->name ?? '-' }}</div>
            </div>

            <div>
                <div class="text-sm text-gray-500">วันที่อนุมัติ</div>
                <div class="font-medium">{{ $leaveRequest->approved_at?->format('Y-m-d H:i') ?? '-' }}</div>
            </div>

            <div>
                <div class="text-sm text-gray-500">ผู้ไม่อนุมัติ</div>
                <div class="font-medium">{{ $leaveRequest->rejecter?->name ?? '-' }}</div>
            </div>

            <div class="md:col-span-3">
                <div class="text-sm text-gray-500">หมายเหตุ</div>
                <div class="whitespace-pre-line font-medium">
                    {{ $leaveRequest->approval_remark ?? '-' }}
                </div>
            </div>
        </div>
    </div>

    @if ($leaveRequest->isPending())
        @can('leave.approve')
            <div class="mb-6 grid gap-4 md:grid-cols-2">
                <div class="rounded bg-white p-6 shadow">
                    <h2 class="mb-4 text-lg font-bold text-green-700">อนุมัติคำขอ</h2>

                    <form method="POST" action="{{ route('leave-requests.approve', $leaveRequest) }}" class="space-y-3">
                        @csrf
                        @method('PATCH')

                        <textarea name="approval_remark"
                                  rows="3"
                                  class="w-full rounded border-gray-300"
                                  placeholder="หมายเหตุการอนุมัติ">{{ old('approval_remark') }}</textarea>

                        <button type="submit"
                                class="rounded bg-green-600 px-4 py-2 text-white hover:bg-green-700">
                            อนุมัติ
                        </button>
                    </form>
                </div>

                <div class="rounded bg-white p-6 shadow">
                    <h2 class="mb-4 text-lg font-bold text-red-700">ไม่อนุมัติคำขอ</h2>

                    <form method="POST" action="{{ route('leave-requests.reject', $leaveRequest) }}" class="space-y-3">
                        @csrf
                        @method('PATCH')

                        <textarea name="approval_remark"
                                  rows="3"
                                  class="w-full rounded border-gray-300"
                                  placeholder="เหตุผลที่ไม่อนุมัติ">{{ old('approval_remark') }}</textarea>

                        <button type="submit"
                                class="rounded bg-red-600 px-4 py-2 text-white hover:bg-red-700"
                                onclick="return confirm('ยืนยันการไม่อนุมัติคำขอนี้?')">
                            ไม่อนุมัติ
                        </button>
                    </form>
                </div>
            </div>
        @endcan
    @endif

    @if (in_array($leaveRequest->status, ['pending', 'approved']))
        @can('leave.update')
            <div class="mb-6 rounded bg-white p-6 shadow">
                <h2 class="mb-4 text-lg font-bold">ยกเลิกคำขอ</h2>

                <form method="POST" action="{{ route('leave-requests.cancel', $leaveRequest) }}" class="space-y-3">
                    @csrf
                    @method('PATCH')

                    <textarea name="approval_remark"
                              rows="3"
                              class="w-full rounded border-gray-300"
                              placeholder="เหตุผลการยกเลิก">{{ old('approval_remark') }}</textarea>

                    <button type="submit"
                            class="rounded bg-gray-700 px-4 py-2 text-white hover:bg-gray-800"
                            onclick="return confirm('ยืนยันการยกเลิกคำขอนี้?')">
                        ยกเลิกคำขอ
                    </button>
                </form>
            </div>
        @endcan
    @endif

    <div class="mb-6 rounded bg-white p-6 shadow">
        <h2 class="mb-4 text-lg font-bold">ไฟล์แนบ</h2>

        @can('attachment.upload')
            <form method="POST"
                  action="{{ route('attachments.store') }}"
                  enctype="multipart/form-data"
                  class="mb-4 rounded border border-dashed border-gray-300 p-4">
                @csrf

                <input type="hidden" name="module" value="leave">
                <input type="hidden" name="attachable_type" value="leave_request">
                <input type="hidden" name="attachable_id" value="{{ $leaveRequest->id }}">

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
                    @forelse ($leaveRequest->attachments as $attachment)
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
            @forelse ($leaveRequest->actions->sortByDesc('created_at') as $action)
                <div class="rounded border border-gray-200 p-4">
                    <div class="flex justify-between">
                        <div class="font-medium">
                            {{ $action->action }}

                            @if ($action->old_status || $action->new_status)
                                <span class="text-sm text-gray-500">
                                    {{ $action->old_status ?? '-' }} → {{ $action->new_status ?? '-' }}
                                </span>
                            @endif
                        </div>

                        <div class="text-sm text-gray-500">
                            {{ $action->created_at->format('Y-m-d H:i') }}
                        </div>
                    </div>

                    <div class="mt-1 text-sm text-gray-600">
                        โดย {{ $action->user?->name ?? 'system' }}
                    </div>

                    @if ($action->remark)
                        <div class="mt-2 text-sm text-gray-700">
                            {{ $action->remark }}
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
