<?php

namespace App\Models;

use App\Concerns\ScopesByDepartment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, ScopesByDepartment, SoftDeletes;

    protected $fillable = [
        'employee_code',
        'citizen_id',
        'prefix',
        'first_name',
        'last_name',
        'gender',
        'birth_date',
        'phone',
        'email',
        'department_id',
        'position_id',
        'employment_type',
        'start_work_date',
        'status',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'start_work_date' => 'date',
    ];

    public function getFullNameAttribute(): string
    {
        return trim(($this->prefix ? $this->prefix.' ' : '').$this->first_name.' '.$this->last_name);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function personnelProfile(): HasOne
    {
        return $this->hasOne(EmployeePersonnelProfile::class);
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function attendanceLogs(): HasMany
    {
        return $this->hasMany(AttendanceLog::class);
    }

    public function attendanceDailySummaries(): HasMany
    {
        return $this->hasMany(AttendanceDailySummary::class);
    }

    public function dutySchedules(): HasMany
    {
        return $this->hasMany(DutySchedule::class);
    }

    public function salaryProfile(): HasOne
    {
        return $this->hasOne(SalaryProfile::class);
    }

    public function payslips(): HasMany
    {
        return $this->hasMany(Payslip::class);
    }

    public function departmentScopePrefix(): string
    {
        return 'employee';
    }
}
