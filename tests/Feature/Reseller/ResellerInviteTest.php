<?php

namespace Tests\Feature\Reseller;

use App\Livewire\ResellerInvite;
use App\Models\CustomerProfile;
use App\Models\ResellerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ResellerInviteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    private function makeReseller(int $maxCustomers = 0): User
    {
        $user = User::factory()->create();
        $user->syncRoles(['reseller']);
        ResellerProfile::create([
            'user_id'       => $user->id,
            'business_name' => 'Test Reseller',
            'slug'          => 'test-reseller',
            'max_customers' => $maxCustomers,
            'is_active'     => true,
        ]);
        return $user;
    }

    public function test_reseller_can_create_sub_customer(): void
    {
        $reseller = $this->makeReseller();

        Livewire::actingAs($reseller)
            ->test(ResellerInvite::class)
            ->set('name', 'New Customer')
            ->set('email', 'newcustomer@example.com')
            ->call('createAccount');

        $newUser = User::where('email', 'newcustomer@example.com')->first();
        $this->assertNotNull($newUser);
        $this->assertEquals($reseller->id, $newUser->reseller_id);
    }

    public function test_sub_customer_gets_customer_role(): void
    {
        $reseller = $this->makeReseller();

        Livewire::actingAs($reseller)
            ->test(ResellerInvite::class)
            ->set('name', 'Role Test Customer')
            ->set('email', 'rolecustomer@example.com')
            ->call('createAccount');

        $newUser = User::where('email', 'rolecustomer@example.com')->first();
        $this->assertNotNull($newUser);
        $this->assertTrue($newUser->hasRole('customer'));
    }

    public function test_reseller_at_limit_cannot_add(): void
    {
        $reseller = $this->makeReseller(maxCustomers: 1);

        // Create one sub-customer manually
        $existing = User::factory()->create(['reseller_id' => $reseller->id]);
        $existing->syncRoles(['customer']);

        Livewire::actingAs($reseller)
            ->test(ResellerInvite::class)
            ->set('name', 'Over Limit Customer')
            ->set('email', 'overlimit@example.com')
            ->call('createAccount');

        // Should NOT have created the user
        $this->assertNull(User::where('email', 'overlimit@example.com')->first());
    }
}
