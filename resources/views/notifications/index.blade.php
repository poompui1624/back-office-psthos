<x-layouts.app title="แจ้งเตือน">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">แจ้งเตือน</h1>
            <p class="text-sm text-gray-600">รายการแจ้งเตือนของคุณ</p>
        </div>

        <form method="POST" action="{{ route('notifications.read-all') }}">
            @csrf
            @method('PATCH')

            <button type="submit"
                    class="rounded bg-gray-800 px-4 py-2 text-white hover:bg-gray-900">
                อ่านทั้งหมด
            </button>
        </form>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded bg-green-100 px-4 py-3 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-4 flex gap-2">
        <a href="{{ route('notifications.index') }}"
           class="rounded px-4 py-2 {{ $filter === '' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700' }}">
            ทั้งหมด
        </a>

        <a href="{{ route('notifications.index', ['filter' => 'unread']) }}"
           class="rounded px-4 py-2 {{ $filter === 'unread' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700' }}">
            ยังไม่อ่าน
        </a>
    </div>

    <div class="space-y-3">
        @forelse ($notifications as $notification)
            <div class="rounded bg-white p-4 shadow {{ $notification->read_at ? 'opacity-70' : '' }}">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            @if (! $notification->read_at)
                                <span class="rounded bg-red-100 px-2 py-1 text-xs text-red-700">
                                    ใหม่
                                </span>
                            @endif

                            <span class="rounded bg-blue-100 px-2 py-1 text-xs text-blue-700">
                                {{ $notification->type }}
                            </span>

                            <h2 class="font-bold">
                                {{ $notification->title }}
                            </h2>
                        </div>

                        @if ($notification->message)
                            <p class="mt-2 text-sm text-gray-600">
                                {{ $notification->message }}
                            </p>
                        @endif

                        <div class="mt-2 text-xs text-gray-400">
                            {{ $notification->created_at->format('Y-m-d H:i') }}
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <form method="POST" action="{{ route('notifications.read', $notification) }}">
                            @csrf
                            @method('PATCH')

                            <button type="submit"
                                    class="rounded bg-gray-800 px-3 py-1 text-sm text-white hover:bg-gray-900">
                                เปิด
                            </button>
                        </form>

                        <form method="POST"
                              action="{{ route('notifications.destroy', $notification) }}"
                              onsubmit="return confirm('ยืนยันการลบแจ้งเตือนนี้?')">
                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                    class="rounded bg-red-600 px-3 py-1 text-sm text-white hover:bg-red-700">
                                ลบ
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded bg-white p-8 text-center text-gray-500 shadow">
                ไม่มีแจ้งเตือน
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $notifications->links() }}
    </div>
</x-layouts.app>
