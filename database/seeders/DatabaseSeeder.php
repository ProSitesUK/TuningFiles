<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        $admin = User::firstOrCreate(
            ['email' => 'admin@tuningfiles.test'],
            ['name' => 'Sam Okafor', 'password' => Hash::make('password')],
        );
        $admin->syncRoles(['admin', 'operations']);

        $tuner = User::firstOrCreate(
            ['email' => 'aleks@tuningfiles.test'],
            ['name' => 'Aleks R.', 'password' => Hash::make('password')],
        );
        $tuner->syncRoles(['tuner']);

        $customer = User::firstOrCreate(
            ['email' => 'jamie@example.com'],
            ['name' => 'Jamie Marshall', 'password' => Hash::make('password')],
        );
        $customer->syncRoles(['customer']);
    }
}
