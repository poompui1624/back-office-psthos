<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Employee>
 */
class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        return [
            'employee_code' => fake()->unique()->numerify('EMP#####'),
            'citizen_id' => fake()->unique()->numerify('#############'),
            'prefix' => fake()->randomElement(['นาย', 'นาง', 'นางสาว']),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'gender' => fake()->randomElement(['male', 'female']),
            'birth_date' => fake()->dateTimeBetween('-55 years', '-25 years')->format('Y-m-d'),
            'phone' => fake()->numerify('08########'),
            'email' => fake()->unique()->safeEmail(),
            'department_id' => Department::factory(),
            'position_id' => Position::factory(),
            'employment_type' => fake()->randomElement(['ข้าราชการ', 'พนักงานราชการ', 'ลูกจ้างชั่วคราว']),
            'start_work_date' => fake()->dateTimeBetween('-10 years', '-1 year')->format('Y-m-d'),
            'status' => 'active',
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => ['status' => 'active']);
    }

    public function resigned(): static
    {
        return $this->state(fn () => ['status' => 'resigned']);
    }

    /**
     * Put the employee in an existing department instead of creating a new one.
     */
    public function inDepartment(Department $department): static
    {
        return $this->state(fn () => ['department_id' => $department->id]);
    }
}
