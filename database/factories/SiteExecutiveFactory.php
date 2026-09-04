<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SiteExecutiveFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'position' => 'ผู้อำนวยการ',
            'photo_path' => null,
            'phone' => fake()->numerify('0##-###-####'),
            'email' => fake()->safeEmail(),
            'sort_order' => 0,
            'is_featured' => false,
            'is_active' => true,
        ];
    }

    public function featured(): static
    {
        return $this->state(fn () => ['is_featured' => true]);
    }
}
