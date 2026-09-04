<?php

namespace Database\Factories;

use App\Models\SitePage;
use Illuminate\Database\Eloquent\Factories\Factory;

class SitePageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'key' => fake()->unique()->randomElement(array_keys(SitePage::KEYS)),
            'title' => fake()->sentence(3),
            'body' => fake()->paragraphs(3, true),
            'image_path' => null,
            'is_active' => true,
        ];
    }
}
