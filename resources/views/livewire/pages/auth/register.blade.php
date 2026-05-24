<?php

use App\Models\Referral;
use App\Models\ResellerProfile;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public int $step = 1;
    public string $name = '';
    public string $workshop = '';
    public string $email = '';
    public string $password = '';
    public string $plan = 'Pro';
    public string $country = 'United Kingdom';
    public bool $agreed = true;
    public string $ref = '';
    public ?string $refBusinessName = null;
    public ?int $referrerUserId = null;

    public function mount(): void
    {
        $this->ref = (string) request()->query('ref', '');

        if ($this->ref) {
            // First try reseller profile slug
            $rp = ResellerProfile::where('slug', $this->ref)->where('is_active', true)->first();
            $this->refBusinessName = $rp?->business_name;

            // If not a reseller, try user referral code
            if (! $rp) {
                $referrer = User::where('referral_code', $this->ref)->first();
                if ($referrer) {
                    $this->referrerUserId = $referrer->id;
                    $this->refBusinessName = $referrer->name . ' (referral)';
                }
            }
        }
    }

    public function next(): void
    {
        $this->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'min:10'],
        ]);
        $this->step = 2;
    }

    public function back(): void { $this->step = 1; }

    public function register(): void
    {
        $this->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', Rules\Password::defaults()],
            'plan'     => ['required', 'in:Pro,Trade,VIP'],
            'agreed'   => ['accepted'],
        ]);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);
        $user->syncRoles(['customer']);

        // Link to reseller if ref param was provided
        if ($this->ref) {
            $rp = ResellerProfile::where('slug', $this->ref)->where('is_active', true)->first();
            if ($rp && $rp->canAddCustomer()) {
                $user->update(['reseller_id' => $rp->user_id]);
            }
        }

        // Create referral record if referred by a user
        if ($this->referrerUserId) {
            Referral::create([
                'referrer_id' => $this->referrerUserId,
                'referred_id' => $user->id,
                'status'      => 'pending',
            ]);
        }

        event(new Registered($user));
        Auth::login($user);
        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <h1 class="auth-title">Open a workshop account</h1>
    <p class="auth-sub">Step {{ $step }} of 2 · takes about a minute.</p>

    @if ($refBusinessName)
        <div class="reseller-badge" style="margin-bottom:16px">
            <span class="badge-dot" style="background:var(--accent)"></span>
            <span>Joining via {{ $refBusinessName }}</span>
        </div>
    @endif

    <div class="auth-steps">
        <span class="auth-step {{ $step >= 1 ? 'auth-step-on' : '' }}"></span>
        <span class="auth-step {{ $step >= 2 ? 'auth-step-on' : '' }}"></span>
    </div>

    <form wire:submit="{{ $step === 1 ? 'next' : 'register' }}" class="auth-form">
        @if ($step === 1)
            <div class="auth-row-2">
                <label class="auth-field">
                    <span>Your name</span>
                    <input wire:model="name" placeholder="Sam Okafor" autofocus required />
                    @error('name')<span class="auth-hint" style="color:var(--danger)">{{ $message }}</span>@enderror
                </label>
                <label class="auth-field">
                    <span>Workshop</span>
                    <input wire:model="workshop" placeholder="Bristol Motorworks" />
                </label>
            </div>
            <label class="auth-field">
                <span>Email</span>
                <input wire:model="email" type="email" placeholder="you@workshop.co.uk" required />
                @error('email')<span class="auth-hint" style="color:var(--danger)">{{ $message }}</span>@enderror
            </label>
            <label class="auth-field">
                <span>Password</span>
                <input wire:model="password" type="password" placeholder="At least 10 characters" required minlength="10" />
                <span class="auth-hint">Use a passphrase. We will send a magic link as backup.</span>
                @error('password')<span class="auth-hint" style="color:var(--danger)">{{ $message }}</span>@enderror
            </label>
            <button type="submit" class="primary-btn primary-btn-lg auth-submit">Continue →</button>
        @else
            <div class="auth-field">
                <span>Pick a plan</span>
                <div class="auth-plans">
                    @foreach ([
                        ['Pro',   '£32 / file', 'pay-as-you-go · best to start'],
                        ['Trade', '£24 / file', '50-pack · 30+ files / month'],
                        ['VIP',   'custom',     'dedicated tuners · contact us'],
                    ] as [$id, $price, $sub])
                        <button type="button" wire:click="$set('plan', '{{ $id }}')"
                                class="auth-plan {{ $plan === $id ? 'auth-plan-on' : '' }}">
                            <div class="auth-plan-head">
                                <span>{{ $id }}</span>
                                <span class="auth-plan-price mono">{{ $price }}</span>
                            </div>
                            <div class="auth-plan-sub small">{{ $sub }}</div>
                        </button>
                    @endforeach
                </div>
            </div>

            <label class="auth-field">
                <span>Country</span>
                <select wire:model="country">
                    <option>United Kingdom</option>
                    <option>Germany</option>
                    <option>France</option>
                    <option>Spain</option>
                    <option>United States</option>
                    <option>Other</option>
                </select>
            </label>

            <label class="auth-check">
                <input wire:model="agreed" type="checkbox" required />
                <span>I agree to the <a href="#">terms of service</a> and <a href="#">tuner agreement</a>.</span>
            </label>

            <div class="auth-row-2">
                <button type="button" wire:click="back" class="ghost-btn ghost-btn-lg auth-submit">← Back</button>
                <button type="submit" class="primary-btn primary-btn-lg auth-submit">Create account →</button>
            </div>
        @endif

        <p class="auth-foot">
            Already have an account? <a href="{{ route('login') }}" wire:navigate>Sign in</a>
        </p>
    </form>
</div>
