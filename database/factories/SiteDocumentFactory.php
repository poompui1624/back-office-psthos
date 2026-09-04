<?php

namespace Database\Factories;

use App\Models\SiteDocument;
use Illuminate\Database\Eloquent\Factories\Factory;

class SiteDocumentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category' => fake()->randomElement(array_keys(SiteDocument::categories())),
            'title' => fake()->sentence(4),
            'description' => fake()->sentence(10),
            'file_path' => 'site/documents/'.fake()->uuid().'.pdf',
            'file_original_name' => fake()->words(3, true).'.pdf',
            'file_mime' => 'application/pdf',
            'file_extension' => 'pdf',
            'file_size' => fake()->numberBetween(30000, 5000000),
            'published_at' => now()->subDay(),
            'is_published' => true,
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

    public function category(string $category): static
    {
        return $this->state(fn () => ['category' => $category]);
    }
}
