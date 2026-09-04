<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SiteLinkFactory extends Factory
{
    public function definition(): array
    {
        return [
            'label' => fake()->words(2, true),
            'url' => fake()->url(),
            'icon' => 'document',
            'description' => fake()->sentence(5),
            'sort_order' => 0,
            'is_active' => true,
            'opens_new_tab' => true,
        ];
    }
}
