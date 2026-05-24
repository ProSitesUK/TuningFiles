<?php

namespace Tests\Feature\Orders;

use App\Livewire\OrderDrawer;
use App\Models\CustomerProfile;
use App\Models\Order;
use App\Models\OrderEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OrderStatusTest extends TestCase
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

    private function makeCustomer(): User
    {
        $user = User::factory()->create();
        $user->syncRoles(['customer']);
        CustomerProfile::create([
            'user_id'        => $user->id,
            'plan'           => 'Pro',
            'credit_balance' => 100,
        ]);
        return $user;
    }

    private function makeOrder(User $customer): Order
    {
        return Order::create([
            'reference'    => '5001',
            'customer_id'  => $customer->id,
            'status'       => 'queued',
            'origin'       => 'customer upload',
            'credits_cost' => 32,
            'queued_at'    => now(),
        ]);
    }

    public function test_admin_can_change_status(): void
    {
        $admin = $this->makeAdmin();
        $customer = $this->makeCustomer();
        $order = $this->makeOrder($customer);

        Livewire::actingAs($admin)
            ->test(OrderDrawer::class)
            ->call('open', $order->id)
            ->call('changeStatus', 'in_progress');

        $order->refresh();
        $this->assertEquals('in_progress', $order->status);

        $this->assertDatabaseHas('order_events', [
            'order_id' => $order->id,
            'stage'    => 'status changed',
            'state'    => 'done',
        ]);
    }

    public function test_invalid_status_rejected(): void
    {
        $admin = $this->makeAdmin();
        $customer = $this->makeCustomer();
        $order = $this->makeOrder($customer);

        Livewire::actingAs($admin)
            ->test(OrderDrawer::class)
            ->call('open', $order->id)
            ->call('changeStatus', 'nonexistent');

        $order->refresh();
        $this->assertEquals('queued', $order->status);
    }

    public function test_non_admin_cannot_change_status(): void
    {
        $customer = $this->makeCustomer();
        $order = $this->makeOrder($customer);

        Livewire::actingAs($customer)
            ->test(OrderDrawer::class)
            ->call('open', $order->id)
            ->call('changeStatus', 'in_progress')
            ->assertForbidden();
    }
}
