<x-layouts.app title="แจ้งเตือน">
    <x-page-header title="แจ้งเตือน" subtitle="รายการแจ้งเตือนของคุณ">
        <form method="POST" action="{{ route('notifications.read-all') }}">
            @csrf
            @method('PATCH')

            <x-btn type="submit" variant="secondary" icon="approvals">อ่านทั้งหมด</x-btn>
        </form>
    </x-page-header>

    <div class="mb-4 flex gap-2">
        @foreach (['' => 'ทั้งหมด', 'unread' => 'ยังไม่อ่าน'] as $value => $label)
            <a href="{{ $value === '' ? route('notifications.index') : route('notifications.index', ['filter' => $value]) }}"
               class="rounded-xl px-4 py-2 text-sm font-semibold transition {{ $filter === $value ? 'bg-brand-500 text-white shadow-sm shadow-brand-500/25' : 'bg-white text-slate-700 shadow-sm hover:bg-slate-50' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="space-y-3">
        @forelse ($notifications as $notification)
            <div class="card p-4 {{ $notification->read_at ? 'opacity-70' : 'border-l-4 border-l-brand-500' }}">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            @unless ($notification->read_at)
                                <x-badge tone="danger">ใหม่</x-badge>
                            @endunless

                            <x-badge tone="brand">{{ $notification->type }}</x-badge>

                            <h2 class="font-bold text-slate-900">{{ $notification->title }}</h2>
                        </div>

                        @if ($notification->message)
                            <p class="mt-2 text-sm text-slate-600">{{ $notification->message }}</p>
                        @endif

                        <div class="mt-2 text-xs text-slate-400">
                            {{ $notification->created_at->format('Y-m-d H:i') }}
                        </div>
                    </div>

                    <div class="flex shrink-0 gap-2">
                        <form method="POST" action="{{ route('notifications.read', $notification) }}">
                            @csrf
                            @method('PATCH')

                            <x-btn type="submit" variant="secondary" size="sm">เปิด</x-btn>
                        </form>

                        <form method="POST"
                              action="{{ route('notifications.destroy', $notification) }}"
                              onsubmit="return confirm('ยืนยันการลบแจ้งเตือนนี้?')">
                            @csrf
                            @method('DELETE')

                            <x-btn type="submit" variant="danger" size="sm">ลบ</x-btn>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="card">
                <x-empty-state icon="bell" title="ไม่มีแจ้งเตือน"
                               description="เมื่อมีคำขอหรือการอนุมัติที่เกี่ยวข้องกับคุณ จะแจ้งที่นี่" />
            </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $notifications->links() }}</div>
</x-layouts.app>
