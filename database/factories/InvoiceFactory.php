<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        return [
            'user_id'        => User::factory(),
            'credit_pack_id' => null,
            'amount_pennies' => fake()->numberBetween(1000, 20000),
            'credits'        => fake()->numberBetween(10, 200),
            'status'         => 'sent',
            'reference'      => 'INV-' . fake()->unique()->numberBetween(1000, 9999),
            'payment_terms'  => 'net_30',
            'due_at'         => now()->addDays(30),
        ];
    }
}
