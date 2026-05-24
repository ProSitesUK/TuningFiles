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
