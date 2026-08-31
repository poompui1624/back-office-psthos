<x-layouts.app title="รายการอนุมัติ">
    @php
        $approvalStatuses = ['pending' => 'รออนุมัติ', 'approved' => 'อนุมัติแล้ว', 'rejected' => 'ไม่อนุมัติ', 'cancelled' => 'ยกเลิก'];
        $approvalTones = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger', 'cancelled' => 'slate'];
    @endphp

    <x-page-header title="รายการอนุมัติ" subtitle="รายการที่รออนุมัติจากระบบต่าง ๆ" />

    <x-filter-bar :action="route('approvals.index')">
        <x-form.field label="ค้นหา" class="min-w-64 flex-1">
            <x-form.input name="search" :value="$search" placeholder="module / หัวข้อ / ผู้ขอ" />
        </x-form.field>

        <x-form.field label="สถานะ">
            <x-form.select name="status" class="w-44">
                <option value="">ทุกสถานะ</option>

                @foreach ($approvalStatuses as $value => $label)
                    <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                @endforeach
            </x-form.select>
        </x-form.field>
    </x-filter-bar>

    <div class="space-y-4">
        @forelse ($approvals as $approval)
            <div class="card card-pad">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                    <div class="min-w-0">
                        <div class="mb-2 flex flex-wrap items-center gap-2">
                            <x-badge tone="brand">{{ $approval->module }}</x-badge>

                            <x-badge :tone="$approvalTones[$approval->status] ?? 'slate'" dot>
                                {{ $approvalStatuses[$approval->status] ?? $approval->status }}
                            </x-badge>
                        </div>

                        <h2 class="text-lg font-bold text-slate-900">{{ $approval->title }}</h2>

                        @if ($approval->description)
                            <p class="mt-1 text-sm text-slate-600">{{ $approval->description }}</p>
                        @endif

                        <div class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-sm text-slate-500">
                            <span>ผู้ขอ: {{ $approval->requester?->name ?? '-' }}</span>
                            <span>ผู้อนุมัติ: {{ $approval->approver?->name ?? '-' }}</span>
                            <span>วันที่: {{ $approval->created_at->format('Y-m-d H:i') }}</span>
                        </div>
                    </div>

                    <div class="w-full shrink-0 lg:w-64">
                        @if ($approval->status === 'pending')
                            <div class="space-y-3">
                                @can('approval.approve')
                                    <form method="POST" action="{{ route('approvals.approve', $approval) }}">
                                        @csrf
                                        @method('PATCH')

                                        <x-form.textarea name="comment" rows="2" class="mb-2 text-sm"
                                                         placeholder="หมายเหตุการอนุมัติ" />

                                        <x-btn type="submit" variant="success" class="w-full justify-center">อนุมัติ</x-btn>
                                    </form>
                                @endcan

                                @can('approval.reject')
                                    <form method="POST" action="{{ route('approvals.reject', $approval) }}">
                                        @csrf
                                        @method('PATCH')

                                        <x-form.textarea name="comment" rows="2" class="mb-2 text-sm"
                                                         placeholder="เหตุผลที่ไม่อนุมัติ" />

                                        <x-btn type="submit" variant="danger" class="w-full justify-center">ไม่อนุมัติ</x-btn>
                                    </form>
                                @endcan
                            </div>
                        @else
                            <div class="rounded-xl bg-slate-50 p-3 text-sm text-slate-600">
                                สรุปผล: {{ $approval->remark ?: '-' }}
                            </div>
                        @endif
                    </div>
                </div>

                @if ($approval->data)
                    <details class="group mt-4">
                        <summary class="flex cursor-pointer items-center gap-1.5 text-sm font-medium text-slate-600 hover:text-slate-900">
                            <x-icon name="chevron-right" class="h-3.5 w-3.5 transition group-open:rotate-90" />
                            ดูข้อมูลเพิ่มเติม
                        </summary>

                        <pre class="mt-2 overflow-auto rounded-xl bg-slate-900 p-3 text-xs text-white">{{ json_encode($approval->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </details>
                @endif

                @if ($approval->actions->count())
                    <details class="group mt-4">
                        <summary class="flex cursor-pointer items-center gap-1.5 text-sm font-medium text-slate-600 hover:text-slate-900">
                            <x-icon name="chevron-right" class="h-3.5 w-3.5 transition group-open:rotate-90" />
                            ประวัติการดำเนินการ
                        </summary>

                        <div class="mt-2 space-y-2">
                            @foreach ($approval->actions as $action)
                                <div class="rounded-xl bg-slate-50 p-3 text-sm">
                                    <div class="text-slate-700">
                                        {{ $action->created_at->format('Y-m-d H:i') }} &middot;
                                        {{ $action->user?->name ?? 'system' }} &middot;
                                        {{ $action->action }}
                                    </div>

                                    @if ($action->comment)
                                        <div class="mt-1 text-slate-600">{{ $action->comment }}</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </details>
                @endif
            </div>
        @empty
            <div class="card">
                <x-empty-state icon="approvals" title="ไม่พบรายการอนุมัติ"
                               description="รายการที่รออนุมัติจากทุกโมดูลจะมาแสดงที่นี่" />
            </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $approvals->links() }}</div>
</x-layouts.app>
