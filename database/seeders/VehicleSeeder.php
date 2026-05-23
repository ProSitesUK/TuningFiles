<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        $vehicles = [
            ['VW',         'Golf R',         'MK7',  2014, 2020, 'petrol',  '2.0 TSI',   300],
            ['VW',         'Golf GTI',       'MK7',  2013, 2020, 'petrol',  '2.0 TSI',   220],
            ['VW',         'Polo GTI',       '6C',   2014, 2017, 'petrol',  '1.8 TSI',   192],
            ['VW',         'Polo GTI',       'AW',   2017, 2024, 'petrol',  '2.0 TSI',   200],
            ['Audi',       'A6 3.0 TDI',     'C7',   2011, 2018, 'diesel',  '3.0 TDI',   245],
            ['Audi',       'A4 B9',          'B9',   2015, 2024, 'petrol',  '2.0 TFSI',  252],
            ['Audi',       'RS3 8V',         '8V',   2015, 2020, 'petrol',  '2.5 TFSI',  400],
            ['BMW',        '335i',           'F30',  2012, 2015, 'petrol',  '3.0 N55',   306],
            ['BMW',        'M140i F20',      'F20',  2016, 2019, 'petrol',  '3.0 B58',   340],
            ['BMW',        'M2 Comp',        'F87',  2018, 2020, 'petrol',  '3.0 S55',   410],
            ['Mercedes',   'A45 AMG',        'W176', 2013, 2018, 'petrol',  '2.0 M133',  381],
            ['Ford',       'Focus ST',       'MK3',  2012, 2018, 'petrol',  '2.0 EB',    250],
            ['Ford',       'Fiesta ST',      'MK7',  2013, 2017, 'petrol',  '1.6 EB',    180],
            ['Land Rover', 'Defender 2.0',   'L663', 2020, null, 'diesel',  '2.0 D200',  200],
            ['Porsche',    'Cayman 718',     '718',  2016, null, 'petrol',  '2.0 turbo', 300],
            ['Mustang',    'GT',             'S550', 2015, 2023, 'petrol',  '5.0 V8',    460],
            ['Skoda',      'Octavia vRS',    'MK3',  2013, 2020, 'petrol',  '2.0 TSI',   230],
            ['Seat',       'Leon Cupra',     '5F',   2014, 2020, 'petrol',  '2.0 TSI',   290],
        ];

        foreach ($vehicles as [$make, $model, $gen, $yStart, $yEnd, $fuel, $disp, $hp]) {
            Vehicle::firstOrCreate(
                ['make' => $make, 'model' => $model, 'generation' => $gen, 'year_start' => $yStart],
                ['year_end' => $yEnd, 'fuel' => $fuel, 'displacement' => $disp, 'stock_hp' => $hp]
            );
        }
    }
}
