<?php

namespace Database\Factories;

use App\Models\ItaDocument;
use App\Models\ItaFiscalYear;
use App\Models\ItaMoitSubTopic;
use App\Models\ItaMoitTopic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ItaDocument>
 */
class ItaDocumentFactory extends Factory
{
    protected $model = ItaDocument::class;

    public function definition(): array
    {
        return [
            'fiscal_year_id' => ItaFiscalYear::factory(),
            'main_topic_id' => ItaMoitTopic::factory(),
            'sub_topic_id' => ItaMoitSubTopic::factory(),
            'title' => 'เอกสาร '.fake()->unique()->words(3, true),
            'description' => null,
            'file_original_name' => 'document.pdf',
            'file_path' => 'ita/'.fake()->unique()->uuid().'.pdf',
            'file_mime' => 'application/pdf',
            'file_extension' => 'pdf',
            'file_size' => fake()->numberBetween(10000, 5000000),
            'is_public' => true,
        ];
    }

    public function private(): static
    {
        return $this->state(fn () => ['is_public' => false]);
    }
}
