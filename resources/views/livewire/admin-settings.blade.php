<div class="page">
    <div class="page-head">
        <div>
            <h1 class="page-title">Settings</h1>
            <p class="page-sub">Site-wide configuration — name, defaults, SEO templates, analytics.</p>
        </div>
    </div>

    @if ($flash)
        <div class="card card-pad" style="border-color: var(--success); background: var(--success-soft); margin-bottom: 16px">
            <span style="color: var(--success); font-weight: 500">✓ {{ $flash }}</span>
        </div>
    @endif

    <form wire:submit="save" class="card card-pad" style="max-width: 760px">
        <div class="va-form-title">Brand</div>
        <label class="va-field">
            <span>Site name</span>
            <input type="text" wire:model.defer="form.site_name" />
            @error('form.site_name') <em class="va-err">{{ $message }}</em> @enderror
        </label>
        <label class="va-field">
            <span>Footer line</span>
            <input type="text" wire:model.defer="form.footer_company_line" />
        </label>

        <div class="va-form-title" style="margin-top:18px">SEO defaults</div>
        <label class="va-field">
            <span>Default page description (max 320 chars)</span>
            <textarea wire:model.defer="form.default_description" rows="3"></textarea>
            @error('form.default_description') <em class="va-err">{{ $message }}</em> @enderror
        </label>
        <label class="va-field">
            <span>Default OG image URL <em class="t-mute small">(1200×630 recommended)</em></span>
            <input type="url" wire:model.defer="form.default_og_image" placeholder="https://…" />
            @error('form.default_og_image') <em class="va-err">{{ $message }}</em> @enderror
        </label>
        <div class="va-grid-2">
            <label class="va-field">
                <span>Title template <em class="t-mute small">tokens: {title} {site}</em></span>
                <input type="text" wire:model.defer="form.title_template" placeholder="{title} · {site}" />
                @error('form.title_template') <em class="va-err">{{ $message }}</em> @enderror
            </label>
            <label class="va-field">
                <span>Default robots policy</span>
                <select wire:model.defer="form.default_robots">
                    <option value="index,follow">index, follow</option>
                    <option value="index,nofollow">index, nofollow</option>
                    <option value="noindex,follow">noindex, follow</option>
                    <option value="noindex,nofollow">noindex, nofollow</option>
                </select>
            </label>
        </div>

        <div class="va-form-title" style="margin-top:18px">Analytics & verification</div>
        <div class="va-grid-2">
            <label class="va-field">
                <span>Google Analytics 4 measurement ID</span>
                <input type="text" wire:model.defer="form.ga4_measurement_id" placeholder="G-XXXXXXXXXX" />
                @error('form.ga4_measurement_id') <em class="va-err">{{ $message }}</em> @enderror
                <em class="t-mute small">Script only injects on production. UK cookie-consent required before this runs at scale.</em>
            </label>
            <label class="va-field">
                <span>Google Search Console verification</span>
                <input type="text" wire:model.defer="form.gsc_verification" placeholder="abcdef123…" />
                @error('form.gsc_verification') <em class="va-err">{{ $message }}</em> @enderror
                <em class="t-mute small">Paste only the content value from the meta tag.</em>
            </label>
        </div>

        <div class="va-form-title" style="margin-top:18px">Payment methods</div>
        <p class="t-mute small" style="margin-bottom:10px">Enable or disable payment gateways available to customers when purchasing credit packs.</p>

        <div class="va-grid-2" style="margin-bottom:14px">
            <label class="va-field">
                <span>Stripe (card payments)</span>
                <select wire:model.defer="form.gateway_stripe_enabled">
                    <option value="true">Enabled</option>
                    <option value="false">Disabled</option>
                </select>
            </label>
            <label class="va-field">
                <span>Bank transfer</span>
                <select wire:model.defer="form.gateway_bank_enabled">
                    <option value="true">Enabled</option>
                    <option value="false">Disabled</option>
                </select>
            </label>
        </div>

        <label class="va-field">
            <span>Bank account details <em class="t-mute small">(shown to customer when they choose bank transfer)</em></span>
            <textarea wire:model.defer="form.gateway_bank_details" rows="4" placeholder="Account name: TuningFiles Ltd&#10;Sort code: 12-34-56&#10;Account number: 12345678&#10;Reference: use your TF-xxx reference"></textarea>
            @error('form.gateway_bank_details') <em class="va-err">{{ $message }}</em> @enderror
        </label>

        <div class="va-grid-2" style="margin-top:14px">
            <label class="va-field">
                <span>Invoice payments</span>
                <select wire:model.defer="form.gateway_invoice_enabled">
                    <option value="true">Enabled</option>
                    <option value="false">Disabled</option>
                </select>
                <em class="t-mute small">Only customers with "can invoice" flag will see this option.</em>
            </label>
            <label class="va-field">
                <span>Default invoice terms</span>
                <select wire:model.defer="form.gateway_invoice_terms">
                    <option value="net_7">Net 7</option>
                    <option value="net_14">Net 14</option>
                    <option value="net_30">Net 30</option>
                    <option value="net_60">Net 60</option>
                </select>
            </label>
        </div>
        <label class="va-field" style="margin-top:8px">
            <span>Invoice company details <em class="t-mute small">(appears on invoice header)</em></span>
            <textarea wire:model.defer="form.gateway_invoice_company" rows="4" placeholder="TuningFiles Ltd&#10;123 Tuning Street&#10;Bristol BS1 1AA&#10;VAT: GB123456789"></textarea>
            @error('form.gateway_invoice_company') <em class="va-err">{{ $message }}</em> @enderror
        </label>

        <div class="va-form-actions" style="margin-top: 18px">
            <button type="submit" class="primary-btn primary-btn-lg" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="save">Save settings</span>
                <span wire:loading wire:target="save">Saving…</span>
            </button>
        </div>
    </form>
</div>
