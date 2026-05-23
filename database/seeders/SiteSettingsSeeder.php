<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'site_name'           => 'TuningFiles',
            'default_description' => 'Professional ECU tuning files delivered in minutes. Stage 1 to full custom remaps from a vetted network of UK and EU tuners. Checksum-correct, dyno-validated, original retained.',
            'title_template'      => '{title} · {site}',
            'default_robots'      => 'index,follow',
            'footer_company_line' => '© '.date('Y').' tuningfiles ltd · Bristol, UK',
        ];

        foreach ($defaults as $key => $value) {
            SiteSetting::firstOrCreate(['key' => $key], ['value' => $value]);
        }

        // Cached gets with defaults can hold stale values; force a refresh.
        SiteSetting::flushCache();
    }
}
