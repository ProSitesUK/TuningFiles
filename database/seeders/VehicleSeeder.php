<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use App\Models\VehicleMake;
use App\Models\VehicleModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        $sort = 0;
        foreach ($this->data() as $domain => $payload) {
            [$makeName, $models] = [$payload['name'], $payload['models']];

            $make = VehicleMake::updateOrCreate(
                ['name' => $makeName],
                [
                    'slug'       => Str::slug($makeName),
                    'logo_url'   => "https://logo.clearbit.com/{$domain}",
                    'image_url'  => $payload['image_url'] ?? null,
                    'sort_order' => $sort++,
                    'is_active'  => true,
                ],
            );

            foreach ($models as $modelName => $modelPayload) {
                $vmodel = VehicleModel::updateOrCreate(
                    ['make_id' => $make->id, 'name' => $modelName],
                    [
                        'slug'      => Str::slug($modelName),
                        'body_type' => $modelPayload['body'] ?? null,
                        'image_url' => $modelPayload['image_url'] ?? null,
                        'is_active' => true,
                    ],
                );

                foreach ($modelPayload['variants'] as $v) {
                    Vehicle::updateOrCreate(
                        [
                            'model_id'    => $vmodel->id,
                            'generation'  => $v['gen'] ?? null,
                            'year_start'  => $v['from'],
                        ],
                        [
                            'year_end'     => $v['to']  ?? null,
                            'fuel'         => $v['fuel'],
                            'displacement' => $v['disp'],
                            'stock_hp'     => $v['hp'],
                            'is_active'    => true,
                        ],
                    );
                }
            }
        }
    }

    private function data(): array
    {
        // Compact format: gen, from, to, fuel, disp, hp
        $g = fn (string $gen, int $from, ?int $to, string $fuel, string $disp, int $hp) =>
            ['gen' => $gen, 'from' => $from, 'to' => $to, 'fuel' => $fuel, 'disp' => $disp, 'hp' => $hp];

        return [
            'audi.com' => ['name' => 'Audi', 'models' => [
                'A1'     => ['body' => 'hatch',     'variants' => [$g('8X', 2010, 2018, 'petrol', '1.4 TFSI', 122), $g('GB', 2018, null, 'petrol', '1.5 TFSI', 150)]],
                'A3'     => ['body' => 'hatch',     'variants' => [$g('8P', 2003, 2012, 'petrol', '2.0 TFSI', 200), $g('8V', 2012, 2020, 'petrol', '2.0 TFSI', 190), $g('8Y', 2020, null, 'petrol', '2.0 TFSI', 245)]],
                'A4'     => ['body' => 'saloon',    'variants' => [$g('B8', 2007, 2015, 'petrol', '2.0 TFSI', 211), $g('B9', 2015, 2024, 'petrol', '2.0 TFSI', 252), $g('B10', 2024, null, 'petrol', '2.0 TFSI', 265)]],
                'A6'     => ['body' => 'saloon',    'variants' => [$g('C7', 2011, 2018, 'diesel', '3.0 TDI', 245), $g('C8', 2018, null, 'diesel', '3.0 TDI', 286)]],
                'Q5'     => ['body' => 'SUV',       'variants' => [$g('8R', 2008, 2017, 'diesel', '2.0 TDI', 190), $g('FY', 2017, null, 'petrol', '2.0 TFSI', 265)]],
                'RS3'    => ['body' => 'hot hatch', 'variants' => [$g('8V', 2015, 2020, 'petrol', '2.5 TFSI', 400), $g('8Y', 2021, null, 'petrol', '2.5 TFSI', 400)]],
                'RS6'    => ['body' => 'estate',    'variants' => [$g('C7', 2013, 2018, 'petrol', '4.0 TFSI', 560), $g('C8', 2019, null, 'petrol', '4.0 TFSI', 600)]],
                'TT'     => ['body' => 'coupe',     'variants' => [$g('8J', 2006, 2014, 'petrol', '2.0 TFSI', 211), $g('8S', 2014, 2023, 'petrol', '2.0 TFSI', 230)]],
            ]],

            'bmw.com' => ['name' => 'BMW', 'models' => [
                '1 Series' => ['body' => 'hatch',     'variants' => [$g('F20', 2011, 2019, 'petrol', '3.0 N55', 326), $g('F40', 2019, null, 'petrol', '2.0 B48', 178)]],
                '3 Series' => ['body' => 'saloon',    'variants' => [$g('E90', 2005, 2012, 'petrol', '3.0 N54', 306), $g('F30', 2012, 2019, 'petrol', '3.0 N55', 306), $g('G20', 2019, null, 'petrol', '3.0 B58', 374)]],
                '5 Series' => ['body' => 'saloon',    'variants' => [$g('F10', 2010, 2017, 'diesel', '3.0 N57', 258), $g('G30', 2017, 2023, 'petrol', '3.0 B58', 340), $g('G60', 2024, null, 'petrol', '3.0 B58', 380)]],
                'X3'       => ['body' => 'SUV',       'variants' => [$g('F25', 2010, 2017, 'diesel', '3.0 N57', 258), $g('G01', 2017, null, 'petrol', '3.0 B58', 360)]],
                'X5'       => ['body' => 'SUV',       'variants' => [$g('F15', 2013, 2018, 'diesel', '3.0 N57', 313), $g('G05', 2018, null, 'petrol', '3.0 B58', 340)]],
                'M2'       => ['body' => 'coupe',     'variants' => [$g('F87', 2018, 2020, 'petrol', '3.0 S55', 410), $g('G87', 2023, null, 'petrol', '3.0 S58', 460)]],
                'M3'       => ['body' => 'saloon',    'variants' => [$g('F80', 2014, 2018, 'petrol', '3.0 S55', 431), $g('G80', 2021, null, 'petrol', '3.0 S58', 510)]],
                'M140i'    => ['body' => 'hot hatch', 'variants' => [$g('F20', 2016, 2019, 'petrol', '3.0 B58', 340)]],
            ]],

            'mercedes-benz.com' => ['name' => 'Mercedes-Benz', 'models' => [
                'A-Class'  => ['body' => 'hatch',     'variants' => [$g('W176', 2012, 2018, 'petrol', '2.0 M270', 218), $g('W177', 2018, null, 'petrol', '2.0 M260', 224)]],
                'C-Class'  => ['body' => 'saloon',    'variants' => [$g('W204', 2007, 2014, 'diesel', '2.1 OM651', 170), $g('W205', 2014, 2021, 'petrol', '2.0 M274', 245), $g('W206', 2021, null, 'petrol', '2.0 M254', 258)]],
                'E-Class'  => ['body' => 'saloon',    'variants' => [$g('W212', 2009, 2016, 'diesel', '2.1 OM651', 204), $g('W213', 2016, 2023, 'petrol', '2.0 M264', 299), $g('W214', 2023, null, 'petrol', '2.0 M254', 258)]],
                'GLC'      => ['body' => 'SUV',       'variants' => [$g('X253', 2015, 2022, 'diesel', '2.1 OM651', 204), $g('X254', 2022, null, 'petrol', '2.0 M254', 258)]],
                'A45 AMG'  => ['body' => 'hot hatch', 'variants' => [$g('W176', 2013, 2018, 'petrol', '2.0 M133', 381), $g('W177', 2019, null, 'petrol', '2.0 M139', 421)]],
                'C63 AMG'  => ['body' => 'saloon',    'variants' => [$g('W204', 2008, 2014, 'petrol', '6.2 M156', 457), $g('W205', 2014, 2022, 'petrol', '4.0 M177', 510)]],
            ]],

            'volkswagen.com' => ['name' => 'Volkswagen', 'models' => [
                'Polo'      => ['body' => 'hatch',     'variants' => [$g('6R', 2009, 2017, 'petrol', '1.4 TSI', 140), $g('AW', 2017, null, 'petrol', '1.0 TSI', 110)]],
                'Polo GTI'  => ['body' => 'hot hatch', 'variants' => [$g('6C', 2014, 2017, 'petrol', '1.8 TSI', 192), $g('AW', 2017, 2024, 'petrol', '2.0 TSI', 200)]],
                'Golf'      => ['body' => 'hatch',     'variants' => [$g('MK6', 2008, 2013, 'petrol', '2.0 TSI', 211), $g('MK7', 2012, 2020, 'petrol', '2.0 TSI', 220), $g('MK8', 2020, null, 'petrol', '1.5 eTSI', 150)]],
                'Golf GTI'  => ['body' => 'hot hatch', 'variants' => [$g('MK7', 2013, 2020, 'petrol', '2.0 TSI', 220), $g('MK8', 2020, null, 'petrol', '2.0 TSI', 245)]],
                'Golf R'    => ['body' => 'hot hatch', 'variants' => [$g('MK7', 2014, 2020, 'petrol', '2.0 TSI', 300), $g('MK8', 2021, null, 'petrol', '2.0 TSI', 320)]],
                'Passat'    => ['body' => 'estate',    'variants' => [$g('B7', 2010, 2015, 'diesel', '2.0 TDI', 170), $g('B8', 2015, 2023, 'diesel', '2.0 TDI', 190)]],
                'Tiguan'    => ['body' => 'SUV',       'variants' => [$g('5N', 2007, 2016, 'diesel', '2.0 TDI', 170), $g('AD', 2016, 2023, 'petrol', '2.0 TSI', 220), $g('CT', 2023, null, 'petrol', '2.0 TSI', 265)]],
            ]],

            'skoda-auto.com' => ['name' => 'Skoda', 'models' => [
                'Fabia'        => ['body' => 'hatch',     'variants' => [$g('NJ', 2014, 2021, 'petrol', '1.0 TSI', 110)]],
                'Octavia'      => ['body' => 'hatch',     'variants' => [$g('MK3', 2013, 2020, 'petrol', '2.0 TSI', 230), $g('MK4', 2020, null, 'diesel', '2.0 TDI', 200)]],
                'Octavia vRS'  => ['body' => 'hot hatch', 'variants' => [$g('MK3', 2013, 2020, 'petrol', '2.0 TSI', 230), $g('MK4', 2021, null, 'petrol', '2.0 TSI', 245)]],
                'Superb'       => ['body' => 'saloon',    'variants' => [$g('B8', 2015, 2023, 'diesel', '2.0 TDI', 190), $g('B9', 2023, null, 'petrol', '2.0 TSI', 265)]],
                'Karoq'        => ['body' => 'SUV',       'variants' => [$g('NU', 2017, null, 'petrol', '1.5 TSI', 150)]],
            ]],

            'seat.com' => ['name' => 'SEAT', 'models' => [
                'Ibiza'      => ['body' => 'hatch',     'variants' => [$g('6P', 2008, 2017, 'petrol', '1.4 TSI', 150), $g('KJ', 2017, null, 'petrol', '1.0 TSI', 115)]],
                'Leon'       => ['body' => 'hatch',     'variants' => [$g('5F', 2012, 2020, 'petrol', '2.0 TSI', 184), $g('KL', 2020, null, 'petrol', '1.5 eTSI', 150)]],
                'Leon Cupra' => ['body' => 'hot hatch', 'variants' => [$g('5F', 2014, 2020, 'petrol', '2.0 TSI', 290)]],
                'Ateca'      => ['body' => 'SUV',       'variants' => [$g('5FP', 2016, null, 'petrol', '2.0 TSI', 190)]],
                'Arona'      => ['body' => 'SUV',       'variants' => [$g('KJ', 2017, null, 'petrol', '1.0 TSI', 115)]],
            ]],

            'porsche.com' => ['name' => 'Porsche', 'models' => [
                '911'      => ['body' => 'coupe',  'variants' => [$g('991.2', 2015, 2019, 'petrol', '3.0 turbo', 370), $g('992', 2019, null, 'petrol', '3.0 turbo', 385)]],
                'Cayman'   => ['body' => 'coupe',  'variants' => [$g('981', 2012, 2016, 'petrol', '2.7 H6', 275), $g('718', 2016, null, 'petrol', '2.0 turbo', 300)]],
                'Boxster'  => ['body' => 'roadster','variants'=> [$g('981', 2012, 2016, 'petrol', '2.7 H6', 265), $g('718', 2016, null, 'petrol', '2.0 turbo', 300)]],
                'Macan'    => ['body' => 'SUV',    'variants' => [$g('95B', 2014, 2024, 'petrol', '2.0 EA888', 252)]],
                'Cayenne'  => ['body' => 'SUV',    'variants' => [$g('92A', 2010, 2017, 'diesel', '3.0 TDI', 245), $g('9YA', 2017, null, 'petrol', '3.0 turbo', 340)]],
            ]],

            'ford.com' => ['name' => 'Ford', 'models' => [
                'Fiesta'    => ['body' => 'hatch',     'variants' => [$g('MK7', 2008, 2017, 'petrol', '1.0 EB', 100), $g('MK8', 2017, 2023, 'petrol', '1.0 EB', 125)]],
                'Fiesta ST' => ['body' => 'hot hatch', 'variants' => [$g('MK7', 2013, 2017, 'petrol', '1.6 EB', 180), $g('MK8', 2018, 2023, 'petrol', '1.5 EB', 200)]],
                'Focus'     => ['body' => 'hatch',     'variants' => [$g('MK3', 2011, 2018, 'petrol', '1.0 EB', 125), $g('MK4', 2018, null, 'petrol', '1.5 EB', 150)]],
                'Focus ST'  => ['body' => 'hot hatch', 'variants' => [$g('MK3', 2012, 2018, 'petrol', '2.0 EB', 250), $g('MK4', 2019, null, 'petrol', '2.3 EB', 280)]],
                'Focus RS'  => ['body' => 'hot hatch', 'variants' => [$g('MK3', 2016, 2018, 'petrol', '2.3 EB', 350)]],
                'Mustang'   => ['body' => 'coupe',     'variants' => [$g('S550', 2015, 2023, 'petrol', '5.0 V8', 460), $g('S650', 2023, null, 'petrol', '5.0 V8', 480)]],
                'Kuga'      => ['body' => 'SUV',       'variants' => [$g('MK2', 2012, 2019, 'diesel', '2.0 TDCi', 180), $g('MK3', 2019, null, 'petrol', '2.5 PHEV', 225)]],
            ]],

            'vauxhall.co.uk' => ['name' => 'Vauxhall', 'models' => [
                'Corsa'     => ['body' => 'hatch', 'variants' => [$g('E', 2014, 2019, 'petrol', '1.4 turbo', 100), $g('F', 2019, null, 'petrol', '1.2 turbo', 130)]],
                'Astra'     => ['body' => 'hatch', 'variants' => [$g('J', 2009, 2015, 'diesel', '1.7 CDTI', 130), $g('K', 2015, 2021, 'petrol', '1.4 turbo', 150), $g('L', 2021, null, 'petrol', '1.2 turbo', 130)]],
                'Insignia'  => ['body' => 'saloon', 'variants'=> [$g('A', 2008, 2017, 'diesel', '2.0 CDTI', 160), $g('B', 2017, 2022, 'petrol', '2.0 turbo', 200)]],
                'Mokka'     => ['body' => 'SUV', 'variants'   => [$g('B', 2020, null, 'petrol', '1.2 turbo', 130)]],
                'Grandland' => ['body' => 'SUV', 'variants'   => [$g('A', 2017, 2024, 'petrol', '1.6 turbo', 180)]],
            ]],

            'peugeot.com' => ['name' => 'Peugeot', 'models' => [
                '208'  => ['body' => 'hatch', 'variants' => [$g('A9', 2012, 2019, 'petrol', '1.2 PureTech', 110), $g('P21', 2019, null, 'petrol', '1.2 PureTech', 130)]],
                '308'  => ['body' => 'hatch', 'variants' => [$g('T9', 2013, 2021, 'petrol', '1.6 THP', 165), $g('P51', 2021, null, 'petrol', '1.2 PureTech', 130)]],
                '508'  => ['body' => 'saloon','variants' => [$g('R8', 2018, null, 'petrol', '1.6 PureTech', 225)]],
                '2008' => ['body' => 'SUV',   'variants' => [$g('P24', 2019, null, 'petrol', '1.2 PureTech', 130)]],
                '3008' => ['body' => 'SUV',   'variants' => [$g('P84', 2016, 2023, 'diesel', '1.5 BlueHDi', 130)]],
            ]],

            'renault.com' => ['name' => 'Renault', 'models' => [
                'Clio'      => ['body' => 'hatch',     'variants' => [$g('IV', 2012, 2019, 'petrol', '0.9 TCe', 90), $g('V', 2019, null, 'petrol', '1.0 TCe', 100)]],
                'Megane'    => ['body' => 'hatch',     'variants' => [$g('III', 2008, 2016, 'diesel', '1.5 dCi', 110), $g('IV', 2016, 2022, 'petrol', '1.6 TCe', 205)]],
                'Megane RS' => ['body' => 'hot hatch', 'variants' => [$g('III', 2010, 2016, 'petrol', '2.0 turbo', 265), $g('IV', 2018, 2023, 'petrol', '1.8 TCe', 280)]],
                'Captur'    => ['body' => 'SUV',       'variants' => [$g('I', 2013, 2019, 'petrol', '0.9 TCe', 90), $g('II', 2019, null, 'petrol', '1.3 TCe', 140)]],
                'Kadjar'    => ['body' => 'SUV',       'variants' => [$g('HA', 2015, 2022, 'petrol', '1.3 TCe', 160)]],
            ]],

            'citroen.com' => ['name' => 'Citroen', 'models' => [
                'C3'         => ['body' => 'hatch', 'variants' => [$g('II', 2009, 2016, 'petrol', '1.2 PureTech', 82), $g('III', 2016, null, 'petrol', '1.2 PureTech', 110)]],
                'C4'         => ['body' => 'hatch', 'variants' => [$g('II', 2010, 2018, 'diesel', '1.6 BlueHDi', 100), $g('III', 2020, null, 'petrol', '1.2 PureTech', 130)]],
                'C5 Aircross'=> ['body' => 'SUV',   'variants' => [$g('A', 2018, null, 'petrol', '1.6 PureTech', 180)]],
                'Berlingo'   => ['body' => 'van',   'variants' => [$g('III', 2018, null, 'diesel', '1.5 BlueHDi', 130)]],
            ]],

            'dsautomobiles.co.uk' => ['name' => 'DS', 'models' => [
                'DS 3'  => ['body' => 'hatch', 'variants' => [$g('I', 2010, 2019, 'petrol', '1.6 THP', 165)]],
                'DS 4'  => ['body' => 'hatch', 'variants' => [$g('II', 2021, null, 'petrol', '1.6 PureTech', 180)]],
                'DS 7'  => ['body' => 'SUV',   'variants' => [$g('I', 2018, null, 'petrol', '1.6 PureTech', 225)]],
            ]],

            'fiat.com' => ['name' => 'Fiat', 'models' => [
                '500'        => ['body' => 'hatch', 'variants' => [$g('312', 2007, null, 'petrol', '1.2 FIRE', 69), $g('500e', 2020, null, 'electric', 'BEV', 117)]],
                '500 Abarth' => ['body' => 'hot hatch', 'variants' => [$g('595', 2008, 2023, 'petrol', '1.4 turbo', 180)]],
                'Panda'      => ['body' => 'hatch', 'variants' => [$g('III', 2012, null, 'petrol', '0.9 TwinAir', 85)]],
                'Tipo'       => ['body' => 'hatch', 'variants' => [$g('356', 2015, null, 'diesel', '1.6 MultiJet', 120)]],
            ]],

            'alfaromeo.com' => ['name' => 'Alfa Romeo', 'models' => [
                'Giulia'              => ['body' => 'saloon', 'variants' => [$g('952', 2016, null, 'petrol', '2.0 turbo', 280)]],
                'Giulia Quadrifoglio' => ['body' => 'saloon', 'variants' => [$g('952', 2016, null, 'petrol', '2.9 V6 BiTurbo', 510)]],
                'Stelvio'             => ['body' => 'SUV',    'variants' => [$g('949', 2017, null, 'petrol', '2.0 turbo', 280)]],
            ]],

            'mini.com' => ['name' => 'Mini', 'models' => [
                'Cooper'            => ['body' => 'hatch',     'variants' => [$g('F56', 2014, 2024, 'petrol', '1.5 B38', 136)]],
                'Cooper S'          => ['body' => 'hot hatch', 'variants' => [$g('F56', 2014, 2024, 'petrol', '2.0 B48', 192)]],
                'John Cooper Works' => ['body' => 'hot hatch', 'variants' => [$g('F56', 2015, 2024, 'petrol', '2.0 B48', 231)]],
                'Countryman'        => ['body' => 'SUV',       'variants' => [$g('F60', 2017, 2024, 'petrol', '2.0 B48', 192)]],
            ]],

            'landrover.com' => ['name' => 'Land Rover', 'models' => [
                'Defender'             => ['body' => 'SUV', 'variants' => [$g('L663', 2020, null, 'diesel', '2.0 D200', 200), $g('L663', 2020, null, 'petrol', '3.0 P400', 400)]],
                'Discovery'            => ['body' => 'SUV', 'variants' => [$g('L319', 2009, 2017, 'diesel', '3.0 TDV6', 256), $g('L462', 2017, null, 'diesel', '3.0 SDV6', 306)]],
                'Range Rover'          => ['body' => 'SUV', 'variants' => [$g('L405', 2012, 2021, 'diesel', '3.0 SDV6', 258), $g('L460', 2021, null, 'diesel', '3.0 D350', 350)]],
                'Range Rover Sport'    => ['body' => 'SUV', 'variants' => [$g('L494', 2013, 2022, 'diesel', '3.0 SDV6', 306), $g('L461', 2022, null, 'petrol', '3.0 P400', 400)]],
                'Range Rover Evoque'   => ['body' => 'SUV', 'variants' => [$g('L538', 2011, 2018, 'diesel', '2.0 TD4', 180), $g('L551', 2018, null, 'diesel', '2.0 D200', 204)]],
                'Range Rover Velar'    => ['body' => 'SUV', 'variants' => [$g('L560', 2017, null, 'diesel', '2.0 D200', 204)]],
            ]],

            'jaguar.com' => ['name' => 'Jaguar', 'models' => [
                'F-Pace' => ['body' => 'SUV',     'variants' => [$g('X761', 2016, null, 'diesel', '2.0 D200', 204)]],
                'F-Type' => ['body' => 'coupe',   'variants' => [$g('X152', 2013, 2024, 'petrol', '3.0 V6 SC', 380)]],
                'XE'     => ['body' => 'saloon',  'variants' => [$g('X760', 2015, 2024, 'diesel', '2.0 D200', 204)]],
                'XF'     => ['body' => 'saloon',  'variants' => [$g('X260', 2015, null, 'diesel', '2.0 D200', 204)]],
                'I-Pace' => ['body' => 'SUV',     'variants' => [$g('X590', 2018, null, 'electric', 'BEV', 400)]],
            ]],

            'volvocars.com' => ['name' => 'Volvo', 'models' => [
                'XC40' => ['body' => 'SUV',    'variants' => [$g('536', 2017, null, 'petrol', '2.0 B4', 197)]],
                'XC60' => ['body' => 'SUV',    'variants' => [$g('246', 2017, null, 'petrol', '2.0 B5', 250)]],
                'XC90' => ['body' => 'SUV',    'variants' => [$g('256', 2014, null, 'diesel', '2.0 D5', 235)]],
                'V60'  => ['body' => 'estate', 'variants' => [$g('225', 2018, null, 'diesel', '2.0 D4', 197)]],
                'V90'  => ['body' => 'estate', 'variants' => [$g('235', 2016, null, 'diesel', '2.0 D4', 197)]],
            ]],

            'toyota.com' => ['name' => 'Toyota', 'models' => [
                'Yaris'    => ['body' => 'hatch',     'variants' => [$g('XP130', 2011, 2020, 'petrol', '1.5 Hybrid', 100), $g('XP210', 2020, null, 'petrol', '1.5 Hybrid', 116)]],
                'GR Yaris' => ['body' => 'hot hatch', 'variants' => [$g('XP210', 2020, null, 'petrol', '1.6 G16E-GTS', 261)]],
                'Corolla'  => ['body' => 'hatch',     'variants' => [$g('E210', 2018, null, 'petrol', '2.0 Hybrid', 184)]],
                'RAV4'     => ['body' => 'SUV',       'variants' => [$g('XA50', 2018, null, 'petrol', '2.5 Hybrid', 218)]],
                'Hilux'    => ['body' => 'pickup',    'variants' => [$g('AN120', 2015, null, 'diesel', '2.8 D-4D', 204)]],
                'Supra'    => ['body' => 'coupe',     'variants' => [$g('A90', 2019, null, 'petrol', '3.0 B58', 340)]],
            ]],

            'lexus.com' => ['name' => 'Lexus', 'models' => [
                'IS'   => ['body' => 'saloon', 'variants' => [$g('XE30', 2013, null, 'petrol', '2.5 Hybrid', 223)]],
                'NX'   => ['body' => 'SUV',    'variants' => [$g('AZ20', 2021, null, 'petrol', '2.5 PHEV', 309)]],
                'RX'   => ['body' => 'SUV',    'variants' => [$g('AL30', 2022, null, 'petrol', '2.4 turbo', 366)]],
                'RC F' => ['body' => 'coupe',  'variants' => [$g('XC10', 2014, null, 'petrol', '5.0 V8', 477)]],
            ]],

            'honda.com' => ['name' => 'Honda', 'models' => [
                'Jazz'           => ['body' => 'hatch',     'variants' => [$g('GR', 2020, null, 'petrol', '1.5 Hybrid', 109)]],
                'Civic'          => ['body' => 'hatch',     'variants' => [$g('FK', 2017, 2022, 'petrol', '1.5 VTEC', 182), $g('FL', 2022, null, 'petrol', '2.0 Hybrid', 184)]],
                'Civic Type R'   => ['body' => 'hot hatch', 'variants' => [$g('FK8', 2017, 2021, 'petrol', '2.0 K20C1', 320), $g('FL5', 2022, null, 'petrol', '2.0 K20C1', 329)]],
                'CR-V'           => ['body' => 'SUV',       'variants' => [$g('RW', 2018, 2023, 'petrol', '1.5 VTEC', 193)]],
            ]],

            'mazda.com' => ['name' => 'Mazda', 'models' => [
                'Mazda2' => ['body' => 'hatch',  'variants' => [$g('DJ', 2014, null, 'petrol', '1.5 Skyactiv-G', 90)]],
                'Mazda3' => ['body' => 'hatch',  'variants' => [$g('BM', 2013, 2018, 'petrol', '2.0 Skyactiv-G', 165), $g('BP', 2019, null, 'petrol', '2.0 Skyactiv-X', 186)]],
                'Mazda6' => ['body' => 'saloon', 'variants' => [$g('GJ', 2012, null, 'diesel', '2.2 Skyactiv-D', 175)]],
                'CX-5'   => ['body' => 'SUV',    'variants' => [$g('KE', 2012, 2017, 'diesel', '2.2 Skyactiv-D', 175), $g('KF', 2017, null, 'petrol', '2.5 Skyactiv-G', 194)]],
                'MX-5'   => ['body' => 'roadster','variants'=> [$g('ND', 2015, null, 'petrol', '2.0 Skyactiv-G', 184)]],
            ]],

            'nissan.com' => ['name' => 'Nissan', 'models' => [
                'Micra'   => ['body' => 'hatch', 'variants' => [$g('K14', 2017, 2024, 'petrol', '0.9 IG-T', 90)]],
                'Juke'    => ['body' => 'SUV',   'variants' => [$g('F15', 2010, 2019, 'petrol', '1.6 DIG-T', 190), $g('F16', 2019, null, 'petrol', '1.0 DIG-T', 117)]],
                'Qashqai' => ['body' => 'SUV',   'variants' => [$g('J11', 2014, 2021, 'diesel', '1.5 dCi', 110), $g('J12', 2021, null, 'petrol', '1.3 DIG-T', 158)]],
                'X-Trail' => ['body' => 'SUV',   'variants' => [$g('T32', 2014, 2022, 'diesel', '1.6 dCi', 130)]],
                'GT-R'    => ['body' => 'coupe', 'variants' => [$g('R35', 2007, null, 'petrol', '3.8 VR38DETT', 570)]],
            ]],

            'subaru.com' => ['name' => 'Subaru', 'models' => [
                'Impreza WRX' => ['body' => 'saloon', 'variants' => [$g('GR', 2007, 2014, 'petrol', '2.5 EJ257', 300), $g('VA', 2014, 2021, 'petrol', '2.5 FA20', 305)]],
                'Forester'    => ['body' => 'SUV',    'variants' => [$g('SK', 2018, null, 'petrol', '2.5 FB25', 184)]],
                'Outback'     => ['body' => 'estate', 'variants' => [$g('BT', 2020, null, 'petrol', '2.5 FB25', 169)]],
                'BRZ'         => ['body' => 'coupe',  'variants' => [$g('ZD8', 2021, null, 'petrol', '2.4 FA24', 234)]],
            ]],

            'mitsubishi-motors.com' => ['name' => 'Mitsubishi', 'models' => [
                'Outlander'        => ['body' => 'SUV',     'variants' => [$g('GF', 2012, 2021, 'petrol', '2.4 PHEV', 224)]],
                'ASX'              => ['body' => 'SUV',     'variants' => [$g('GA', 2010, 2023, 'petrol', '1.6 MIVEC', 117)]],
                'L200'             => ['body' => 'pickup',  'variants' => [$g('KK', 2015, 2024, 'diesel', '2.4 turbo', 181)]],
                'Lancer Evolution' => ['body' => 'saloon',  'variants' => [$g('X', 2007, 2016, 'petrol', '2.0 4B11T', 295)]],
            ]],

            'hyundai.com' => ['name' => 'Hyundai', 'models' => [
                'i10'    => ['body' => 'hatch',     'variants' => [$g('AC3', 2019, null, 'petrol', '1.0 MPI', 67)]],
                'i20'    => ['body' => 'hatch',     'variants' => [$g('BC3', 2020, null, 'petrol', '1.0 T-GDI', 100)]],
                'i30'    => ['body' => 'hatch',     'variants' => [$g('PD', 2016, 2024, 'petrol', '1.4 T-GDI', 140)]],
                'i30 N'  => ['body' => 'hot hatch', 'variants' => [$g('PDe', 2017, 2024, 'petrol', '2.0 T-GDI', 280)]],
                'Tucson' => ['body' => 'SUV',       'variants' => [$g('TL', 2015, 2020, 'diesel', '2.0 CRDi', 185), $g('NX4', 2020, null, 'petrol', '1.6 T-GDI', 180)]],
            ]],

            'kia.com' => ['name' => 'Kia', 'models' => [
                'Picanto'  => ['body' => 'hatch',  'variants' => [$g('JA', 2017, null, 'petrol', '1.0 MPI', 67)]],
                'Rio'      => ['body' => 'hatch',  'variants' => [$g('YB', 2017, 2024, 'petrol', '1.0 T-GDI', 120)]],
                'Sportage' => ['body' => 'SUV',    'variants' => [$g('QL', 2015, 2021, 'diesel', '2.0 CRDi', 185), $g('NQ5', 2021, null, 'petrol', '1.6 T-GDI', 180)]],
                'Stinger'  => ['body' => 'saloon', 'variants' => [$g('CK', 2017, 2023, 'petrol', '3.3 V6 turbo', 370)]],
            ]],

            'tesla.com' => ['name' => 'Tesla', 'models' => [
                'Model 3' => ['body' => 'saloon', 'variants' => [$g('AWD', 2017, null, 'electric', 'BEV', 351), $g('Performance', 2018, null, 'electric', 'BEV', 510)]],
                'Model Y' => ['body' => 'SUV',    'variants' => [$g('AWD', 2020, null, 'electric', 'BEV', 384)]],
                'Model S' => ['body' => 'saloon', 'variants' => [$g('Plaid', 2021, null, 'electric', 'BEV', 1020)]],
                'Model X' => ['body' => 'SUV',    'variants' => [$g('Plaid', 2021, null, 'electric', 'BEV', 1020)]],
            ]],

            'dacia.com' => ['name' => 'Dacia', 'models' => [
                'Sandero' => ['body' => 'hatch', 'variants' => [$g('III', 2020, null, 'petrol', '1.0 TCe', 90)]],
                'Duster'  => ['body' => 'SUV',   'variants' => [$g('II', 2017, 2024, 'diesel', '1.5 dCi', 115)]],
                'Jogger'  => ['body' => 'estate','variants' => [$g('I', 2022, null, 'petrol', '1.0 TCe', 110)]],
            ]],
        ];
    }
}
