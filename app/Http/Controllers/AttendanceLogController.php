<?php

namespace App\Http\Controllers;

use App\Models\AttendanceDevice;
use App\Models\AttendanceLog;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AttendanceLogController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->can('attendance.view'), 403);

        $search = $request->string('search')->toString();
        $dateFrom = $request->string('date_from')->toString();
        $dateTo = $request->string('date_to')->toString();

        $logs = AttendanceLog::query()
            ->with(['employee', 'device'])
            ->when($search, function ($query) use ($search) {
                $query->where('employee_code', 'like', "%{$search}%")
                    ->orWhereHas('employee', function ($employeeQuery) use ($search) {
                        $employeeQuery->where('employee_code', 'like', "%{$search}%")
                            ->orWhere('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('device', function ($deviceQuery) use ($search) {
                        $deviceQuery->where('code', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%");
                    });
            })
            ->when($dateFrom, function ($query) use ($dateFrom) {
                $query->whereDate('scan_date', '>=', $dateFrom);
            })
            ->when($dateTo, function ($query) use ($dateTo) {
                $query->whereDate('scan_date', '<=', $dateTo);
            })
            ->latest('scan_time')
            ->paginate(50)
            ->withQueryString();

        return view('attendance-logs.index', compact(
            'logs',
            'search',
            'dateFrom',
            'dateTo'
        ));
    }

    public function importForm()
    {
        abort_unless(auth()->user()->can('attendance.import'), 403);

        return view('attendance-logs.import', [
            'devices' => AttendanceDevice::where('is_active', true)->orderBy('code')->get(),
        ]);
    }

    public function import(Request $request)
    {
        abort_unless(auth()->user()->can('attendance.import'), 403);

        $validated = $request->validate([
            'attendance_device_id' => ['nullable', 'exists:attendance_devices,id'],
            'file' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $device = null;

        if (! empty($validated['attendance_device_id'])) {
            $device = AttendanceDevice::find($validated['attendance_device_id']);
        }

        $path = $request->file('file')->getRealPath();

        $handle = fopen($path, 'r');

        if (! $handle) {
            return back()->with('error', 'ไม่สามารถอ่านไฟล์ได้');
        }

        $header = fgetcsv($handle);

        if (! $header) {
            fclose($handle);

            return back()->with('error', 'ไฟล์ไม่มี header');
        }

        $header = array_map(function ($value) {
            return Str::of($value)
                ->trim()
                ->lower()
                ->replace(' ', '_')
                ->toString();
        }, $header);

        $imported = 0;
        $skipped = 0;

        DB::transaction(function () use (
            $handle,
            $header,
            $device,
            &$imported,
            &$skipped
        ) {
            while (($row = fgetcsv($handle)) !== false) {
                $data = $this->combineRow($header, $row);

                $employeeCode = $this->pick($data, [
                    'employee_code',
                    'emp_code',
                    'user_id',
                    'userid',
                    'pin',
                    'id',
                ]);

                $scanTimeText = $this->pick($data, [
                    'scan_time',
                    'datetime',
                    'date_time',
                    'time',
                    'timestamp',
                    'check_time',
                ]);

                $deviceCode = $this->pick($data, [
                    'device_code',
                    'device',
                    'terminal',
                    'terminal_id',
                ]);

                if (! $deviceCode && $device) {
                    $deviceCode = $device->code;
                }

                if (! $employeeCode || ! $scanTimeText) {
                    $skipped++;
                    continue;
                }

                try {
                    $scanTime = Carbon::parse($scanTimeText);
                } catch (\Throwable $e) {
                    $skipped++;
                    continue;
                }

                $employee = Employee::where('employee_code', $employeeCode)->first();

                $targetDevice = $device;

                if (! $targetDevice && $deviceCode) {
                    $targetDevice = AttendanceDevice::where('code', $deviceCode)->first();
                }

                $exists = AttendanceLog::where('employee_code', $employeeCode)
                    ->where('scan_time', $scanTime)
                    ->where('device_code', $deviceCode)
                    ->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                AttendanceLog::create([
                    'employee_id' => $employee?->id,
                    'attendance_device_id' => $targetDevice?->id,
                    'employee_code' => $employeeCode,
                    'device_code' => $deviceCode,
                    'scan_time' => $scanTime,
                    'scan_date' => $scanTime->toDateString(),
                    'scan_type' => $this->pick($data, ['scan_type', 'in_out', 'state', 'status']),
                    'verify_type' => $this->pick($data, ['verify_type', 'verify', 'method']),
                    'source' => 'csv',
                    'raw_data' => $data,
                ]);

                $imported++;
            }
        });

        fclose($handle);

        return redirect()
            ->route('attendance-logs.index')
            ->with('success', "นำเข้าเวลาสแกนสำเร็จ {$imported} รายการ, ข้าม {$skipped} รายการ");
    }

    private function combineRow(array $header, array $row): array
    {
        $data = [];

        foreach ($header as $index => $key) {
            $data[$key] = trim($row[$index] ?? '');
        }

        return $data;
    }

    private function pick(array $data, array $keys): ?string
    {
        foreach ($keys as $key) {
            if (isset($data[$key]) && trim((string) $data[$key]) !== '') {
                return trim((string) $data[$key]);
            }
        }

        return null;
    }
}
