<?php

namespace Database\Factories;

use App\Models\MeetingRoom;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MeetingRoom>
 */
class MeetingRoomFactory extends Factory
{
    protected $model = MeetingRoom::class;

    public function definition(): array
    {
        return [
            'code' => fake()->unique()->numerify('MR##'),
            'name' => 'ห้องประชุม'.fake()->unique()->word(),
            'location' => 'อาคาร '.fake()->randomElement(['A', 'B', 'C']).' ชั้น '.fake()->numberBetween(1, 5),
            'capacity' => fake()->numberBetween(8, 120),
            'has_projector' => true,
            'has_sound_system' => true,
            'has_video_conference' => false,
            'has_whiteboard' => true,
            'is_active' => true,
            'description' => null,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
