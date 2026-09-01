<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComputerSnapshot extends Model
{
    protected $fillable = [
        'computer_id',
        'hostname',
        'ip_address',
        'os_name',
        'os_version',
        'cpu_name',
        'ram_gb',
        'storage_gb',
        'installed_software',
        'software_hash',
        'raw_payload',
        'reported_at',
    ];

    protected $casts = [
        'installed_software' => 'array',
        'raw_payload' => 'array',
        'reported_at' => 'datetime',
    ];

    public function computer(): BelongsTo
    {
        return $this->belongsTo(Computer::class);
    }
}
