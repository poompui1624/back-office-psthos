<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeetingBookingAction extends Model
{
    protected $fillable = [
        'meeting_booking_id',
        'user_id',
        'action',
        'old_status',
        'new_status',
        'remark',
    ];

    public function meetingBooking(): BelongsTo
    {
        return $this->belongsTo(MeetingBooking::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
