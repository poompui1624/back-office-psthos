<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\PayrollPeriod;
use App\Models\Payslip;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payslip>
 */
class PayslipFactory extends Factory
{
    protected $model = Payslip::class;

    public function definition(): array
    {
        return [
            'payroll_period_id' => PayrollPeriod::factory(),
            'employee_id' => Employee::factory(),
            'gross_income' => 25000,
            'total_deduction' => 1000,
            'net_pay' => 24000,
            'late_minutes' => 0,
            'early_leave_minutes' => 0,
            'absent_days' => 0,
            'status' => 'draft',
            'generated_at' => now(),
        ];
    }

    public function forEmployee(Employee $employee): static
    {
        return $this->state(fn () => ['employee_id' => $employee->id]);
    }
}
