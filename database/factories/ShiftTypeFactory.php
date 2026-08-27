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
            'is_ot' => false,
            'ot_multiplier' => 1,
            'ot_flat_rate' => null,
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

    /**
     * An overtime shift paid at a multiple of the employee's hourly rate.
     */
    public function overtime(float $multiplier = 1.5): static
    {
        return $this->state(fn () => [
            'name' => 'OT '.fake()->unique()->numberBetween(1, 9999),
            'is_ot' => true,
            'ot_multiplier' => $multiplier,
            'ot_flat_rate' => null,
        ]);
    }

    /**
     * An overtime shift paid a fixed amount regardless of its length.
     */
    public function overtimeFlat(float $amount): static
    {
        return $this->state(fn () => [
            'name' => 'OT เหมาจ่าย '.fake()->unique()->numberBetween(1, 9999),
            'is_ot' => true,
            'ot_flat_rate' => $amount,
        ]);
    }
}
