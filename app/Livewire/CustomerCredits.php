<?php

namespace App\Livewire;

use App\Models\CreditPack;
use App\Models\CreditTransaction;
use App\Models\CustomerProfile;
use App\Models\Invoice;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CustomerCredits extends Component
{
    public ?int $selectedPack   = null;
    public ?string $selectedMethod = null;

    // Confirmation state after bank/invoice submission
    public ?string $confirmation = null;
    public ?string $bankReference = null;

    public function selectPack(int $id): void
    {
        $this->selectedPack = $id;
        $this->selectedMethod = null;
        $this->confirmation = null;
        $this->bankReference = null;
    }

    public function cancelSelection(): void
    {
        $this->selectedPack = null;
        $this->selectedMethod = null;
        $this->confirmation = null;
        $this->bankReference = null;
    }

    public function selectMethod(string $method): void
    {
        $pack = CreditPack::find($this->selectedPack);
        if (! $pack) return;

        if ($method === 'stripe') {
            // Redirect to the existing Stripe checkout route
            $this->redirect(route('app.checkout.start', $pack), navigate: false);
            return;
        }

        $this->selectedMethod = $method;

        if ($method === 'bank') {
            $this->bankReference = 'TF-' . auth()->id() . '-' . $pack->id;
        }
    }

    public function processBank(): void
    {
        $pack = CreditPack::find($this->selectedPack);
        if (! $pack) return;

        $user = auth()->user();
        $reference = 'TF-' . $user->id . '-' . $pack->id;

        $profile = $user->customerProfile
            ?? CustomerProfile::create(['user_id' => $user->id, 'plan' => 'Pro', 'credit_balance' => 0]);

        CreditTransaction::create([
            'user_id'        => $user->id,
            'credit_pack_id' => $pack->id,
            'type'           => 'purchase',
            'credits'        => $pack->credits,
            'balance_after'  => $profile->credit_balance, // unchanged until approved
            'amount_pennies' => $pack->price_pennies,
            'payment_method' => 'bank',
            'payment_status' => 'pending',
            'note'           => "Bank transfer pending — ref: {$reference}",
        ]);

        $this->confirmation = 'bank';
        $this->selectedMethod = null;
    }

    public function processInvoice(): void
    {
        $pack = CreditPack::find($this->selectedPack);
        if (! $pack) return;

        $user = auth()->user();
        $canInvoice = $user->customerProfile?->can_invoice ?? false;

        if (! $canInvoice) {
            $this->confirmation = null;
            return;
        }

        $terms = SiteSetting::get('gateway_invoice_terms', 'net_30');
        $days = (int) str_replace('net_', '', $terms);

        DB::transaction(function () use ($user, $pack, $days) {
            $profile = $user->customerProfile
                ?? CustomerProfile::create(['user_id' => $user->id, 'plan' => 'Pro', 'credit_balance' => 0]);

            $invoice = Invoice::create([
                'user_id'       => $user->id,
                'credit_pack_id' => $pack->id,
                'amount_pennies' => $pack->price_pennies,
                'credits'       => $pack->credits,
                'status'        => 'sent',
                'payment_terms' => "net_{$days}",
                'due_at'        => now()->addDays($days),
            ]);

            CreditTransaction::create([
                'user_id'        => $user->id,
                'credit_pack_id' => $pack->id,
                'type'           => 'purchase',
                'credits'        => $pack->credits,
                'balance_after'  => $profile->credit_balance, // unchanged until approved
                'amount_pennies' => $pack->price_pennies,
                'payment_method' => 'invoice',
                'payment_status' => 'pending',
                'note'           => "Invoice {$invoice->reference} — due in {$days} days",
            ]);
        });

        $this->confirmation = 'invoice';
        $this->selectedMethod = null;
    }

    public function render()
    {
        $packs = CreditPack::where('is_active', true)->orderBy('credits')->get();
        $tx = CreditTransaction::where('user_id', auth()->id())
            ->latest()->limit(15)->get();

        $user = auth()->user();

        return view('livewire.customer-credits', [
            'packs'   => $packs,
            'tx'      => $tx,
            'balance' => (int) ($user->customerProfile?->credit_balance ?? 0),
            'stripeEnabled'  => SiteSetting::get('gateway_stripe_enabled', 'true') === 'true',
            'bankEnabled'    => SiteSetting::get('gateway_bank_enabled', 'false') === 'true',
            'invoiceEnabled' => SiteSetting::get('gateway_invoice_enabled', 'false') === 'true'
                                && ($user->customerProfile?->can_invoice ?? false),
            'bankDetails'    => SiteSetting::get('gateway_bank_details', ''),
        ]);
    }
}
