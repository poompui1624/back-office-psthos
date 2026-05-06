<x-layouts.app title="ยกเลิก Software License">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">ยกเลิก Software License</h1>
        <p class="text-sm text-gray-600">
            {{ $softwareLicense->product?->name }}
            /
            {{ $softwareLicense->license_name ?? '-' }}
        </p>
    </div>

    <div class="mb-6 rounded bg-red-50 p-4 text-red-800">
        การยกเลิก License จะเปลี่ยนสถานะเป็น cancelled แต่ข้อมูลประวัติจะยังถูกเก็บไว้
    </div>

    <div class="rounded bg-white p-6 shadow">
        <form method="POST" action="{{ route('software-licenses.cancel', $softwareLicense) }}" class="space-y-4">
            @csrf

            <div>
                <label class="mb-1 block font-medium">
                    วันที่ยกเลิก <span class="text-red-600">*</span>
                </label>
                <input type="date"
                       name="cancelled_at"
                       value="{{ old('cancelled_at', now()->format('Y-m-d')) }}"
                       class="w-full rounded border-gray-300">
                @error('cancelled_at')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-1 block font-medium">
                    เหตุผลการยกเลิก <span class="text-red-600">*</span>
                </label>
                <textarea name="remark"
                          rows="4"
                          class="w-full rounded border-gray-300">{{ old('remark') }}</textarea>
                @error('remark')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-2">
                <button type="submit"
                        class="rounded bg-red-600 px-4 py-2 text-white hover:bg-red-700"
                        onclick="return confirm('ยืนยันการยกเลิก License นี้?')">
                    ยืนยันการยกเลิก
                </button>

                <a href="{{ route('software-licenses.index') }}"
                   class="rounded bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">
                    ย้อนกลับ
                </a>
            </div>
        </form>
    </div>
</x-layouts.app>
