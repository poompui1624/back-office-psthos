<?php

namespace Database\Factories;

use App\Models\ComputerAgent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ComputerAgent>
 */
class ComputerAgentFactory extends Factory
{
    protected $model = ComputerAgent::class;

    public function definition(): array
    {
        return [
            'name' => 'Agent '.fake()->unique()->numberBetween(1, 9999),
            'token_hash' => hash('sha256', fake()->unique()->uuid()),
            'is_active' => true,
            'last_seen_at' => now(),
            'last_ip_address' => fake()->localIpv4(),
            'last_user_agent' => 'SACS-Agent/1.0',
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }

    public function neverSeen(): static
    {
        return $this->state(fn () => [
            'last_seen_at' => null,
            'last_ip_address' => null,
            'last_user_agent' => null,
        ]);
    }
}
