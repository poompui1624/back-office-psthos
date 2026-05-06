<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MeetingRoom extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'location',
        'capacity',
        'has_projector',
        'has_sound_system',
        'has_video_conference',
        'has_whiteboard',
        'is_active',
        'description',
    ];

    protected $casts = [
        'has_projector' => 'boolean',
        'has_sound_system' => 'boolean',
        'has_video_conference' => 'boolean',
        'has_whiteboard' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(MeetingBooking::class);
    }
}
