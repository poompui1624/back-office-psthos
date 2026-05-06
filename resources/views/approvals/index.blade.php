<x-layouts.app title="รายการอนุมัติ">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">รายการอนุมัติ</h1>
        <p class="text-sm text-gray-600">รายการที่รออนุมัติจากระบบต่าง ๆ</p>
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

    <div class="mb-4 rounded bg-white p-4 shadow">
        <form method="GET" action="{{ route('approvals.index') }}" class="grid gap-3 md:grid-cols-4">
            <input type="text"
                   name="search"
                   value="{{ $search }}"
                   placeholder="ค้นหา module / หัวข้อ / ผู้ขอ"
                   class="rounded border-gray-300 md:col-span-2">

            <select name="status" class="rounded border-gray-300">
                <option value="">ทุกสถานะ</option>
                <option value="pending" @selected($status === 'pending')>รออนุมัติ</option>
                <option value="approved" @selected($status === 'approved')>อนุมัติแล้ว</option>
                <option value="rejected" @selected($status === 'rejected')>ไม่อนุมัติ</option>
                <option value="cancelled" @selected($status === 'cancelled')>ยกเลิก</option>
            </select>

            <div class="flex gap-2">
                <button type="submit"
                        class="rounded bg-gray-800 px-4 py-2 text-white hover:bg-gray-900">
                    ค้นหา
                </button>

                <a href="{{ route('approvals.index') }}"
                   class="rounded bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">
                    ล้าง
                </a>
            </div>
        </form>
    </div>

    <div class="space-y-4">
        @forelse ($approvals as $approval)
            <div class="rounded bg-white p-5 shadow">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="mb-2 flex items-center gap-2">
                            <span class="rounded bg-blue-100 px-2 py-1 text-xs text-blue-800">
                                {{ $approval->module }}
                            </span>

                            @if ($approval->status === 'pending')
                                <span class="rounded bg-yellow-100 px-2 py-1 text-xs text-yellow-800">
                                    รออนุมัติ
                                </span>
                            @elseif ($approval->status === 'approved')
                                <span class="rounded bg-green-100 px-2 py-1 text-xs text-green-800">
                                    อนุมัติแล้ว
                                </span>
                            @elseif ($approval->status === 'rejected')
                                <span class="rounded bg-red-100 px-2 py-1 text-xs text-red-800">
                                    ไม่อนุมัติ
                                </span>
                            @else
                                <span class="rounded bg-gray-100 px-2 py-1 text-xs text-gray-800">
                                    {{ $approval->status }}
                                </span>
                            @endif
                        </div>

                        <h2 class="text-lg font-bold">
                            {{ $approval->title }}
                        </h2>

                        @if ($approval->description)
                            <p class="mt-1 text-sm text-gray-600">
                                {{ $approval->description }}
                            </p>
                        @endif

                        <div class="mt-3 text-sm text-gray-500">
                            ผู้ขอ: {{ $approval->requester?->name ?? '-' }}
                            |
                            ผู้อนุมัติ: {{ $approval->approver?->name ?? '-' }}
                            |
                            วันที่: {{ $approval->created_at->format('Y-m-d H:i') }}
                        </div>
                    </div>

                    <div class="min-w-64">
                        @if ($approval->status === 'pending')
                            <div class="space-y-2">
                                @can('approval.approve')
                                    <form method="POST" action="{{ route('approvals.approve', $approval) }}">
                                        @csrf
                                        @method('PATCH')

                                        <textarea name="comment"
                                                  rows="2"
                                                  placeholder="หมายเหตุการอนุมัติ"
                                                  class="mb-2 w-full rounded border-gray-300 text-sm"></textarea>

                                        <button type="submit"
                                                class="w-full rounded bg-green-600 px-3 py-2 text-sm text-white hover:bg-green-700">
                                            อนุมัติ
                                        </button>
                                    </form>
                                @endcan

                                @can('approval.reject')
                                    <form method="POST" action="{{ route('approvals.reject', $approval) }}">
                                        @csrf
                                        @method('PATCH')

                                        <textarea name="comment"
                                                  rows="2"
                                                  placeholder="เหตุผลที่ไม่อนุมัติ"
                                                  class="mb-2 w-full rounded border-gray-300 text-sm"></textarea>

                                        <button type="submit"
                                                class="w-full rounded bg-red-600 px-3 py-2 text-sm text-white hover:bg-red-700">
                                            ไม่อนุมัติ
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        @else
                            <div class="rounded bg-gray-50 p-3 text-sm text-gray-600">
                                สรุปผล: {{ $approval->remark ?: '-' }}
                            </div>
                        @endif
                    </div>
                </div>

                @if ($approval->data)
                    <details class="mt-4">
                        <summary class="cursor-pointer text-sm font-medium text-gray-700">
                            ดูข้อมูลเพิ่มเติม
                        </summary>

                        <pre class="mt-2 overflow-auto rounded bg-gray-900 p-3 text-xs text-white">{{ json_encode($approval->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </details>
                @endif

                @if ($approval->actions->count())
                    <details class="mt-4">
                        <summary class="cursor-pointer text-sm font-medium text-gray-700">
                            ประวัติการดำเนินการ
                        </summary>

                        <div class="mt-2 space-y-2">
                            @foreach ($approval->actions as $action)
                                <div class="rounded bg-gray-50 p-3 text-sm">
                                    <div>
                                        {{ $action->created_at->format('Y-m-d H:i') }}
                                        -
                                        {{ $action->user?->name ?? 'system' }}
                                        -
                                        {{ $action->action }}
                                    </div>

                                    @if ($action->comment)
                                        <div class="mt-1 text-gray-600">
                                            {{ $action->comment }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </details>
                @endif
            </div>
        @empty
            <div class="rounded bg-white p-8 text-center text-gray-500 shadow">
                ไม่พบรายการอนุมัติ
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $approvals->links() }}
    </div>
</x-layouts.app>
