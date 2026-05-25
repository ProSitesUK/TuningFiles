<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class TenantSubscriptionController extends Controller
{
    public function plans()
    {
        $plans = SubscriptionPlan::active()->orderBy('sort_order')->get();
        $currentPlan = auth()->user()->resellerProfile?->subscription_status;

        return view('reseller.plans', [
            'plans'       => $plans,
            'currentPlan' => $currentPlan,
        ]);
    }

    public function subscribe(SubscriptionPlan $plan)
    {
        // Dev mode: no Stripe -- just activate
        if (!config('cashier.secret')) {
            $profile = auth()->user()->resellerProfile;
            $profile->update([
                'subscription_status' => 'active',
                'max_customers'       => $plan->max_customers,
            ]);

            return redirect()->route('reseller.billing')->with('status', 'Subscription activated (dev mode).');
        }

        // Production: Stripe Checkout
        return auth()->user()->newSubscription('default', $plan->stripe_price_id)
            ->checkout([
                'success_url' => route('reseller.billing') . '?subscribed=1',
                'cancel_url'  => route('reseller.plans'),
            ]);
    }

    public function billing()
    {
        $profile = auth()->user()->resellerProfile;
        $plan = $profile
            ? SubscriptionPlan::where('max_customers', $profile->max_customers)->first()
            : null;

        return view('reseller.billing', [
            'profile' => $profile,
            'plan'    => $plan,
        ]);
    }

    public function cancel()
    {
        if (config('cashier.secret') && auth()->user()->subscription('default')) {
            auth()->user()->subscription('default')->cancel();
        }

        auth()->user()->resellerProfile?->update(['subscription_status' => 'cancelled']);

        return redirect()->route('reseller.billing')->with('status', 'Subscription cancelled.');
    }
}
