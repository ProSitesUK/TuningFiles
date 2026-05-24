<?php

namespace App\Livewire;

use App\Models\SiteSetting;
use Livewire\Component;

class AdminSettings extends Component
{
    public array $form = [];
    public ?string $flash = null;

    public function mount(): void
    {
        $this->form = [
            'site_name'            => SiteSetting::get('site_name', config('app.name', 'TuningFiles')),
            'default_description'  => SiteSetting::get('default_description', 'Professional ECU files delivered in minutes. Stage 1 to custom remaps from a vetted tuner network.'),
            'default_og_image'     => SiteSetting::get('default_og_image', ''),
            'title_template'       => SiteSetting::get('title_template', '{title} · {site}'),
            'default_robots'       => SiteSetting::get('default_robots', 'index,follow'),
            'ga4_measurement_id'   => SiteSetting::get('ga4_measurement_id', ''),
            'gsc_verification'     => SiteSetting::get('gsc_verification', ''),
            'footer_company_line'  => SiteSetting::get('footer_company_line', '© '.date('Y').' tuningfiles ltd · Bristol, UK'),

            // Payment gateways
            'gateway_stripe_enabled'   => SiteSetting::get('gateway_stripe_enabled', 'true'),
            'gateway_bank_enabled'     => SiteSetting::get('gateway_bank_enabled', 'false'),
            'gateway_bank_details'     => SiteSetting::get('gateway_bank_details', ''),
            'gateway_invoice_enabled'  => SiteSetting::get('gateway_invoice_enabled', 'false'),
            'gateway_invoice_terms'    => SiteSetting::get('gateway_invoice_terms', 'net_30'),
            'gateway_invoice_company'  => SiteSetting::get('gateway_invoice_company', ''),

            // Guarantees & Revisions
            'guarantee_days'           => SiteSetting::get('guarantee_days', '30'),
            'revision_window_hours'    => SiteSetting::get('revision_window_hours', '24'),
            'max_free_revisions'       => SiteSetting::get('max_free_revisions', '1'),

            // Pay-per-file
            'pay_per_file_enabled'     => SiteSetting::get('pay_per_file_enabled', 'true'),
            'credit_rate_pennies'      => SiteSetting::get('credit_rate_pennies', '100'),

            // Referral program
            'referral_enabled'           => SiteSetting::get('referral_enabled', 'false'),
            'referral_credits_referrer'  => SiteSetting::get('referral_credits_referrer', '10'),
            'referral_credits_referred'  => SiteSetting::get('referral_credits_referred', '10'),
        ];
    }

    public function save(): void
    {
        $this->validate([
            'form.site_name'           => 'required|string|max:80',
            'form.default_description' => 'nullable|string|max:320',
            'form.default_og_image'    => 'nullable|url|max:255',
            'form.title_template'      => 'required|string|max:120',
            'form.default_robots'      => 'required|string|max:64',
            'form.ga4_measurement_id'  => 'nullable|string|max:32|regex:/^G-[A-Z0-9]+$/',
            'form.gsc_verification'    => 'nullable|string|max:255',
            'form.footer_company_line' => 'nullable|string|max:160',

            // Payment gateways
            'form.gateway_stripe_enabled'  => 'required|in:true,false',
            'form.gateway_bank_enabled'    => 'required|in:true,false',
            'form.gateway_bank_details'    => 'nullable|string|max:1000',
            'form.gateway_invoice_enabled' => 'required|in:true,false',
            'form.gateway_invoice_terms'   => 'required|in:net_7,net_14,net_30,net_60',
            'form.gateway_invoice_company' => 'nullable|string|max:1000',

            // Guarantees & Revisions
            'form.guarantee_days'          => 'required|integer|min:1|max:365',
            'form.revision_window_hours'   => 'required|integer|min:1|max:720',
            'form.max_free_revisions'      => 'required|integer|min:0|max:10',

            // Pay-per-file
            'form.pay_per_file_enabled'    => 'required|in:true,false',
            'form.credit_rate_pennies'     => 'required|integer|min:1|max:100000',

            // Referral program
            'form.referral_enabled'          => 'required|in:true,false',
            'form.referral_credits_referrer' => 'required|integer|min:0|max:1000',
            'form.referral_credits_referred' => 'required|integer|min:0|max:1000',
        ], [
            'form.ga4_measurement_id.regex' => 'GA4 measurement IDs look like G-XXXXXXXXXX.',
        ]);

        foreach ($this->form as $key => $value) {
            SiteSetting::put($key, $value === '' ? null : $value);
        }

        SiteSetting::flushCache();
        $this->flash = 'Settings saved.';
    }

    public function render()
    {
        return view('livewire.admin-settings');
    }
}
