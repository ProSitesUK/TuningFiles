<?php

namespace App\Http\Controllers;

use App\Models\CustomerProfile;
use App\Models\ResellerProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TenantAuthController extends Controller
{
    public function showLogin(ResellerProfile $tenant)
    {
        abort_unless($tenant->is_active, 404);

        return view('tenant.login', ['tenant' => $tenant]);
    }

    public function login(Request $request, ResellerProfile $tenant)
    {
        abort_unless($tenant->is_active, 404);

        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $user = Auth::user();

            // Verify user belongs to this tenant
            if ($user->reseller_id !== $tenant->user_id) {
                Auth::logout();

                return back()->withErrors(['email' => 'This account does not belong to this portal.']);
            }

            $request->session()->regenerate();

            return redirect()->route('tenant.dashboard', $tenant);
        }

        return back()->withErrors(['email' => 'Invalid credentials.']);
    }

    public function showRegister(ResellerProfile $tenant)
    {
        abort_unless($tenant->is_active && $tenant->canAddCustomer(), 404);

        return view('tenant.register', ['tenant' => $tenant]);
    }

    public function register(Request $request, ResellerProfile $tenant)
    {
        abort_unless($tenant->is_active && $tenant->canAddCustomer(), 404);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:10',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'reseller_id' => $tenant->user_id,
        ]);

        $user->syncRoles(['customer']);

        CustomerProfile::create([
            'user_id' => $user->id,
            'plan' => 'Pro',
            'credit_balance' => 0,
        ]);

        Auth::login($user);

        return redirect()->route('tenant.dashboard', $tenant);
    }
}
