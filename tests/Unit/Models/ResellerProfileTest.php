<?php

namespace Tests\Unit\Models;

use App\Models\ResellerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResellerProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    public function test_can_add_customer_when_under_limit(): void
    {
        $reseller = User::factory()->create();
        $profile = ResellerProfile::factory()->create([
            'user_id'       => $reseller->id,
            'max_customers' => 5,
        ]);

        // Create 3 sub-customers linked to this reseller
        User::factory()->count(3)->create(['reseller_id' => $reseller->id]);

        $this->assertTrue($profile->canAddCustomer());
    }

    public function test_cannot_add_customer_at_limit(): void
    {
        $reseller = User::factory()->create();
        $profile = ResellerProfile::factory()->create([
            'user_id'       => $reseller->id,
            'max_customers' => 2,
        ]);

        User::factory()->count(2)->create(['reseller_id' => $reseller->id]);

        $this->assertFalse($profile->canAddCustomer());
    }

    public function test_can_always_add_when_unlimited(): void
    {
        $reseller = User::factory()->create();
        $profile = ResellerProfile::factory()->create([
            'user_id'       => $reseller->id,
            'max_customers' => 0,
        ]);

        User::factory()->count(100)->create(['reseller_id' => $reseller->id]);

        $this->assertTrue($profile->canAddCustomer());
    }
}
