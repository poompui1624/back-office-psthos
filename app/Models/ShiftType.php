<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShiftType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'start_time',
        'end_time',
        'crosses_midnight',
        'is_ot',
        'ot_multiplier',
        'ot_flat_rate',
        'color',
        'is_active',
        'description',
    ];

    protected $casts = [
        'crosses_midnight' => 'boolean',
        'is_ot' => 'boolean',
        'ot_multiplier' => 'decimal:2',
        'ot_flat_rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function dutySchedules(): HasMany
    {
        return $this->hasMany(DutySchedule::class);
    }
}
