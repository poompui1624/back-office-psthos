<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\SalaryProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SalaryProfile>
 */
class SalaryProfileFactory extends Factory
{
    protected $model = SalaryProfile::class;

    public function definition(): array
    {
        return [
            'employee_id' => Employee::factory(),
            'base_salary' => 20000,
            'position_allowance' => 3500,
            'professional_allowance' => 0,
            'other_allowance' => 0,
            'social_security' => 750,
            'tax' => 0,
            'provident_fund' => 0,
            'other_deduction' => 0,
            'late_deduction_per_minute' => 0,
            'early_leave_deduction_per_minute' => 0,
            'absent_deduction_per_day' => 0,
            'is_active' => true,
            'remark' => null,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }

    /**
     * Turn on the per-minute and per-day penalties so deductions are exercised.
     */
    public function withPenalties(): static
    {
        return $this->state(fn () => [
            'late_deduction_per_minute' => 5,
            'early_leave_deduction_per_minute' => 5,
            'absent_deduction_per_day' => 800,
        ]);
    }

    public function forEmployee(Employee $employee): static
    {
        return $this->state(fn () => ['employee_id' => $employee->id]);
    }
}
