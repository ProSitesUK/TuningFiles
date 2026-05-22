<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    public function login(): void
    {
        $this->validate();
        $this->form->authenticate();
        Session::regenerate();
        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <h1 class="auth-title">Welcome back</h1>
    <p class="auth-sub">Sign in to your workshop dashboard.</p>

    @if (session('status'))
        <div class="mb-4 small" style="color:var(--success)">{{ session('status') }}</div>
    @endif

    <form wire:submit="login" class="auth-form">
        <label class="auth-field">
            <span>Email</span>
            <input wire:model="form.email" type="email" name="email" required autofocus autocomplete="username" />
            @error('form.email')<span class="auth-hint" style="color:var(--danger)">{{ $message }}</span>@enderror
        </label>

        <label class="auth-field">
            <span>
                Password
                @if (Route::has('password.request'))
                    <a class="auth-link-r" href="{{ route('password.request') }}" wire:navigate>Forgot?</a>
                @endif
            </span>
            <input wire:model="form.password" type="password" name="password" required autocomplete="current-password" />
            @error('form.password')<span class="auth-hint" style="color:var(--danger)">{{ $message }}</span>@enderror
        </label>

        <label class="auth-check">
            <input wire:model="form.remember" type="checkbox" name="remember" />
            <span>Keep me signed in for 30 days</span>
        </label>

        <button type="submit" class="primary-btn primary-btn-lg auth-submit">Sign in →</button>

        <p class="auth-foot">
            New here? <a href="{{ route('register') }}" wire:navigate>Open a workshop account</a>
        </p>
    </form>
</div>
