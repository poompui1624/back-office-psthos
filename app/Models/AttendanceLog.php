<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceLog extends Model
{
    protected $fillable = [
        'employee_id',
        'attendance_device_id',
        'employee_code',
        'device_code',
        'scan_time',
        'scan_date',
        'scan_type',
        'verify_type',
        'source',
        'raw_data',
    ];

    protected $casts = [
        'scan_time' => 'datetime',
        'scan_date' => 'date',
        'raw_data' => 'array',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(AttendanceDevice::class, 'attendance_device_id');
    }
}
