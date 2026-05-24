<?php

namespace App\Http\Controllers;

use App\Models\CreditPack;
use App\Models\CreditTransaction;
use App\Models\CustomerProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    /** Start a Stripe Checkout session for the given pack. */
    public function start(Request $request, CreditPack $pack)
    {
        $user = $request->user();
        abort_unless($user, 401);

        // If the pack doesn't have a real stripe_price_id, fall back to dev mode:
        // simulate the purchase server-side so the dashboard works end-to-end without
        // a Stripe account. Replace with real Cashier checkout once STRIPE_KEY is set.
        if (! $pack->stripe_price_id || ! config('cashier.secret')) {
            $this->devGrant($user, $pack);
            return redirect()->route('app.credits')->with('status', "Granted {$pack->credits} credits (dev mode — Stripe not configured).");
        }

        return $user->checkoutCharge(
            $pack->price_pennies,
            "Credit pack: {$pack->name}",
            1,
            [
                'success_url' => route('checkout.success').'?session_id={CHECKOUT_SESSION_ID}&pack='.$pack->id,
                'cancel_url'  => route('app.credits'),
                'metadata'    => ['credit_pack_id' => $pack->id, 'user_id' => $user->id],
            ]
        );
    }

    public function success(Request $request)
    {
        $packId = (int) $request->query('pack');
        $pack   = $packId ? CreditPack::find($packId) : null;
        $user   = $request->user();

        if ($pack && $user) {
            // In production the webhook does the grant; here we double-grant-safely via session_id.
            $sessionId = $request->query('session_id');
            $exists = CreditTransaction::where('stripe_payment_intent', $sessionId)->exists();
            if (! $exists) {
                $this->devGrant($user, $pack, $sessionId);
            }
        }

        return redirect()->route('app.credits')->with('status', 'Credits added — thank you!');
    }

    private function devGrant($user, CreditPack $pack, ?string $stripeRef = null): void
    {
        DB::transaction(function () use ($user, $pack, $stripeRef) {
            $profile = $user->customerProfile
                ?? CustomerProfile::create(['user_id' => $user->id, 'plan' => 'Pro']);

            $profile->increment('credit_balance', $pack->credits);
            $profile->increment('total_spent_pennies', $pack->price_pennies);

            CreditTransaction::create([
                'user_id'               => $user->id,
                'credit_pack_id'        => $pack->id,
                'type'                  => 'purchase',
                'credits'               => $pack->credits,
                'balance_after'         => $profile->credit_balance,
                'amount_pennies'        => $pack->price_pennies,
                'stripe_payment_intent' => $stripeRef,
                'note'                  => "Credit pack: {$pack->name}",
            ]);
        });
    }
}
