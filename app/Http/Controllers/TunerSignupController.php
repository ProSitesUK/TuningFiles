<?php

namespace App\Http\Controllers;

use App\Models\CustomerProfile;
use App\Models\ResellerProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TunerSignupController extends Controller
{
    public function show()
    {
        $plans = \App\Models\SubscriptionPlan::active()->orderBy('sort_order')->get();
        return view('marketing.become-a-tuner', ['plans' => $plans]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:100',
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users',
            'password'      => 'required|string|min:10',
            'website'       => 'nullable|url|max:255',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->syncRoles(['reseller']);
        $user->markEmailAsVerified();

        CustomerProfile::create([
            'user_id'        => $user->id,
            'plan'           => 'Pro',
            'credit_balance' => 0,
        ]);

        $slug = Str::slug($request->business_name);
        // Ensure unique slug
        $baseSlug = $slug;
        $counter = 1;
        while (ResellerProfile::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        ResellerProfile::create([
            'user_id'       => $user->id,
            'business_name' => $request->business_name,
            'slug'          => $slug,
            'website'       => $request->website,
            'is_active'     => true,
        ]);

        Auth::login($user);

        return redirect()->route('reseller.plans')->with('status', 'Welcome! Pick a plan to get started.');
    }
}
