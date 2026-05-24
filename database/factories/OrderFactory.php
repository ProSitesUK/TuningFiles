<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'reference'     => (string) fake()->unique()->numberBetween(5000, 9999),
            'customer_id'   => User::factory(),
            'status'        => 'queued',
            'credits_cost'  => fake()->numberBetween(20, 60),
            'vehicle_label' => fake()->words(3, true),
            'ecu_label'     => 'MED17.5.25',
            'options_label' => 'Stage 1',
            'options'       => ['stage_1'],
            'origin'        => 'customer upload',
            'queued_at'     => now(),
            'sla_due_at'    => now()->addMinutes(30),
        ];
    }
}
