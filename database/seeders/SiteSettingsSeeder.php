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
            'referral_commission_tiers' => json_encode([
                ['threshold_pennies' => 10000, 'percent' => 2, 'label' => '£100+'],
                ['threshold_pennies' => 100000, 'percent' => 3, 'label' => '£1,000+'],
                ['threshold_pennies' => 1000000, 'percent' => 5, 'label' => '£10,000+'],
            ]),
        ];

        foreach ($defaults as $key => $value) {
            SiteSetting::firstOrCreate(['key' => $key], ['value' => $value]);
        }

        // Cached gets with defaults can hold stale values; force a refresh.
        SiteSetting::flushCache();
    }
}
