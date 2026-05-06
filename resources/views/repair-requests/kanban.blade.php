<x-layouts.app title="Repair Kanban">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">Repair Kanban</h1>
            <p class="text-sm text-gray-600">
                บอร์ดติดตามสถานะงานแจ้งซ่อม
            </p>
        </div>

        <div class="flex gap-2">
            @can('repair.create')
                <a href="{{ route('repair-requests.create') }}"
                   class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                    แจ้งซ่อมใหม่
                </a>
            @endcan

            <a href="{{ route('repair-requests.index') }}"
               class="rounded bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">
                แบบตาราง
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded bg-green-100 px-4 py-3 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-4 rounded bg-white p-4 shadow">
        <form method="GET" action="{{ route('repair-requests.kanban') }}" class="flex gap-2">
            <input type="text"
                   name="search"
                   value="{{ $search }}"
                   placeholder="ค้นหาเลขงาน / หัวข้อ / ผู้แจ้ง / หน่วยงาน"
                   class="w-full rounded border-gray-300">

            <button type="submit"
                    class="rounded bg-gray-800 px-4 py-2 text-white hover:bg-gray-900">
                ค้นหา
            </button>

            <a href="{{ route('repair-requests.kanban') }}"
               class="rounded bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">
                ล้าง
            </a>
        </form>
    </div>

    @php
        $columnStyles = [
            'new' => [
                'header' => 'bg-blue-100 text-blue-900',
                'badge' => 'bg-blue-100 text-blue-800',
            ],
            'in_progress' => [
                'header' => 'bg-yellow-100 text-yellow-900',
                'badge' => 'bg-yellow-100 text-yellow-800',
            ],
            'completed' => [
                'header' => 'bg-green-100 text-green-900',
                'badge' => 'bg-green-100 text-green-800',
            ],
            'cancelled' => [
                'header' => 'bg-gray-100 text-gray-900',
                'badge' => 'bg-gray-100 text-gray-800',
            ],
        ];

        $priorityStyles = [
            'low' => 'bg-gray-100 text-gray-700',
            'normal' => 'bg-blue-100 text-blue-800',
            'high' => 'bg-orange-100 text-orange-800',
            'urgent' => 'bg-red-100 text-red-800',
        ];

        $priorityText = [
            'low' => 'ต่ำ',
            'normal' => 'ปกติ',
            'high' => 'สูง',
            'urgent' => 'ด่วนมาก',
        ];
    @endphp

    <div class="grid gap-4 xl:grid-cols-4 md:grid-cols-2">
        @foreach ($statuses as $statusKey => $statusLabel)
            @php
                $items = $repairRequests->get($statusKey, collect());
                $style = $columnStyles[$statusKey] ?? $columnStyles['new'];
            @endphp

            <div class="rounded-xl border border-gray-200 bg-gray-50">
                <div class="rounded-t-xl px-4 py-3 {{ $style['header'] }}">
                    <div class="flex items-center justify-between">
                        <div class="font-bold">
                            {{ $statusLabel }}
                        </div>

                        <div class="rounded-full bg-white px-2 py-1 text-xs text-gray-700">
                            {{ $items->count() }}
                        </div>
                    </div>
                </div>

                <div class="space-y-3 p-3">
                    @forelse ($items as $repairRequest)
                        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                            <div class="mb-2 flex items-start justify-between gap-2">
                                <div>
                                    <a href="{{ route('repair-requests.show', $repairRequest) }}"
                                       class="font-bold text-gray-900 hover:text-blue-600">
                                        {{ $repairRequest->ticket_no }}
                                    </a>

                                    <div class="mt-1 text-sm font-medium text-gray-800">
                                        {{ $repairRequest->title }}
                                    </div>
                                </div>

                                <span class="whitespace-nowrap rounded px-2 py-1 text-xs {{ $priorityStyles[$repairRequest->priority] ?? $priorityStyles['normal'] }}">
                                    {{ $priorityText[$repairRequest->priority] ?? $repairRequest->priority }}
                                </span>
                            </div>

                            <div class="space-y-1 text-sm text-gray-600">
                                <div>
                                    ผู้แจ้ง:
                                    {{ $repairRequest->requesterEmployee?->full_name ?? $repairRequest->requester?->name ?? '-' }}
                                </div>

                                <div>
                                    หน่วยงาน:
                                    {{ $repairRequest->department?->name ?? '-' }}
                                </div>

                                <div>
                                    สถานที่:
                                    {{ $repairRequest->location ?? '-' }}
                                </div>

                                <div>
                                    ผู้รับผิดชอบ:
                                    {{ $repairRequest->assignedUser?->name ?? '-' }}
                                </div>

                                <div>
                                    วันที่:
                                    {{ $repairRequest->created_at->format('Y-m-d H:i') }}
                                </div>
                            </div>

                            <div class="mt-3 flex flex-wrap gap-2">
                                <a href="{{ route('repair-requests.show', $repairRequest) }}"
                                   class="rounded bg-gray-800 px-3 py-1 text-xs text-white hover:bg-gray-900">
                                    รายละเอียด
                                </a>

                                @can('repair.update')
                                    @if ($repairRequest->status === 'new')
                                        <form method="POST" action="{{ route('repair-requests.update-status', $repairRequest) }}">
                                            @csrf
                                            @method('PATCH')

                                            <input type="hidden" name="status" value="in_progress">
                                            <input type="hidden" name="note" value="รับงานเข้าดำเนินการ">

                                            <button type="submit"
                                                    class="rounded bg-yellow-600 px-3 py-1 text-xs text-white hover:bg-yellow-700">
                                                รับงาน
                                            </button>
                                        </form>
                                    @endif

                                    @if ($repairRequest->status === 'in_progress')
                                        <form method="POST" action="{{ route('repair-requests.update-status', $repairRequest) }}">
                                            @csrf
                                            @method('PATCH')

                                            <input type="hidden" name="status" value="completed">
                                            <input type="hidden" name="note" value="ปิดงานซ่อมจาก Kanban">

                                            <button type="submit"
                                                    class="rounded bg-green-600 px-3 py-1 text-xs text-white hover:bg-green-700"
                                                    onclick="return confirm('ยืนยันว่าดำเนินการเสร็จแล้ว?')">
                                                เสร็จแล้ว
                                            </button>
                                        </form>
                                    @endif

                                    @if (! in_array($repairRequest->status, ['completed', 'cancelled']))
                                        <form method="POST" action="{{ route('repair-requests.update-status', $repairRequest) }}">
                                            @csrf
                                            @method('PATCH')

                                            <input type="hidden" name="status" value="cancelled">
                                            <input type="hidden" name="note" value="ยกเลิกงานซ่อมจาก Kanban">

                                            <button type="submit"
                                                    class="rounded bg-red-600 px-3 py-1 text-xs text-white hover:bg-red-700"
                                                    onclick="return confirm('ยืนยันการยกเลิกงานซ่อมนี้?')">
                                                ยกเลิก
                                            </button>
                                        </form>
                                    @endif

                                    @if ($repairRequest->status === 'completed')
                                        <form method="POST" action="{{ route('repair-requests.update-status', $repairRequest) }}">
                                            @csrf
                                            @method('PATCH')

                                            <input type="hidden" name="status" value="in_progress">
                                            <input type="hidden" name="note" value="เปิดงานกลับมาดำเนินการอีกครั้ง">

                                            <button type="submit"
                                                    class="rounded bg-yellow-600 px-3 py-1 text-xs text-white hover:bg-yellow-700">
                                                เปิดงานอีกครั้ง
                                            </button>
                                        </form>
                                    @endif
                                @endcan
                            </div>
                        </div>
                    @empty
                        <div class="rounded-lg border border-dashed border-gray-300 p-6 text-center text-sm text-gray-500">
                            ไม่มีรายการ
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</x-layouts.app>
