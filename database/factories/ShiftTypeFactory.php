<?php

namespace Database\Factories;

use App\Models\ShiftType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ShiftType>
 */
class ShiftTypeFactory extends Factory
{
    protected $model = ShiftType::class;

    public function definition(): array
    {
        return [
            'code' => fake()->unique()->numerify('SH##'),
            'name' => 'เวรเช้า '.fake()->unique()->numberBetween(1, 9999),
            'start_time' => '08:00:00',
            'end_time' => '16:00:00',
            'crosses_midnight' => false,
            'color' => fake()->hexColor(),
            'is_active' => true,
            'description' => null,
        ];
    }

    public function morning(): static
    {
        return $this->state(fn () => [
            'name' => 'เวรเช้า '.fake()->unique()->numberBetween(1, 9999),
            'start_time' => '08:00:00',
            'end_time' => '16:00:00',
            'crosses_midnight' => false,
        ]);
    }

    public function afternoon(): static
    {
        return $this->state(fn () => [
            'name' => 'เวรบ่าย '.fake()->unique()->numberBetween(1, 9999),
            'start_time' => '16:00:00',
            'end_time' => '00:00:00',
            'crosses_midnight' => false,
        ]);
    }

    /**
     * A night shift that runs past midnight into the following day.
     */
    public function night(): static
    {
        return $this->state(fn () => [
            'name' => 'เวรดึก '.fake()->unique()->numberBetween(1, 9999),
            'start_time' => '00:00:00',
            'end_time' => '08:00:00',
            'crosses_midnight' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
