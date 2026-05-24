<?php

namespace Tests\Feature\Credits;

use App\Livewire\AdminCredits;
use App\Models\CreditTransaction;
use App\Models\CustomerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ManualAdjustmentTest extends TestCase
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

    private function makeCustomerWithProfile(int $balance = 100): User
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

    public function test_admin_can_adjust_credits_positive(): void
    {
        $admin = $this->makeAdmin();
        $customer = $this->makeCustomerWithProfile(100);

        Livewire::actingAs($admin)
            ->test(AdminCredits::class)
            ->call('selectUser', $customer->id)
            ->set('adjCredits', '50')
            ->set('adjNote', 'Goodwill bonus')
            ->call('applyAdjustment');

        $customer->refresh();
        $this->assertEquals(150, $customer->customerProfile->credit_balance);

        $this->assertDatabaseHas('credit_transactions', [
            'user_id' => $customer->id,
            'type'    => 'adjust',
            'credits' => 50,
            'note'    => 'Goodwill bonus',
        ]);
    }

    public function test_admin_can_adjust_credits_negative(): void
    {
        $admin = $this->makeAdmin();
        $customer = $this->makeCustomerWithProfile(100);

        Livewire::actingAs($admin)
            ->test(AdminCredits::class)
            ->call('selectUser', $customer->id)
            ->set('adjCredits', '-20')
            ->set('adjNote', 'Overcharge correction')
            ->call('applyAdjustment');

        $customer->refresh();
        $this->assertEquals(80, $customer->customerProfile->credit_balance);
    }

    public function test_zero_adjustment_rejected(): void
    {
        $admin = $this->makeAdmin();
        $customer = $this->makeCustomerWithProfile(100);

        Livewire::actingAs($admin)
            ->test(AdminCredits::class)
            ->call('selectUser', $customer->id)
            ->set('adjCredits', '0')
            ->set('adjNote', 'Testing zero')
            ->call('applyAdjustment')
            ->assertHasErrors(['adjCredits']);
    }

    public function test_note_required(): void
    {
        $admin = $this->makeAdmin();
        $customer = $this->makeCustomerWithProfile(100);

        Livewire::actingAs($admin)
            ->test(AdminCredits::class)
            ->call('selectUser', $customer->id)
            ->set('adjCredits', '10')
            ->set('adjNote', '')
            ->call('applyAdjustment')
            ->assertHasErrors(['adjNote']);
    }
}
