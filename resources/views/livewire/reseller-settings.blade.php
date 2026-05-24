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

                <div class="va-form-actions">
                    <button type="submit" class="primary-btn">Save settings</button>
                </div>
            </form>
        </div>
    </div>
</div>
