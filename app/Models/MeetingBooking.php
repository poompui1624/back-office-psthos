<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeetingBooking extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'booking_no',
        'meeting_room_id',
        'employee_id',
        'department_id',
        'created_by',
        'title',
        'purpose',
        'start_at',
        'end_at',
        'attendees_count',
        'need_projector',
        'need_sound_system',
        'need_video_conference',
        'need_whiteboard',
        'status',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'cancelled_at',
        'approval_remark',
        'remark',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'need_projector' => 'boolean',
        'need_sound_system' => 'boolean',
        'need_video_conference' => 'boolean',
        'need_whiteboard' => 'boolean',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(MeetingRoom::class, 'meeting_room_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejecter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function actions(): HasMany
    {
        return $this->hasMany(MeetingBookingAction::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
