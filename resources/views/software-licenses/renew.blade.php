<x-layouts.app title="ต่ออายุ Software License">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">ต่ออายุ Software License</h1>
        <p class="text-sm text-gray-600">
            {{ $softwareLicense->product?->name }}
            /
            {{ $softwareLicense->license_name ?? '-' }}
        </p>
    </div>

    <div class="mb-6 rounded bg-white p-6 shadow">
        <h2 class="mb-4 text-lg font-bold">ข้อมูลปัจจุบัน</h2>

        <div class="grid gap-4 md:grid-cols-4">
            <div>
                <div class="text-sm text-gray-500">วันหมดอายุเดิม</div>
                <div class="font-medium">
                    {{ $softwareLicense->expire_date?->format('Y-m-d') ?? '-' }}
                </div>
            </div>

            <div>
                <div class="text-sm text-gray-500">จำนวนสิทธิ์</div>
                <div class="font-medium">
                    {{ $softwareLicense->used_seats }} / {{ $softwareLicense->total_seats }}
                </div>
            </div>

            <div>
                <div class="text-sm text-gray-500">สถานะ</div>
                <div class="font-medium">
                    {{ $softwareLicense->status }}
                </div>
            </div>

            <div>
                <div class="text-sm text-gray-500">ราคาเดิม</div>
                <div class="font-medium">
                    {{ $softwareLicense->price ?? '-' }}
                </div>
            </div>
        </div>
    </div>

    <div class="rounded bg-white p-6 shadow">
        <form method="POST" action="{{ route('software-licenses.renew', $softwareLicense) }}" class="space-y-4">
            @csrf

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1 block font-medium">
                        วันที่ต่ออายุ <span class="text-red-600">*</span>
                    </label>
                    <input type="date"
                           name="renewed_at"
                           value="{{ old('renewed_at', now()->format('Y-m-d')) }}"
                           class="w-full rounded border-gray-300">
                    @error('renewed_at')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1 block font-medium">
                        วันหมดอายุใหม่ <span class="text-red-600">*</span>
                    </label>
                    <input type="date"
                           name="new_expire_date"
                           value="{{ old('new_expire_date') }}"
                           class="w-full rounded border-gray-300">
                    @error('new_expire_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1 block font-medium">
                        จำนวนสิทธิ์ทั้งหมด <span class="text-red-600">*</span>
                    </label>
                    <input type="number"
                           name="total_seats"
                           value="{{ old('total_seats', $softwareLicense->total_seats) }}"
                           class="w-full rounded border-gray-300">
                    @error('total_seats')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1 block font-medium">ราคา</label>
                    <input type="number"
                           step="0.01"
                           name="price"
                           value="{{ old('price', $softwareLicense->price) }}"
                           class="w-full rounded border-gray-300">
                    @error('price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="mb-1 block font-medium">หมายเหตุ</label>
                <textarea name="remark"
                          rows="3"
                          class="w-full rounded border-gray-300">{{ old('remark') }}</textarea>
                @error('remark')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-2">
                <button type="submit"
                        class="rounded bg-green-600 px-4 py-2 text-white hover:bg-green-700">
                    บันทึกการต่ออายุ
                </button>

                <a href="{{ route('software-licenses.index') }}"
                   class="rounded bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">
                    ย้อนกลับ
                </a>
            </div>
        </form>
    </div>
</x-layouts.app>
