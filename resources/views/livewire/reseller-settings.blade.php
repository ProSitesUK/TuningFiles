<div>
    <div class="page">
        <div class="page-head">
            <div>
                <h1 class="page-title">Settings</h1>
                <p class="page-sub">Configure your reseller profile and branding.</p>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="card card-pad" style="margin-bottom:14px; border-color: var(--success); background: var(--success-soft);">
                {{ session('message') }}
            </div>
        @endif

        <div class="card card-pad" style="max-width:640px">
            <form wire:submit="save" class="va-form" style="margin:0; padding:0; border:0; background:transparent;">
                <label class="va-field">
                    <span>Business name</span>
                    <input wire:model="business_name" type="text" placeholder="Your business name" required />
                    @error('business_name')<span class="va-err">{{ $message }}</span>@enderror
                </label>

                <label class="va-field">
                    <span>Logo URL</span>
                    <input wire:model="logo_url" type="url" placeholder="https://example.com/logo.png" />
                    @error('logo_url')<span class="va-err">{{ $message }}</span>@enderror
                </label>

                <label class="va-field">
                    <span>Website</span>
                    <input wire:model="website" type="url" placeholder="https://your-website.com" />
                    @error('website')<span class="va-err">{{ $message }}</span>@enderror
                </label>

                <label class="va-field">
                    <span>Bio</span>
                    <textarea wire:model="bio" rows="4" placeholder="A brief description of your business..."
                              style="padding:8px 12px; border:1px solid var(--border); border-radius:var(--r-sm); background:var(--bg); color:var(--ink); font-size:13px; font-family:inherit; outline:0; resize:vertical;"></textarea>
                    @error('bio')<span class="va-err">{{ $message }}</span>@enderror
                </label>

                @if ($this->customDomainsEnabled())
                    <div style="margin-top:18px; padding-top:14px; border-top:1px solid var(--border)">
                        <div class="va-form-title">Custom domain</div>
                        <label class="va-field">
                            <span>Domain name</span>
                            <input wire:model="custom_domain" type="text" placeholder="tune.yourdomain.com" />
                            @error('custom_domain')<span class="va-err">{{ $message }}</span>@enderror
                        </label>

                        @if ($domain_verified)
                            <div style="display:flex; align-items:center; gap:6px; margin-bottom:10px">
                                <span class="badge badge-success">verified</span>
                                <span class="t-mute small">Your custom domain is active.</span>
                            </div>
                        @elseif ($custom_domain)
                            <div class="card card-pad" style="background:var(--surface-2); margin-bottom:10px">
                                <div class="metric-label" style="margin-bottom:6px">CNAME setup required</div>
                                <p class="t-mute small" style="margin-bottom:6px">
                                    Point your domain to <code>tuningfiles.app</code> via a CNAME record in your DNS settings.
                                </p>
                                <div class="mono small" style="padding:6px 10px; background:var(--bg); border:1px solid var(--border); border-radius:4px">
                                    {{ $custom_domain }} CNAME tuningfiles.app
                                </div>
                                <p class="t-mute small" style="margin-top:6px">Once propagated, verification will happen automatically.</p>
                            </div>
                        @endif
                    </div>
                @endif

                <div class="va-form-actions">
                    <button type="submit" class="primary-btn">Save settings</button>
                </div>
            </form>
        </div>
    </div>
</div>
