<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Computer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'asset_id',
        'department_id',
        'responsible_employee_id',
        'machine_uuid',
        'hostname',
        'ip_address',
        'mac_address',
        'manufacturer',
        'model',
        'serial_number',
        'os_name',
        'os_version',
        'cpu_name',
        'ram_gb',
        'storage_gb',
        'last_seen_at',
        'source',
        'status',
        'remark',
    ];

    protected $casts = [
        'last_seen_at' => 'datetime',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function responsibleEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'responsible_employee_id');
    }

    public function snapshots(): HasMany
    {
        return $this->hasMany(ComputerSnapshot::class);
    }

    public function latestSnapshot(): HasOne
    {
        return $this->hasOne(ComputerSnapshot::class)->latestOfMany();
    }

    public function software(): HasMany
    {
        return $this->hasMany(ComputerSoftware::class);
    }

    public function repairRequests(): MorphMany
    {
        return $this->morphMany(RepairRequest::class, 'repairable');
    }
}
