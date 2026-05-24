<?php

namespace Database\Factories;

use App\Models\ResellerProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ResellerProfile>
 */
class ResellerProfileFactory extends Factory
{
    protected $model = ResellerProfile::class;

    public function definition(): array
    {
        return [
            'user_id'        => User::factory(),
            'business_name'  => fake()->company(),
            'slug'           => fake()->unique()->slug(),
            'max_customers'  => 0,
            'is_active'      => true,
        ];
    }
}
