<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LeaveRequest>
 */
class LeaveRequestFactory extends Factory
{
    protected $model = LeaveRequest::class;

    public function definition(): array
    {
        $start = fake()->dateTimeBetween('-1 month', '+1 month');
        $end = (clone $start)->modify('+'.fake()->numberBetween(0, 3).' days');

        return [
            'request_no' => 'LV'.fake()->unique()->numerify('##########'),
            'employee_id' => Employee::factory(),
            'department_id' => fn (array $attributes) => Employee::find($attributes['employee_id'])?->department_id,
            'leave_type_id' => LeaveType::factory(),
            'start_date' => $start->format('Y-m-d'),
            'end_date' => $end->format('Y-m-d'),
            'start_period' => 'full',
            'end_period' => 'full',
            'total_days' => $start->diff($end)->days + 1,
            'reason' => fake()->sentence(),
            'contact_during_leave' => fake()->numerify('08########'),
            'created_by' => User::factory(),
            'status' => 'pending',
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => 'pending']);
    }

    public function approved(): static
    {
        return $this->state(fn () => [
            'status' => 'approved',
            'approved_by' => User::factory(),
            'approved_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn () => [
            'status' => 'rejected',
            'rejected_by' => User::factory(),
            'rejected_at' => now(),
            'approval_remark' => fake()->sentence(),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn () => [
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);
    }

    /**
     * Pin the request to an exact date range, e.g. to land it inside a dashboard month.
     */
    public function between(string $startDate, string $endDate): static
    {
        return $this->state(fn () => [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_days' => Carbon::parse($startDate)->diffInDays($endDate) + 1,
        ]);
    }

    public function forEmployee(Employee $employee): static
    {
        return $this->state(fn () => [
            'employee_id' => $employee->id,
            'department_id' => $employee->department_id,
        ]);
    }
}
