<?php

namespace App\Livewire;

use App\Models\CustomerProfile;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Component;

class ResellerInvite extends Component
{
    public string $name = '';
    public string $email = '';

    public function createAccount(): void
    {
        $this->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
        ]);

        $profile = auth()->user()->resellerProfile;

        if ($profile && ! $profile->canAddCustomer()) {
            session()->flash('error', 'You have reached the maximum number of customers for your plan.');
            return;
        }

        $user = User::create([
            'name'        => $this->name,
            'email'       => $this->email,
            'password'    => Hash::make(Str::random(16)),
            'reseller_id' => auth()->id(),
        ]);

        $user->syncRoles(['customer']);

        CustomerProfile::firstOrCreate(
            ['user_id' => $user->id],
            ['plan' => 'Pro']
        );

        $this->reset('name', 'email');
        session()->flash('message', "Account created for {$user->name} ({$user->email}). They can reset their password to log in.");
    }

    public function render()
    {
        $profile = auth()->user()->resellerProfile;
        $inviteLink = $profile?->slug
            ? url("/register?ref={$profile->slug}")
            : null;

        return view('livewire.reseller-invite', [
            'inviteLink' => $inviteLink,
        ]);
    }
}
