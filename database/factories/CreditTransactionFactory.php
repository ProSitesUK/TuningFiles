<?php

namespace Database\Factories;

use App\Models\CreditTransaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CreditTransaction>
 */
class CreditTransactionFactory extends Factory
{
    protected $model = CreditTransaction::class;

    public function definition(): array
    {
        return [
            'user_id'        => User::factory(),
            'type'           => 'purchase',
            'credits'        => 50,
            'balance_after'  => 50,
            'amount_pennies' => 5000,
        ];
    }
}
