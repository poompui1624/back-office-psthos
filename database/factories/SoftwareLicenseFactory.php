<?php

namespace Database\Factories;

use App\Models\SoftwareLicense;
use App\Models\SoftwareProduct;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SoftwareLicense>
 */
class SoftwareLicenseFactory extends Factory
{
    protected $model = SoftwareLicense::class;

    public function definition(): array
    {
        return [
            'software_product_id' => SoftwareProduct::factory(),
            'license_name' => fake()->unique()->words(2, true).' License',
            'license_key' => fake()->unique()->bothify('????-####-????-####'),
            'license_type' => fake()->randomElement(['subscription', 'perpetual', 'oem']),
            'total_seats' => 10,
            'used_seats' => 3,
            'purchase_date' => now()->subYear()->toDateString(),
            'start_date' => now()->subYear()->toDateString(),
            'expire_date' => now()->addMonths(6)->toDateString(),
            'price' => fake()->numberBetween(5000, 200000),
            'vendor_contact' => fake()->companyEmail(),
            'status' => 'active',
            'remark' => null,
        ];
    }

    public function expiringIn(int $days): static
    {
        return $this->state(fn () => ['expire_date' => now()->addDays($days)->toDateString()]);
    }

    public function expired(): static
    {
        return $this->state(fn () => [
            'expire_date' => now()->subDays(7)->toDateString(),
            'status' => 'expired',
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn () => [
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);
    }
}
