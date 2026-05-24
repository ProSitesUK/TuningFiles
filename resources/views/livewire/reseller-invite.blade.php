<div>
    <div class="page">
        <div class="page-head">
            <div>
                <h1 class="page-title">Invite customer</h1>
                <p class="page-sub">Share your referral link or create an account directly.</p>
            </div>
            <div class="page-actions">
                <a href="{{ route('reseller.customers') }}" class="ghost-btn" style="text-decoration:none">&larr; Back to customers</a>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="card card-pad" style="margin-bottom:14px; border-color: var(--success); background: var(--success-soft);">
                {{ session('message') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="card card-pad" style="margin-bottom:14px; border-color: var(--danger); background: var(--danger-soft);">
                {{ session('error') }}
            </div>
        @endif

        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
            {{-- A. Invite link --}}
            <div class="card card-pad">
                <div class="metric-label" style="margin-bottom:12px;">Invite link</div>
                @if ($inviteLink)
                    <p style="color:var(--ink-2); font-size:13px; margin:0 0 12px;">Share this link with potential customers. When they register through it, they will be automatically linked to your account.</p>
                    <div style="display:flex; gap:8px;">
                        <input type="text" value="{{ $inviteLink }}" readonly
                               id="invite-link-input"
                               style="flex:1; padding:8px 12px; border:1px solid var(--border); border-radius:var(--r-sm); background:var(--surface-2); color:var(--ink); font-size:13px; font-family:var(--font-mono); outline:0;" />
                        <button type="button" class="ghost-btn"
                                onclick="navigator.clipboard.writeText(document.getElementById('invite-link-input').value); this.textContent='Copied!'; setTimeout(() => this.textContent='Copy', 2000);">
                            Copy
                        </button>
                    </div>
                @else
                    <p style="color:var(--muted); font-size:13px; margin:0;">Set up your business name in <a href="{{ route('reseller.settings') }}" style="color:var(--accent)">Settings</a> first to generate your referral link.</p>
                @endif
            </div>

            {{-- B. Create account directly --}}
            <div class="card card-pad">
                <div class="metric-label" style="margin-bottom:12px;">Create account directly</div>
                <p style="color:var(--ink-2); font-size:13px; margin:0 0 12px;">Create an account on behalf of a customer. They will receive a password reset email to set their password.</p>
                <form wire:submit="createAccount" class="va-form" style="margin:0; padding:0; border:0; background:transparent;">
                    <div class="va-grid-2">
                        <label class="va-field">
                            <span>Name</span>
                            <input wire:model="name" type="text" placeholder="Customer name" required />
                            @error('name')<span class="va-err">{{ $message }}</span>@enderror
                        </label>
                        <label class="va-field">
                            <span>Email</span>
                            <input wire:model="email" type="email" placeholder="customer@example.com" required />
                            @error('email')<span class="va-err">{{ $message }}</span>@enderror
                        </label>
                    </div>
                    <div class="va-form-actions">
                        <button type="submit" class="primary-btn">Create account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
