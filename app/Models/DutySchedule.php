<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DutySchedule extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'employee_id',
        'department_id',
        'shift_type_id',
        'work_date',
        'start_at',
        'end_at',
        'role_group',
        'status',
        'assigned_by',
        'remark',
    ];

    protected $casts = [
        'work_date' => 'date',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function shiftType(): BelongsTo
    {
        return $this->belongsTo(ShiftType::class);
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function actions(): HasMany
    {
        return $this->hasMany(DutyScheduleAction::class);
    }
}
