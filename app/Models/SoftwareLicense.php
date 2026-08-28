<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SoftwareLicense extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'software_product_id',
        'license_name',
        'license_key',
        'license_type',
        'total_seats',
        'used_seats',
        'purchase_date',
        'start_date',
        'expire_date',
        'renewed_at',
        'cancelled_at',
        'price',
        'vendor_contact',
        'status',
        'remark',
        'last_expire_notified_at',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'start_date' => 'date',
        'expire_date' => 'date',
        'renewed_at' => 'date',
        'cancelled_at' => 'date',
        'price' => 'decimal:2',
        'last_expire_notified_at' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(SoftwareProduct::class, 'software_product_id');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expire_date && $this->expire_date->isPast();
    }

    public function getIsExpiringSoonAttribute(): bool
    {
        return $this->expire_date &&
            ! $this->expire_date->isPast() &&
            $this->expire_date->diffInDays(now()) <= 30;
    }

    public function actions(): HasMany
    {
        return $this->hasMany(SoftwareLicenseAction::class);
    }
}
