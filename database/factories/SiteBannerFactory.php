<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SiteBannerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'image_path' => 'site/banners/'.fake()->uuid().'.jpg',
            'title' => fake()->sentence(4),
            'subtitle' => fake()->sentence(6),
            'link_url' => null,
            'sort_order' => 0,
            'is_active' => true,
            'starts_at' => null,
            'ends_at' => null,
        ];
    }

    public function expired(): static
    {
        return $this->state(fn () => [
            'starts_at' => now()->subMonth(),
            'ends_at' => now()->subDay(),
        ]);
    }

    public function scheduled(): static
    {
        return $this->state(fn () => [
            'starts_at' => now()->addWeek(),
            'ends_at' => null,
        ]);
    }
}
