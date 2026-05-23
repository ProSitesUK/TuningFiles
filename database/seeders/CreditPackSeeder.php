<?php

namespace Database\Seeders;

use App\Models\CreditPack;
use Illuminate\Database\Seeder;

class CreditPackSeeder extends Seeder
{
    public function run(): void
    {
        $packs = [
            ['pro',   'Pro',    50,   4900],
            ['trade', 'Trade',  250, 19900],
            ['trade-500', 'Trade 500', 500, 36000],
            ['vip',   'VIP',    1000, 69900],
        ];
        foreach ($packs as [$slug, $name, $credits, $price]) {
            CreditPack::firstOrCreate(['slug' => $slug], ['name' => $name, 'credits' => $credits, 'price_pennies' => $price]);
        }
    }
}
