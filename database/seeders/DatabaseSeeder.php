<?php

namespace Database\Seeders;

use App\Models\CustomerProfile;
use App\Models\TunerProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RoleSeeder::class);
        $this->call(VehicleSeeder::class);
        $this->call(EcuSeeder::class);
        $this->call(TuneSeeder::class);
        $this->call(CreditPackSeeder::class);

        $admin = User::firstOrCreate(
            ['email' => 'admin@tuningfiles.test'],
            ['name' => 'Sam Okafor', 'password' => Hash::make('password')],
        );
        $admin->syncRoles(['admin', 'operations']);

        $this->call(DemoSeeder::class);

        User::role('customer')->each(fn ($u) => CustomerProfile::firstOrCreate(['user_id' => $u->id], ['plan' => 'Pro']));
        User::role('tuner')->each(fn ($u)    => TunerProfile::firstOrCreate(['user_id' => $u->id],    ['status' => 'off']));
    }
}
