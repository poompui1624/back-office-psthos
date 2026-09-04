<?php

namespace App\Models;

use App\Concerns\ScopesByDepartment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RepairRequest extends Model
{
    use HasFactory, ScopesByDepartment, SoftDeletes;

    protected $fillable = [
        'ticket_no',
        'requested_by',
        'requester_employee_id',
        'department_id',
        'repairable_type',
        'repairable_id',
        'category',
        'title',
        'description',
        'location',
        'priority',
        'assigned_to',
        'status',
        'started_at',
        'completed_at',
        'cancelled_at',
        'solution',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function repairable(): MorphTo
    {
        return $this->morphTo();
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function requesterEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'requester_employee_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function updates(): HasMany
    {
        return $this->hasMany(RepairUpdate::class);
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function departmentScopePrefix(): string
    {
        return 'repair';
    }
}
