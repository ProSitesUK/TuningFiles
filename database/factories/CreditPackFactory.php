<?php

namespace Database\Factories;

use App\Models\CreditPack;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CreditPack>
 */
class CreditPackFactory extends Factory
{
    protected $model = CreditPack::class;

    public function definition(): array
    {
        return [
            'name'          => fake()->words(2, true),
            'slug'          => fake()->unique()->slug(),
            'credits'       => fake()->numberBetween(10, 200),
            'price_pennies' => fake()->numberBetween(1000, 20000),
            'is_active'     => true,
        ];
    }
}
