<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SoftwareLicenseAction extends Model
{
    protected $fillable = [
        'software_license_id',
        'user_id',
        'action',
        'old_expire_date',
        'new_expire_date',
        'old_values',
        'new_values',
        'remark',
    ];

    protected $casts = [
        'old_expire_date' => 'date',
        'new_expire_date' => 'date',
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function softwareLicense(): BelongsTo
    {
        return $this->belongsTo(SoftwareLicense::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
