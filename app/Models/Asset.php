<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

// use Illuminate\Database\Eloquent\Relations\MorphMany;

class Asset extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'asset_code',
        'name',
        'asset_category_id',
        'department_id',
        'responsible_employee_id',
        'brand',
        'model',
        'serial_number',
        'received_date',
        'purchase_price',
        'budget_source',
        'location',
        'status',
        'remark',
    ];

    protected $casts = [
        'received_date' => 'date',
        'purchase_price' => 'decimal:2',
    ];

    public function getAgeTextAttribute(): string
    {
        if (! $this->received_date instanceof CarbonInterface) {
            return '-';
        }

        $diff = $this->received_date->diff(now());

        if ($diff->y === 0 && $diff->m === 0) {
            return $diff->d.' วัน';
        }

        if ($diff->y === 0) {
            return $diff->m.' เดือน '.$diff->d.' วัน';
        }

        if ($diff->m === 0) {
            return $diff->y.' ปี';
        }

        return $diff->y.' ปี '.$diff->m.' เดือน';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(AssetCategory::class, 'asset_category_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function responsibleEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'responsible_employee_id');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function movements(): HasMany
    {
        return $this->hasMany(AssetMovement::class);
    }

    public function repairRequests(): MorphMany
    {
        return $this->morphMany(RepairRequest::class, 'repairable');
    }
}
