<?php

namespace Database\Seeders;

use App\Models\Tune;
use Illuminate\Database\Seeder;

class TuneSeeder extends Seeder
{
    public function run(): void
    {
        $tunes = [
            ['stage_1',    'Stage 1',         32, 'Bolt-on, no hardware. Smoother power, +20-30% torque.'],
            ['stage_2',    'Stage 2',         40, 'Requires downpipe / intake. Bigger gains, sharper throttle.'],
            ['egr_off',    'EGR off',         12, 'Disable EGR valve in software.'],
            ['dpf_off',    'DPF off',         16, 'Software-only DPF delete. Diesel only.'],
            ['adblue_off', 'AdBlue off',      16, 'Disable AdBlue system. Diesel only.'],
            ['pops',       'Pops & bangs',    18, 'Cracks and pops on overrun and gear changes.'],
            ['dsg_flash',  'DSG / TCU flash', 28, 'Transmission map for sharper, harder shifts.'],
            ['custom',     'Custom remap',    55, 'Fully custom one-off map.'],
        ];

        foreach ($tunes as [$slug, $label, $cost, $desc]) {
            Tune::firstOrCreate(['slug' => $slug], ['label' => $label, 'credit_cost' => $cost, 'description' => $desc]);
        }
    }
}
