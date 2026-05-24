<?php

namespace Tests\Feature\Credits;

use App\Http\Controllers\CheckoutController;
use App\Models\CreditPack;
use App\Models\CreditTransaction;
use App\Models\CustomerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreditPurchaseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    private function makePack(array $overrides = []): CreditPack
    {
        return CreditPack::create(array_merge([
            'slug'         => 'test-pack',
            'name'         => 'Test Pack',
            'credits'      => 100,
            'price_pennies' => 4900,
            'is_active'    => true,
        ], $overrides));
    }

    private function makeCustomer(): User
    {
        $user = User::factory()->create();
        $user->syncRoles(['customer']);
        return $user;
    }

    public function test_dev_grant_creates_profile_if_missing(): void
    {
        $user = $this->makeCustomer();
        $pack = $this->makePack(['credits' => 100]);

        $this->assertNull($user->customerProfile);

        $this->actingAs($user)
            ->post(route('app.checkout.start', $pack));

        $user->refresh();
        $this->assertNotNull($user->customerProfile);
        $this->assertEquals(100, $user->customerProfile->credit_balance);
    }

    public function test_dev_grant_increments_balance(): void
    {
        $user = $this->makeCustomer();
        CustomerProfile::create([
            'user_id'        => $user->id,
            'plan'           => 'Pro',
            'credit_balance' => 50,
        ]);

        $pack = $this->makePack(['credits' => 100]);

        $this->actingAs($user)
            ->post(route('app.checkout.start', $pack));

        $user->refresh();
        $this->assertEquals(150, $user->customerProfile->credit_balance);
    }

    public function test_dev_grant_creates_transaction(): void
    {
        $user = $this->makeCustomer();
        $pack = $this->makePack(['credits' => 100, 'price_pennies' => 4900]);

        $this->actingAs($user)
            ->post(route('app.checkout.start', $pack));

        $this->assertDatabaseHas('credit_transactions', [
            'user_id'        => $user->id,
            'type'           => 'purchase',
            'credits'        => 100,
            'amount_pennies' => 4900,
            'credit_pack_id' => $pack->id,
        ]);
    }

    public function test_dev_grant_idempotent_on_success_callback(): void
    {
        $user = $this->makeCustomer();
        $pack = $this->makePack(['credits' => 50]);

        // First call via the start route (dev mode)
        $this->actingAs($user)
            ->post(route('app.checkout.start', $pack));

        // Simulate hitting the success URL with a session_id
        $sessionId = 'cs_test_abc123';

        // First success call — the balance was already granted in start,
        // but success URL also tries to grant. We pass a session_id.
        $this->actingAs($user)
            ->get(route('app.checkout.success', ['pack' => $pack->id, 'session_id' => $sessionId]));

        // The success route should have created a transaction with the session_id
        $countAfterFirst = CreditTransaction::where('stripe_payment_intent', $sessionId)->count();

        // Second success call — same session_id — should NOT create a duplicate
        $this->actingAs($user)
            ->get(route('app.checkout.success', ['pack' => $pack->id, 'session_id' => $sessionId]));

        $countAfterSecond = CreditTransaction::where('stripe_payment_intent', $sessionId)->count();

        $this->assertEquals($countAfterFirst, $countAfterSecond);
    }

    public function test_balance_shows_on_dashboard(): void
    {
        $user = $this->makeCustomer();
        $pack = $this->makePack(['credits' => 75]);

        $this->actingAs($user)
            ->post(route('app.checkout.start', $pack));

        // Verify balance landed in DB (dashboard reads from this)
        $this->assertEquals(75, $user->fresh()->customerProfile->credit_balance);

        // Verify dashboard renders for this user
        $response = $this->actingAs($user->fresh())->get('/app');
        $response->assertOk();
        $response->assertSee('75');
    }
}
