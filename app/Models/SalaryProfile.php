<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalaryProfile extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'employee_id',
        'base_salary',
        'position_allowance',
        'professional_allowance',
        'other_allowance',
        'social_security',
        'tax',
        'provident_fund',
        'other_deduction',
        'late_deduction_per_minute',
        'early_leave_deduction_per_minute',
        'absent_deduction_per_day',
        'is_active',
        'remark',
    ];

    protected $casts = [
        'base_salary' => 'decimal:2',
        'position_allowance' => 'decimal:2',
        'professional_allowance' => 'decimal:2',
        'other_allowance' => 'decimal:2',
        'social_security' => 'decimal:2',
        'tax' => 'decimal:2',
        'provident_fund' => 'decimal:2',
        'other_deduction' => 'decimal:2',
        'late_deduction_per_minute' => 'decimal:2',
        'early_leave_deduction_per_minute' => 'decimal:2',
        'absent_deduction_per_day' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
