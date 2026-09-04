<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Employee;
use App\Models\RepairRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RepairRequest>
 */
class RepairRequestFactory extends Factory
{
    protected $model = RepairRequest::class;

    public function definition(): array
    {
        return [
            'ticket_no' => 'RP'.fake()->unique()->numerify('##########'),
            'requested_by' => User::factory(),
            'requester_employee_id' => Employee::factory(),
            'department_id' => Department::factory(),
            'category' => fake()->randomElement(['computer', 'network', 'electric', 'building']),
            'title' => 'แจ้งซ่อม'.fake()->word(),
            'description' => fake()->sentence(),
            'location' => fake()->word(),
            'priority' => fake()->randomElement(['low', 'normal', 'high']),
            'status' => 'pending',
        ];
    }

    public function inProgress(): static
    {
        return $this->state(fn () => [
            'status' => 'in_progress',
            'assigned_to' => User::factory(),
            'started_at' => now(),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn () => [
            'status' => 'completed',
            'completed_at' => now(),
            'solution' => fake()->sentence(),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn () => [
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);
    }

    public function inDepartment(Department $department): static
    {
        return $this->state(fn () => ['department_id' => $department->id]);
    }
}
