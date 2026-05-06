<x-layouts.app title="แก้ไข Software License">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">แก้ไข Software License</h1>
        <p class="text-sm text-gray-600">
            {{ $softwareLicense->product?->name }} / {{ $softwareLicense->license_name }}
        </p>
    </div>

    <div class="rounded bg-white p-6 shadow">
        <form method="POST" action="{{ route('software-licenses.update', $softwareLicense) }}" class="space-y-4">
            @csrf
            @method('PUT')

            @include('software-licenses._form')

            <div class="flex gap-2">
                <button class="rounded bg-blue-600 px-4 py-2 text-white">บันทึกการแก้ไข</button>
                <a href="{{ route('software-licenses.index') }}"
                   class="rounded bg-gray-200 px-4 py-2 text-gray-700">ย้อนกลับ</a>
            </div>
        </form>
    </div>
    <div class="mt-6 rounded bg-white p-6 shadow">
    <h2 class="mb-4 text-lg font-bold">ประวัติการดำเนินการ</h2>

    <div class="overflow-hidden rounded border">
        <table class="w-full border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-4 py-2 text-left">วันที่</th>
                    <th class="border px-4 py-2 text-left">ผู้ทำรายการ</th>
                    <th class="border px-4 py-2 text-left">Action</th>
                    <th class="border px-4 py-2 text-left">วันหมดอายุเดิม</th>
                    <th class="border px-4 py-2 text-left">วันหมดอายุใหม่</th>
                    <th class="border px-4 py-2 text-left">หมายเหตุ</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($softwareLicense->actions()->with('user')->latest()->get() as $action)
                    <tr>
                        <td class="border px-4 py-2">
                            {{ $action->created_at->format('Y-m-d H:i') }}
                        </td>

                        <td class="border px-4 py-2">
                            {{ $action->user?->name ?? 'system' }}
                        </td>

                        <td class="border px-4 py-2">
                            @if ($action->action === 'renewed')
                                <span class="rounded bg-green-100 px-2 py-1 text-xs text-green-800">
                                    renewed
                                </span>
                            @elseif ($action->action === 'cancelled')
                                <span class="rounded bg-red-100 px-2 py-1 text-xs text-red-800">
                                    cancelled
                                </span>
                            @else
                                <span class="rounded bg-gray-100 px-2 py-1 text-xs text-gray-800">
                                    {{ $action->action }}
                                </span>
                            @endif
                        </td>

                        <td class="border px-4 py-2">
                            {{ $action->old_expire_date?->format('Y-m-d') ?? '-' }}
                        </td>

                        <td class="border px-4 py-2">
                            {{ $action->new_expire_date?->format('Y-m-d') ?? '-' }}
                        </td>

                        <td class="border px-4 py-2">
                            {{ $action->remark ?? '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="border px-4 py-6 text-center text-gray-500">
                            ยังไม่มีประวัติการดำเนินการ
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</x-layouts.app>
