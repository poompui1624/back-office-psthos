<x-layouts.app title="นำเข้าเวลาสแกน">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">นำเข้าเวลาสแกนจาก CSV</h1>
        <p class="text-sm text-gray-600">
            รองรับไฟล์ CSV จากเครื่องสแกนนิ้วมือ โดยต้องมีรหัสพนักงานและวันเวลาสแกน
        </p>
    </div>

    @if (session('error'))
        <div class="mb-4 rounded bg-red-100 px-4 py-3 text-red-800">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded bg-white p-6 shadow">
            <h2 class="mb-4 text-lg font-bold">
                อัปโหลดไฟล์
            </h2>

            <form method="POST"
                  action="{{ route('attendance-logs.import') }}"
                  enctype="multipart/form-data"
                  class="space-y-4">
                @csrf

                <div>
                    <label class="mb-1 block font-medium">เครื่องสแกน</label>

                    <select name="attendance_device_id" class="w-full rounded border-gray-300">
                        <option value="">-- ไม่ระบุ / ใช้ device_code จากไฟล์ --</option>

                        @foreach ($devices as $device)
                            <option value="{{ $device->id }}" @selected(old('attendance_device_id') == $device->id)>
                                {{ $device->code }} - {{ $device->name }}
                            </option>
                        @endforeach
                    </select>

                    @error('attendance_device_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1 block font-medium">
                        ไฟล์ CSV <span class="text-red-600">*</span>
                    </label>

                    <input type="file"
                           name="file"
                           accept=".csv,.txt"
                           class="w-full rounded border border-gray-300 p-2">

                    @error('file')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex gap-2">
                    <button type="submit"
                            class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                        นำเข้าข้อมูล
                    </button>

                    <a href="{{ route('attendance-logs.index') }}"
                       class="rounded bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">
                        ย้อนกลับ
                    </a>
                </div>
            </form>
        </div>

        <div class="rounded bg-white p-6 shadow">
            <h2 class="mb-4 text-lg font-bold">
                รูปแบบ CSV ที่รองรับ
            </h2>

            <p class="mb-3 text-sm text-gray-600">
                Header สามารถใช้ชื่อ column ได้หลายแบบ ระบบจะพยายาม map ให้อัตโนมัติ
            </p>

            <div class="mb-4 rounded bg-gray-900 p-4 text-sm text-white">
<pre>employee_code,scan_time,device_code,scan_type,verify_type
EMP001,2026-04-29 08:01:00,DEVICE01,in,fingerprint
EMP001,2026-04-29 16:32:00,DEVICE01,out,fingerprint
EMP002,2026-04-29 07:55:00,DEVICE01,in,fingerprint</pre>
            </div>

            <div class="space-y-3 text-sm text-gray-700">
                <div>
                    <div class="font-semibold">รหัสพนักงาน รองรับชื่อ column:</div>
                    <div class="text-gray-600">
                        employee_code, emp_code, user_id, userid, pin, id
                    </div>
                </div>

                <div>
                    <div class="font-semibold">วันเวลาสแกน รองรับชื่อ column:</div>
                    <div class="text-gray-600">
                        scan_time, datetime, date_time, time, timestamp, check_time
                    </div>
                </div>

                <div>
                    <div class="font-semibold">รหัสเครื่อง รองรับชื่อ column:</div>
                    <div class="text-gray-600">
                        device_code, device, terminal, terminal_id
                    </div>
                </div>

                <div>
                    <div class="font-semibold">ประเภทการสแกน:</div>
                    <div class="text-gray-600">
                        scan_type, in_out, state, status
                    </div>
                </div>
            </div>

            <div class="mt-4 rounded bg-yellow-50 p-4 text-sm text-yellow-900">
                สำคัญ: รหัสพนักงานในไฟล์ CSV ต้องตรงกับ `employee_code` ในทะเบียนบุคลากร
                เพื่อให้ระบบผูกข้อมูลเวลาสแกนกับบุคลากรได้ถูกต้อง
            </div>
        </div>
    </div>
</x-layouts.app>
