<?php

namespace App\Livewire;

use App\Models\ResellerProfile;
use Illuminate\Support\Str;
use Livewire\Component;

class ResellerSettings extends Component
{
    public string $business_name = '';
    public string $logo_url = '';
    public string $website = '';
    public string $bio = '';

    public function mount(): void
    {
        $profile = auth()->user()->resellerProfile;

        if ($profile) {
            $this->business_name = $profile->business_name ?? '';
            $this->logo_url = $profile->logo_url ?? '';
            $this->website = $profile->website ?? '';
            $this->bio = $profile->bio ?? '';
        }
    }

    public function save(): void
    {
        $this->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'logo_url'      => ['nullable', 'url', 'max:500'],
            'website'       => ['nullable', 'url', 'max:500'],
            'bio'           => ['nullable', 'string', 'max:2000'],
        ]);

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

        if ($profile) {
            $profile->update([
                'business_name' => $this->business_name,
                'slug'          => $slug,
                'logo_url'      => $this->logo_url ?: null,
                'website'       => $this->website ?: null,
                'bio'           => $this->bio ?: null,
            ]);
        } else {
            ResellerProfile::create([
                'user_id'       => auth()->id(),
                'business_name' => $this->business_name,
                'slug'          => $slug,
                'logo_url'      => $this->logo_url ?: null,
                'website'       => $this->website ?: null,
                'bio'           => $this->bio ?: null,
                'is_active'     => true,
            ]);
        }

        session()->flash('message', 'Settings saved successfully.');
    }

    public function render()
    {
        return view('livewire.reseller-settings');
    }
}
