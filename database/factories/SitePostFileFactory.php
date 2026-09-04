<?php

namespace Database\Factories;

use App\Models\SitePost;
use Illuminate\Database\Eloquent\Factories\Factory;

class SitePostFileFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->words(3, true).'.pdf';

        return [
            'site_post_id' => SitePost::factory(),
            'title' => null,
            'file_path' => 'site/posts/files/'.fake()->uuid().'.pdf',
            'file_original_name' => $name,
            'file_mime' => 'application/pdf',
            'file_extension' => 'pdf',
            'file_size' => fake()->numberBetween(20000, 4000000),
            'sort_order' => 0,
        ];
    }
}
