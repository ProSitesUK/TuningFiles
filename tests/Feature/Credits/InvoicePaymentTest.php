<?php

namespace Tests\Feature\Credits;

use App\Livewire\AdminCredits;
use App\Livewire\CustomerCredits;
use App\Models\CreditPack;
use App\Models\CreditTransaction;
use App\Models\CustomerProfile;
use App\Models\Invoice;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class InvoicePaymentTest extends TestCase
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
            'slug'          => 'inv-test',
            'name'          => 'Invoice Test Pack',
            'credits'       => 200,
            'price_pennies' => 9900,
            'is_active'     => true,
        ]);
    }

    private function makeCustomer(bool $canInvoice = true): User
    {
        $user = User::factory()->create();
        $user->syncRoles(['customer']);
        CustomerProfile::create([
            'user_id'        => $user->id,
            'plan'           => 'Pro',
            'credit_balance' => 0,
            'can_invoice'    => $canInvoice,
        ]);
        return $user;
    }

    private function makeAdmin(): User
    {
        $admin = User::factory()->create();
        $admin->syncRoles(['admin']);
        return $admin;
    }

    public function test_invoice_available_when_can_invoice(): void
    {
        $user = $this->makeCustomer(canInvoice: true);
        $pack = $this->makePack();

        // Enable invoice gateway
        SiteSetting::put('gateway_invoice_enabled', 'true');

        $component = Livewire::actingAs($user)
            ->test(CustomerCredits::class);

        // The rendered view data should include invoiceEnabled = true
        $component->assertViewHas('invoiceEnabled', true);
    }

    public function test_invoice_hidden_when_cannot(): void
    {
        $user = $this->makeCustomer(canInvoice: false);
        $pack = $this->makePack();

        SiteSetting::put('gateway_invoice_enabled', 'true');

        $component = Livewire::actingAs($user)
            ->test(CustomerCredits::class);

        $component->assertViewHas('invoiceEnabled', false);
    }

    public function test_invoice_creates_record(): void
    {
        $user = $this->makeCustomer(canInvoice: true);
        $pack = $this->makePack();

        SiteSetting::put('gateway_invoice_enabled', 'true');
        SiteSetting::put('gateway_invoice_terms', 'net_30');

        Livewire::actingAs($user)
            ->test(CustomerCredits::class)
            ->call('selectPack', $pack->id)
            ->call('selectMethod', 'invoice')
            ->call('processInvoice');

        // Invoice record should exist
        $this->assertDatabaseHas('invoices', [
            'user_id'        => $user->id,
            'credit_pack_id' => $pack->id,
            'credits'        => 200,
            'status'         => 'sent',
            'payment_terms'  => 'net_30',
        ]);

        // Check that due_at is set (approximately 30 days from now)
        $invoice = Invoice::where('user_id', $user->id)->first();
        $this->assertNotNull($invoice->due_at);

        // CreditTransaction with payment_method=invoice
        $this->assertDatabaseHas('credit_transactions', [
            'user_id'        => $user->id,
            'payment_method' => 'invoice',
            'payment_status' => 'pending',
            'credits'        => 200,
        ]);
    }

    public function test_approve_invoice_marks_paid(): void
    {
        $customer = $this->makeCustomer(canInvoice: true);
        $pack = $this->makePack();
        $admin = $this->makeAdmin();

        // Create invoice and pending transaction
        $invoice = Invoice::create([
            'user_id'        => $customer->id,
            'credit_pack_id' => $pack->id,
            'amount_pennies' => 9900,
            'credits'        => 200,
            'status'         => 'sent',
            'payment_terms'  => 'net_30',
            'due_at'         => now()->addDays(30),
        ]);

        $tx = CreditTransaction::create([
            'user_id'        => $customer->id,
            'credit_pack_id' => $pack->id,
            'type'           => 'purchase',
            'credits'        => 200,
            'balance_after'  => 0,
            'amount_pennies' => 9900,
            'payment_method' => 'invoice',
            'payment_status' => 'pending',
            'note'           => "Invoice {$invoice->reference}",
        ]);

        Livewire::actingAs($admin)
            ->test(AdminCredits::class)
            ->call('approvePending', $tx->id);

        $invoice->refresh();
        $this->assertEquals('paid', $invoice->status);
        $this->assertNotNull($invoice->paid_at);

        $customer->refresh();
        $this->assertEquals(200, $customer->customerProfile->credit_balance);
    }
}
