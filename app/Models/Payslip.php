<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payslip extends Model
{
    use HasFactory;

    protected $fillable = [
        'payroll_period_id',
        'employee_id',
        'salary_profile_id',
        'gross_income',
        'total_deduction',
        'net_pay',
        'late_minutes',
        'early_leave_minutes',
        'absent_days',
        'status',
        'generated_at',
        'approved_at',
        'approved_by',
        'remark',
    ];

    protected $casts = [
        'gross_income' => 'decimal:2',
        'total_deduction' => 'decimal:2',
        'net_pay' => 'decimal:2',
        'generated_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function payrollPeriod(): BelongsTo
    {
        return $this->belongsTo(PayrollPeriod::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function salaryProfile(): BelongsTo
    {
        return $this->belongsTo(SalaryProfile::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PayslipItem::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
