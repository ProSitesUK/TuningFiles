<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        $user = User::firstOrCreate(
            ['email' => 'stuart@digitaldra.co.uk'],
            [
                'name'     => 'Stuart Elliot',
                'password' => Hash::make('TuningAdmin2026!'),
            ],
        );

        $user->syncRoles(['admin', 'operations']);
        $user->markEmailAsVerified();

        $this->command->info("Admin account ready: stuart@digitaldra.co.uk (change password on first login)");
    }
}
