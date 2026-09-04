<?php

namespace Database\Factories;

use App\Models\SitePost;
use Illuminate\Database\Eloquent\Factories\Factory;

class SitePostImageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'site_post_id' => SitePost::factory(),
            'image_path' => 'site/posts/'.fake()->uuid().'.jpg',
            'caption' => fake()->sentence(4),
            'sort_order' => 0,
        ];
    }
}
