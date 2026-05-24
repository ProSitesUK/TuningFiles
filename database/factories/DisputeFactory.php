<?php

namespace Database\Factories;

use App\Models\Dispute;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Dispute>
 */
class DisputeFactory extends Factory
{
    protected $model = Dispute::class;

    public function definition(): array
    {
        return [
            'order_id'     => Order::factory(),
            'opened_by_id' => User::factory(),
            'reason'       => fake()->sentence(),
            'status'       => 'open',
        ];
    }
}
