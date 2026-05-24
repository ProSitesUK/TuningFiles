<?php

namespace Tests\Feature\Admin;

use App\Livewire\AdminDisputes;
use App\Models\CustomerProfile;
use App\Models\Dispute;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminDisputeTest extends TestCase
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

    private function makeDispute(): Dispute
    {
        $customer = User::factory()->create();
        $customer->syncRoles(['customer']);

        $order = Order::create([
            'reference'    => '8001',
            'customer_id'  => $customer->id,
            'status'       => 'dispute',
            'origin'       => 'customer upload',
            'credits_cost' => 32,
            'queued_at'    => now(),
        ]);

        return Dispute::create([
            'order_id'     => $order->id,
            'opened_by_id' => $customer->id,
            'reason'       => 'File not working',
            'status'       => 'open',
            'description'  => 'The tuned file causes engine light.',
        ]);
    }

    public function test_mark_investigating(): void
    {
        $admin = $this->makeAdmin();
        $dispute = $this->makeDispute();

        Livewire::actingAs($admin)
            ->test(AdminDisputes::class)
            ->call('selectDispute', $dispute->id)
            ->call('markInvestigating');

        $dispute->refresh();
        $this->assertEquals('investigating', $dispute->status);
    }

    public function test_resolve_requires_resolution(): void
    {
        $admin = $this->makeAdmin();
        $dispute = $this->makeDispute();

        Livewire::actingAs($admin)
            ->test(AdminDisputes::class)
            ->call('selectDispute', $dispute->id)
            ->set('resolution', '')
            ->call('resolve');

        // Since resolution is empty, the resolve method returns early without changing status
        $dispute->refresh();
        $this->assertNotEquals('resolved', $dispute->status);
        $this->assertEquals('open', $dispute->status);
    }

    public function test_resolve_with_note(): void
    {
        $admin = $this->makeAdmin();
        $dispute = $this->makeDispute();

        Livewire::actingAs($admin)
            ->test(AdminDisputes::class)
            ->call('selectDispute', $dispute->id)
            ->set('resolution', 'Replaced file with corrected version. Issue was wrong ECU mapping.')
            ->call('resolve');

        $dispute->refresh();
        $this->assertEquals('resolved', $dispute->status);
        $this->assertEquals('Replaced file with corrected version. Issue was wrong ECU mapping.', $dispute->resolution);
        $this->assertNotNull($dispute->resolved_at);
    }
}
