<?php

namespace Database\Seeders;

use App\Models\Ecu;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class EcuSeeder extends Seeder
{
    public function run(): void
    {
        $ecus = [
            ['Bosch',      'MED17',  'MED17.1.62',  'Bosch MED17.1.62',  ['stage_1','stage_2','egr_off','pops','custom']],
            ['Bosch',      'MED17',  'MED17.5.25',  'MED17.5.25',        ['stage_1','stage_2','pops','custom']],
            ['Bosch',      'MED17',  'MED17.5.21',  'MED17.5.21',        ['stage_1','stage_2','custom']],
            ['Bosch',      'MED17',  'MED17.1.21',  'MED17.1.21',        ['stage_1','stage_2','custom']],
            ['Bosch',      'MEVD17', 'MEVD17.2.6',  'MEVD17.2.6',        ['stage_1','stage_2','pops','custom']],
            ['Bosch',      'MEVD17', 'MEVD17.2.G',  'Bosch MEVD17.2.G',  ['stage_1','stage_2','pops','custom']],
            ['Bosch',      'EDC17',  'EDC17 CP44',  'EDC17 CP44',        ['stage_1','stage_2','dpf_off','egr_off','custom']],
            ['Bosch',      'MG1',    'MG1CS201',    'Bosch MG1CS201',    ['stage_1','stage_2','custom']],
            ['Bosch',      'MED40',  'MED40',       'MED40',             ['stage_1','stage_2','custom']],
            ['Continental','SIM2K',  'SIM2K-250',   'Continental SIM2K-250', ['stage_1','stage_2','custom']],
        ];

        foreach ($ecus as [$vendor, $family, $variant, $identifier, $supported]) {
            Ecu::firstOrCreate(
                ['identifier' => $identifier],
                ['vendor' => $vendor, 'family' => $family, 'variant' => $variant, 'supported_tunes' => $supported]
            );
        }

        $defaults = [
            'Golf R'        => 'Bosch MED17.1.62',
            'Golf GTI'      => 'MED17.5.25',
            'Polo GTI'      => 'MED17.5.21',
            'A6 3.0 TDI'    => 'EDC17 CP44',
            'A4 B9'         => 'MED17.5.25',
            'RS3 8V'        => 'MED17.5.21',
            '335i'          => 'Bosch MEVD17.2.G',
            'M140i F20'     => 'MEVD17.2.6',
            'M2 Comp'       => 'Bosch MG1CS201',
            'A45 AMG'       => 'MED17.5.25',
            'Focus ST'      => 'Continental SIM2K-250',
            'Defender 2.0'  => 'Bosch MG1CS201',
            'Cayman 718'    => 'MED17.1.21',
            'GT'            => 'Continental SIM2K-250',
            'Octavia vRS'   => 'MED17.5.25',
            'Leon Cupra'    => 'MED17.5.25',
        ];

        foreach ($defaults as $model => $ecuIdentifier) {
            $ecu = Ecu::where('identifier', $ecuIdentifier)->first();
            if (! $ecu) continue;
            Vehicle::whereHas('vehicleModel', fn ($q) => $q->where('name', $model))
                ->get()
                ->each(fn ($v) => $v->ecus()->syncWithoutDetaching([$ecu->id]));
        }

        // Fallback: every variant without an ECU gets a fuel-appropriate default
        // so the customer order wizard can progress past step 2 in the demo.
        $petrolEcu = Ecu::where('identifier', 'MED17.5.25')->first();
        $dieselEcu = Ecu::where('identifier', 'EDC17 CP44')->first();
        Vehicle::doesntHave('ecus')->get()->each(function ($v) use ($petrolEcu, $dieselEcu) {
            $ecu = $v->fuel === 'diesel' ? $dieselEcu : $petrolEcu;
            if ($ecu) $v->ecus()->syncWithoutDetaching([$ecu->id]);
        });
    }
}
