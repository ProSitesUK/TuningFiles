<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name'          => 'Starter',
                'slug'          => 'starter',
                'description'   => 'For independent tuners getting started',
                'price_pennies' => 4900,
                'interval'      => 'monthly',
                'max_customers' => 50,
                'features'      => ['White-label portal', 'Custom pricing', '50 customers max', 'Email support'],
                'sort_order'    => 0,
            ],
            [
                'name'          => 'Professional',
                'slug'          => 'professional',
                'description'   => 'For growing tuning businesses',
                'price_pennies' => 14900,
                'interval'      => 'monthly',
                'max_customers' => 0,
                'features'      => ['Everything in Starter', 'Unlimited customers', 'Priority support', 'API access', 'Custom domain'],
                'sort_order'    => 1,
            ],
            [
                'name'          => 'Enterprise',
                'slug'          => 'enterprise',
                'description'   => 'For tuning networks and franchises',
                'price_pennies' => 0,
                'interval'      => 'monthly',
                'max_customers' => 0,
                'features'      => ['Everything in Professional', 'Multiple tuner seats', 'Dedicated account manager', 'Custom integrations', 'SLA guarantee'],
                'sort_order'    => 2,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::firstOrCreate(
                ['slug' => $plan['slug']],
                $plan
            );
        }
    }
}
