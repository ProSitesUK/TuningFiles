<?php

namespace Tests\Feature\Reseller;

use App\Livewire\ResellerDashboard;
use App\Livewire\ResellerOrders;
use App\Models\CustomerProfile;
use App\Models\Order;
use App\Models\ResellerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ResellerDataIsolationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    private function makeReseller(string $slug): User
    {
        $user = User::factory()->create();
        $user->syncRoles(['reseller']);
        ResellerProfile::create([
            'user_id'       => $user->id,
            'business_name' => "Reseller {$slug}",
            'slug'          => $slug,
            'max_customers' => 0,
            'is_active'     => true,
        ]);
        return $user;
    }

    private function makeSubCustomer(User $reseller): User
    {
        $user = User::factory()->create(['reseller_id' => $reseller->id]);
        $user->syncRoles(['customer']);
        CustomerProfile::create([
            'user_id'        => $user->id,
            'plan'           => 'Pro',
            'credit_balance' => 100,
        ]);
        return $user;
    }

    private function makeOrder(User $customer, User $reseller): Order
    {
        static $refCounter = 7000;
        return Order::create([
            'reference'    => (string) $refCounter++,
            'customer_id'  => $customer->id,
            'reseller_id'  => $reseller->id,
            'status'       => 'queued',
            'origin'       => 'customer upload',
            'credits_cost' => 32,
            'queued_at'    => now(),
        ]);
    }

    public function test_reseller_only_sees_own_customers(): void
    {
        $resellerA = $this->makeReseller('reseller-a');
        $resellerB = $this->makeReseller('reseller-b');

        $customerA = $this->makeSubCustomer($resellerA);
        $customerB = $this->makeSubCustomer($resellerB);

        // Reseller A dashboard should count only their customer
        $componentA = Livewire::actingAs($resellerA)
            ->test(ResellerDashboard::class);

        $componentA->assertViewHas('totalCustomers', 1);

        // Reseller B dashboard should count only their customer
        $componentB = Livewire::actingAs($resellerB)
            ->test(ResellerDashboard::class);

        $componentB->assertViewHas('totalCustomers', 1);
    }

    public function test_reseller_only_sees_own_orders(): void
    {
        $resellerA = $this->makeReseller('reseller-x');
        $resellerB = $this->makeReseller('reseller-y');

        $customerA = $this->makeSubCustomer($resellerA);
        $customerB = $this->makeSubCustomer($resellerB);

        $orderA = $this->makeOrder($customerA, $resellerA);
        $orderB = $this->makeOrder($customerB, $resellerB);

        // Reseller A orders page
        $componentA = Livewire::actingAs($resellerA)
            ->test(ResellerOrders::class);

        $orders = $componentA->viewData('orders');
        $orderIds = $orders->pluck('id')->toArray();
        $this->assertContains($orderA->id, $orderIds);
        $this->assertNotContains($orderB->id, $orderIds);

        // Reseller B orders page
        $componentB = Livewire::actingAs($resellerB)
            ->test(ResellerOrders::class);

        $orders = $componentB->viewData('orders');
        $orderIds = $orders->pluck('id')->toArray();
        $this->assertContains($orderB->id, $orderIds);
        $this->assertNotContains($orderA->id, $orderIds);
    }
}
