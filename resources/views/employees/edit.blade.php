<x-layouts.app title="แก้ไขบุคลากร">
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold">แก้ไขบุคลากร</h1>
            <p class="text-sm text-gray-600">
                {{ $employee->employee_code }} - {{ $employee->full_name }}
            </p>
        </div>

        @if (auth()->user()?->can('employee.sensitive.view') || auth()->user()?->can('employee.update'))
            <a href="{{ route('employees.personnel-profile.edit', $employee) }}"
               class="inline-flex justify-center rounded bg-sky-600 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-700">
                ข้อมูล ก.พ.7
            </a>
        @endif
    </div>

    <div class="rounded bg-white p-6 shadow">
        <form method="POST" action="{{ route('employees.update', $employee) }}" class="space-y-4">
            @csrf
            @method('PUT')

            @include('employees._form')

            <div class="flex gap-2">
                <button type="submit"
                        class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                    บันทึกการแก้ไข
                </button>

                <a href="{{ route('employees.index') }}"
                   class="rounded bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">
                    ย้อนกลับ
                </a>
            </div>
        </form>
    </div>

    @include('attachments._employee')
</x-layouts.app>
