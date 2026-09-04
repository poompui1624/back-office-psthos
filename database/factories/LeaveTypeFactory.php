<?php

namespace Database\Factories;

use App\Models\LeaveType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LeaveType>
 */
class LeaveTypeFactory extends Factory
{
    protected $model = LeaveType::class;

    public function definition(): array
    {
        return [
            'code' => fake()->unique()->numerify('LT##'),
            'name' => fake()->randomElement(['ลาป่วย', 'ลากิจ', 'ลาพักผ่อน', 'ลาคลอด']).' '.fake()->unique()->numberBetween(1, 9999),
            'default_days_per_year' => fake()->randomElement([10, 15, 30]),
            'requires_document' => false,
            'is_active' => true,
            'description' => null,
        ];
    }

    public function requiresDocument(): static
    {
        return $this->state(fn () => ['requires_document' => true]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
