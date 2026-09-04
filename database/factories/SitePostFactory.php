<?php

namespace Database\Factories;

use App\Models\SitePost;
use Illuminate\Database\Eloquent\Factories\Factory;

class SitePostFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->sentence(5);

        return [
            'category' => fake()->randomElement(array_keys(SitePost::CATEGORIES)),
            'title' => $title,
            'slug' => SitePost::slugFor($title),
            'excerpt' => fake()->sentence(12),
            'body' => fake()->paragraphs(3, true),
            'cover_image_path' => null,
            'published_at' => now()->subDay(),
            'is_published' => true,
            'is_pinned' => false,
            'created_by' => null,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => ['is_published' => false]);
    }

    public function scheduled(): static
    {
        return $this->state(fn () => ['is_published' => true, 'published_at' => now()->addWeek()]);
    }

    public function pinned(): static
    {
        return $this->state(fn () => ['is_pinned' => true]);
    }

    public function category(string $category): static
    {
        return $this->state(fn () => ['category' => $category]);
    }
}
