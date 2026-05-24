<?php

namespace App\Livewire;

use App\Models\CreditPack;
use App\Models\CreditTransaction;
use App\Models\CustomerProfile;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AdminCredits extends Component
{
    // Credit pack form
    public ?int $editingId = null;
    public string $mode = 'list'; // list | form

    public array $form = [
        'name'            => '',
        'credits'         => '',
        'price_pennies'   => '',
        'stripe_price_id' => '',
        'is_active'       => true,
    ];

    // Manual adjustment
    public string $adjSearch   = '';
    public ?int   $adjUserId   = null;
    public string $adjUserName = '';
    public string $adjCredits  = '';
    public string $adjNote     = '';

    public ?string $flash = null;

    /* ---- Credit Pack CRUD ---- */
    public function newPack(): void
    {
        $this->mode = 'form';
        $this->editingId = null;
        $this->form = [
            'name' => '', 'credits' => '', 'price_pennies' => '',
            'stripe_price_id' => '', 'is_active' => true,
        ];
    }

    public function editPack(int $id): void
    {
        $pack = CreditPack::findOrFail($id);
        $this->mode = 'form';
        $this->editingId = $id;
        $this->form = [
            'name'            => $pack->name,
            'credits'         => (string) $pack->credits,
            'price_pennies'   => (string) $pack->price_pennies,
            'stripe_price_id' => $pack->stripe_price_id ?? '',
            'is_active'       => (bool) $pack->is_active,
        ];
    }

    public function savePack(): void
    {
        $this->validate([
            'form.name'            => 'required|string|max:80',
            'form.credits'         => 'required|integer|min:1',
            'form.price_pennies'   => 'required|integer|min:0',
            'form.stripe_price_id' => 'nullable|string|max:191',
            'form.is_active'       => 'boolean',
        ]);

        $attrs = [
            'name'            => $this->form['name'],
            'credits'         => (int) $this->form['credits'],
            'price_pennies'   => (int) $this->form['price_pennies'],
            'stripe_price_id' => $this->form['stripe_price_id'] ?: null,
            'is_active'       => (bool) $this->form['is_active'],
        ];

        if ($this->editingId) {
            CreditPack::findOrFail($this->editingId)->update($attrs);
            $this->flash = 'Credit pack updated.';
        } else {
            CreditPack::create($attrs);
            $this->flash = 'Credit pack created.';
        }

        $this->mode = 'list';
        $this->editingId = null;
    }

    public function cancelPack(): void
    {
        $this->mode = 'list';
        $this->editingId = null;
    }

    public function toggleActive(int $id): void
    {
        $pack = CreditPack::findOrFail($id);
        $pack->update(['is_active' => ! $pack->is_active]);
        $this->flash = $pack->is_active ? 'Pack activated.' : 'Pack archived.';
    }

    /* ---- Manual credit adjustment ---- */
    public function selectUser(int $userId): void
    {
        $user = User::find($userId);
        if ($user) {
            $this->adjUserId = $user->id;
            $this->adjUserName = $user->name.' ('.$user->email.')';
            $this->adjSearch = '';
        }
    }

    public function clearUser(): void
    {
        $this->adjUserId = null;
        $this->adjUserName = '';
    }

    public function applyAdjustment(): void
    {
        $this->validate([
            'adjUserId'  => 'required|exists:users,id',
            'adjCredits' => 'required|integer|not_in:0',
            'adjNote'    => 'required|string|max:255',
        ], [
            'adjCredits.not_in' => 'Credits amount cannot be zero.',
            'adjNote.required'  => 'A note is required for audit purposes.',
        ]);

        $user = User::findOrFail($this->adjUserId);
        $credits = (int) $this->adjCredits;

        // Create credit transaction
        CreditTransaction::create([
            'user_id'        => $user->id,
            'type'           => 'adjust',
            'credits'        => $credits,
            'amount_pennies' => 0,
            'note'           => trim($this->adjNote),
        ]);

        // Update customer profile balance
        $profile = $user->customerProfile;
        if ($profile) {
            $profile->increment('credit_balance', $credits);
        }

        $this->flash = "Adjustment of {$credits} credits applied to {$user->name}.";
        $this->adjUserId = null;
        $this->adjUserName = '';
        $this->adjCredits = '';
        $this->adjNote = '';
        $this->adjSearch = '';
    }

    /* ---- Pending payment approval / rejection ---- */
    public function approvePending(int $transactionId): void
    {
        $tx = CreditTransaction::findOrFail($transactionId);

        if ($tx->payment_status !== 'pending') {
            $this->flash = 'Transaction is no longer pending.';
            return;
        }

        DB::transaction(function () use ($tx) {
            $tx->update(['payment_status' => 'completed']);

            // Grant credits
            $profile = $tx->user->customerProfile
                ?? CustomerProfile::create(['user_id' => $tx->user_id, 'plan' => 'Pro']);

            $profile->increment('credit_balance', $tx->credits);
            $profile->increment('total_spent_pennies', $tx->amount_pennies ?? 0);

            // Update balance_after on the transaction
            $tx->update(['balance_after' => $profile->credit_balance]);

            // If this came from an invoice, mark the invoice as paid
            if ($tx->payment_method === 'invoice') {
                Invoice::where('user_id', $tx->user_id)
                    ->where('credits', $tx->credits)
                    ->whereIn('status', ['sent', 'draft'])
                    ->latest()
                    ->first()
                    ?->update(['status' => 'paid', 'paid_at' => now()]);
            }
        });

        $this->flash = "Payment approved — {$tx->credits} credits granted to {$tx->user->name}.";
    }

    public function rejectPending(int $transactionId): void
    {
        $tx = CreditTransaction::findOrFail($transactionId);

        if ($tx->payment_status !== 'pending') {
            $this->flash = 'Transaction is no longer pending.';
            return;
        }

        $tx->update(['payment_status' => 'failed']);

        // If from invoice, cancel the invoice
        if ($tx->payment_method === 'invoice') {
            Invoice::where('user_id', $tx->user_id)
                ->where('credits', $tx->credits)
                ->whereIn('status', ['sent', 'draft'])
                ->latest()
                ->first()
                ?->update(['status' => 'cancelled']);
        }

        $this->flash = "Payment rejected for {$tx->user->name}.";
    }

    public function render()
    {
        $packs = CreditPack::orderByDesc('is_active')->orderBy('credits')->get();

        // User search results for adjustment
        $searchResults = collect();
        if (strlen($this->adjSearch) >= 2 && ! $this->adjUserId) {
            $needle = $this->adjSearch;
            $searchResults = User::role('customer')
                ->where(function ($q) use ($needle) {
                    $q->where('name', 'like', "%{$needle}%")
                      ->orWhere('email', 'like', "%{$needle}%");
                })->limit(8)->get(['id', 'name', 'email']);
        }

        // Pending payments: bank transfers + invoices awaiting approval
        $pendingTransactions = CreditTransaction::where('payment_status', 'pending')
            ->with('user:id,name,email')
            ->latest()
            ->get();

        $pendingInvoices = Invoice::whereIn('status', ['sent', 'overdue'])
            ->with('user:id,name,email')
            ->latest()
            ->get();

        return view('livewire.admin-credits', [
            'packs'               => $packs,
            'searchResults'       => $searchResults,
            'pendingTransactions' => $pendingTransactions,
            'pendingInvoices'     => $pendingInvoices,
        ]);
    }
}
