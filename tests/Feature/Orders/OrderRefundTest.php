<?php

namespace Tests\Feature\Orders;

use App\Livewire\OrderDrawer;
use App\Models\CreditTransaction;
use App\Models\CustomerProfile;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OrderRefundTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    private function makeAdmin(): User
    {
        $admin = User::factory()->create();
        $admin->syncRoles(['admin']);
        return $admin;
    }

    private function makeCustomer(int $balance = 68): User
    {
        $user = User::factory()->create();
        $user->syncRoles(['customer']);
        CustomerProfile::create([
            'user_id'        => $user->id,
            'plan'           => 'Pro',
            'credit_balance' => $balance,
        ]);
        return $user;
    }

    private function makeOrder(User $customer, int $creditsCost = 32): Order
    {
        return Order::create([
            'reference'    => '6001',
            'customer_id'  => $customer->id,
            'status'       => 'queued',
            'origin'       => 'customer upload',
            'credits_cost' => $creditsCost,
            'queued_at'    => now(),
        ]);
    }

    public function test_refund_restores_credits(): void
    {
        $admin = $this->makeAdmin();
        $customer = $this->makeCustomer(68);
        $order = $this->makeOrder($customer, 32);

        Livewire::actingAs($admin)
            ->test(OrderDrawer::class)
            ->call('open', $order->id)
            ->call('refund');

        $customer->refresh();
        $this->assertEquals(100, $customer->customerProfile->credit_balance);
    }

    public function test_refund_creates_transaction(): void
    {
        $admin = $this->makeAdmin();
        $customer = $this->makeCustomer(68);
        $order = $this->makeOrder($customer, 32);

        Livewire::actingAs($admin)
            ->test(OrderDrawer::class)
            ->call('open', $order->id)
            ->call('refund');

        $this->assertDatabaseHas('credit_transactions', [
            'user_id'  => $customer->id,
            'order_id' => $order->id,
            'type'     => 'refund',
            'credits'  => 32,
        ]);
    }

    public function test_refund_sets_status_refunded(): void
    {
        $admin = $this->makeAdmin();
        $customer = $this->makeCustomer(68);
        $order = $this->makeOrder($customer, 32);

        Livewire::actingAs($admin)
            ->test(OrderDrawer::class)
            ->call('open', $order->id)
            ->call('refund');

        $order->refresh();
        $this->assertEquals('refunded', $order->status);
        $this->assertNotNull($order->refunded_at);
    }
}
