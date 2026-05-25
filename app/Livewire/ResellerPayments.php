<?php

namespace App\Livewire;

use App\Models\CreditTransaction;
use App\Models\CustomerProfile;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ResellerPayments extends Component
{
    public string $filter = 'pending';
    public ?string $flash = null;

    public function approvePending(int $transactionId): void
    {
        $tx = CreditTransaction::findOrFail($transactionId);
        abort_unless($tx->user && $tx->user->reseller_id === auth()->id(), 403);

        if ($tx->payment_status !== 'pending') {
            $this->flash = 'Transaction is no longer pending.';
            return;
        }

        DB::transaction(function () use ($tx) {
            $tx->update(['payment_status' => 'completed']);

            $profile = $tx->user->customerProfile
                ?? CustomerProfile::create(['user_id' => $tx->user_id, 'plan' => 'Pro', 'credit_balance' => 0]);

            $profile->increment('credit_balance', $tx->credits);
            $profile->increment('total_spent_pennies', $tx->amount_pennies ?? 0);
            $tx->update(['balance_after' => $profile->credit_balance]);

            if ($tx->payment_method === 'invoice') {
                Invoice::where('user_id', $tx->user_id)
                    ->where('credits', $tx->credits)
                    ->whereIn('status', ['sent', 'draft'])
                    ->latest()
                    ->first()
                    ?->update(['status' => 'paid', 'paid_at' => now()]);
            }
        });

        $this->flash = "Approved — {$tx->credits} credits granted to {$tx->user->name}.";
    }

    public function rejectPending(int $transactionId): void
    {
        $tx = CreditTransaction::findOrFail($transactionId);
        abort_unless($tx->user && $tx->user->reseller_id === auth()->id(), 403);

        if ($tx->payment_status !== 'pending') {
            $this->flash = 'Transaction is no longer pending.';
            return;
        }

        $tx->update(['payment_status' => 'failed']);

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
        $subCustomerIds = User::where('reseller_id', auth()->id())->pluck('id');

        $q = CreditTransaction::whereIn('user_id', $subCustomerIds)
            ->whereIn('payment_method', ['bank', 'invoice'])
            ->with('user:id,name,email')
            ->orderByDesc('created_at');

        if ($this->filter === 'pending') {
            $q->where('payment_status', 'pending');
        } elseif ($this->filter !== 'all') {
            $q->where('payment_status', $this->filter);
        }

        $transactions = $q->get();
        $pendingCount = CreditTransaction::whereIn('user_id', $subCustomerIds)
            ->where('payment_status', 'pending')
            ->whereIn('payment_method', ['bank', 'invoice'])
            ->count();

        return view('livewire.reseller-payments', [
            'transactions' => $transactions,
            'pendingCount' => $pendingCount,
        ]);
    }
}
