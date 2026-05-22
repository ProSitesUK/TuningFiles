<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $email = '';

    public function sendPasswordResetLink(): void
    {
        $this->validate(['email' => ['required', 'string', 'email']]);
        $status = Password::sendResetLink($this->only('email'));
        if ($status !== Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));
            return;
        }
        $this->reset('email');
        session()->flash('status', __($status));
    }
}; ?>

<div>
    <h1 class="auth-title">Reset your password</h1>
    <p class="auth-sub">Enter your email — we will send you a reset link.</p>

    @if (session('status'))
        <div class="auth-hint" style="color:var(--success); margin-bottom:12px">{{ session('status') }}</div>
    @endif

    <form wire:submit="sendPasswordResetLink" class="auth-form">
        <label class="auth-field">
            <span>Email</span>
            <input wire:model="email" type="email" required autofocus />
            @error('email')<span class="auth-hint" style="color:var(--danger)">{{ $message }}</span>@enderror
        </label>
        <button type="submit" class="primary-btn primary-btn-lg auth-submit">Send reset link →</button>
        <p class="auth-foot">Remembered it? <a href="{{ route('login') }}" wire:navigate>Sign in</a></p>
    </form>
</div>
