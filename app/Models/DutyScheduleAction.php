<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DutyScheduleAction extends Model
{
    protected $fillable = [
        'duty_schedule_id',
        'user_id',
        'action',
        'old_values',
        'new_values',
        'remark',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function dutySchedule(): BelongsTo
    {
        return $this->belongsTo(DutySchedule::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
