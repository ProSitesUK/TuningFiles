<?php

namespace App\Livewire;

use App\Models\ResellerProfile;
use App\Models\SiteSetting;
use Illuminate\Support\Str;
use Livewire\Component;

class ResellerSettings extends Component
{
    public string $business_name = '';
    public string $logo_url = '';
    public string $website = '';
    public string $bio = '';
    public string $custom_domain = '';
    public bool $domain_verified = false;

    public function mount(): void
    {
        $profile = auth()->user()->resellerProfile;

        if ($profile) {
            $this->business_name = $profile->business_name ?? '';
            $this->logo_url = $profile->logo_url ?? '';
            $this->website = $profile->website ?? '';
            $this->bio = $profile->bio ?? '';
            $this->custom_domain = $profile->custom_domain ?? '';
            $this->domain_verified = (bool) $profile->domain_verified;
        }
    }

    public function save(): void
    {
        $rules = [
            'business_name' => ['required', 'string', 'max:255'],
            'logo_url'      => ['nullable', 'url', 'max:500'],
            'website'       => ['nullable', 'url', 'max:500'],
            'bio'           => ['nullable', 'string', 'max:2000'],
        ];

        if ($this->customDomainsEnabled()) {
            $rules['custom_domain'] = ['nullable', 'string', 'max:255'];
        }

        $this->validate($rules);

        $profile = auth()->user()->resellerProfile;

        $slug = Str::slug($this->business_name);

        // Ensure slug uniqueness
        $slugCheck = ResellerProfile::where('slug', $slug)
            ->where('user_id', '!=', auth()->id())
            ->exists();

        if ($slugCheck) {
            $this->addError('business_name', 'This business name generates a slug already in use. Please choose a different name.');
            return;
        }

        $data = [
            'business_name' => $this->business_name,
            'slug'          => $slug,
            'logo_url'      => $this->logo_url ?: null,
            'website'       => $this->website ?: null,
            'bio'           => $this->bio ?: null,
        ];

        if ($this->customDomainsEnabled()) {
            $oldDomain = $profile?->custom_domain;
            $data['custom_domain'] = $this->custom_domain ?: null;

            // Reset verification if domain changed
            if ($this->custom_domain !== $oldDomain) {
                $data['domain_verified'] = false;
            }
        }

        if ($profile) {
            $profile->update($data);
        } else {
            ResellerProfile::create(array_merge($data, [
                'user_id'   => auth()->id(),
                'is_active' => true,
            ]));
        }

        session()->flash('message', 'Settings saved successfully.');
    }

    public function customDomainsEnabled(): bool
    {
        return SiteSetting::get('custom_domains_enabled', 'false') === 'true';
    }

    public function render()
    {
        return view('livewire.reseller-settings');
    }
}
