<?php

namespace Tests\Feature\Credits;

use App\Livewire\AdminCredits;
use App\Livewire\CustomerCredits;
use App\Models\CreditPack;
use App\Models\CreditTransaction;
use App\Models\CustomerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BankTransferTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    private function makePack(): CreditPack
    {
        return CreditPack::create([
            'slug'          => 'bank-test',
            'name'          => 'Bank Test Pack',
            'credits'       => 100,
            'price_pennies' => 4900,
            'is_active'     => true,
        ]);
    }

    private function makeCustomer(array $profileAttrs = []): User
    {
        $user = User::factory()->create();
        $user->syncRoles(['customer']);
        if ($profileAttrs !== []) {
            CustomerProfile::create(array_merge(['user_id' => $user->id, 'plan' => 'Pro'], $profileAttrs));
        }
        return $user;
    }

    private function makeAdmin(): User
    {
        $admin = User::factory()->create();
        $admin->syncRoles(['admin']);
        return $admin;
    }

    public function test_bank_transfer_creates_pending_transaction(): void
    {
        $user = $this->makeCustomer();
        $pack = $this->makePack();

        Livewire::actingAs($user)
            ->test(CustomerCredits::class)
            ->call('selectPack', $pack->id)
            ->call('selectMethod', 'bank')
            ->call('processBank');

        $this->assertDatabaseHas('credit_transactions', [
            'user_id'        => $user->id,
            'credit_pack_id' => $pack->id,
            'payment_method' => 'bank',
            'payment_status' => 'pending',
        ]);
    }

    public function test_admin_can_approve_pending(): void
    {
        $customer = $this->makeCustomer(['credit_balance' => 0]);
        $pack = $this->makePack();
        $admin = $this->makeAdmin();

        $tx = CreditTransaction::create([
            'user_id'        => $customer->id,
            'credit_pack_id' => $pack->id,
            'type'           => 'purchase',
            'credits'        => 100,
            'balance_after'  => 0,
            'amount_pennies' => 4900,
            'payment_method' => 'bank',
            'payment_status' => 'pending',
            'note'           => 'Bank transfer pending',
        ]);

        Livewire::actingAs($admin)
            ->test(AdminCredits::class)
            ->call('approvePending', $tx->id);

        $tx->refresh();
        $this->assertEquals('completed', $tx->payment_status);

        $customer->refresh();
        $this->assertEquals(100, $customer->customerProfile->credit_balance);
    }

    public function test_admin_can_reject_pending(): void
    {
        $customer = $this->makeCustomer(['credit_balance' => 50]);
        $pack = $this->makePack();
        $admin = $this->makeAdmin();

        $tx = CreditTransaction::create([
            'user_id'        => $customer->id,
            'credit_pack_id' => $pack->id,
            'type'           => 'purchase',
            'credits'        => 100,
            'balance_after'  => 50,
            'amount_pennies' => 4900,
            'payment_method' => 'bank',
            'payment_status' => 'pending',
            'note'           => 'Bank transfer pending',
        ]);

        Livewire::actingAs($admin)
            ->test(AdminCredits::class)
            ->call('rejectPending', $tx->id);

        $tx->refresh();
        $this->assertEquals('failed', $tx->payment_status);

        $customer->refresh();
        $this->assertEquals(50, $customer->customerProfile->credit_balance);
    }
}
