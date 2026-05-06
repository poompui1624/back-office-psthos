<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceDailySummary extends Model
{
    protected $fillable = [
        'employee_id',
        'work_date',
        'first_in_at',
        'last_out_at',
        'expected_in_time',
        'expected_out_time',
        'work_minutes',
        'late_minutes',
        'early_leave_minutes',
        'status',
        'remark',
        'generated_at',
        'duty_schedule_id',
    ];

    protected $casts = [
        'work_date' => 'date',
        'first_in_at' => 'datetime',
        'last_out_at' => 'datetime',
        'generated_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function dutySchedule(): BelongsTo
    {
        return $this->belongsTo(DutySchedule::class);
    }

    public function getWorkHoursAttribute(): string
    {
        $hours = intdiv($this->work_minutes, 60);
        $minutes = $this->work_minutes % 60;

        return "{$hours} ชม. {$minutes} นาที";
    }
}