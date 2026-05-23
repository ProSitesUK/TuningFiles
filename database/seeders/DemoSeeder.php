<?php

namespace Database\Seeders;

use App\Models\CustomerProfile;
use App\Models\Ecu;
use App\Models\Order;
use App\Models\OrderEvent;
use App\Models\TunerProfile;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // -------------------- Customers (match prototype data.js) --------------------
        $customers = [
            // [name,             email,                   plan,    since,        orders, revenue, disputes, refunds, credits, country]
            ['Jamie Marshall',  'jamie@example.com',     'Pro',   'Aug 2023', 42,  1840, 1, 0,  248, 'UK'],
            ['Priya Nair',      'priya@example.com',     'Trade', 'Mar 2022', 118, 6420, 0, 40, 92,  'UK'],
            ['Tom Hadley',      'tom@example.com',       'Pro',   'Jun 2024', 11,  420,  0, 0,  14,  'UK'],
            ['Sara Kemp',       'sara@example.com',      'VIP',   'Jan 2021', 86,  4210, 0, 0,  312, 'DE'],
            ['Mo Idris',        'mo@example.com',        'Pro',   'Sep 2024', 9,   380,  0, 0,  8,   'UK'],
            ['C. Whitman',      'whitman@example.com',   'Pro',   'Feb 2023', 24,  1120, 0, 0,  56,  'US'],
            ['Hae-Jin Kim',     'haejin@example.com',    'Trade', 'Nov 2022', 62,  3140, 0, 0,  188, 'KR'],
            ['R. Banerjee',     'banerjee@example.com',  'Pro',   'May 2024', 6,   240,  1, 0,  4,   'IN'],
            ['L. Petrov',       'petrov@example.com',    'Pro',   'Oct 2023', 18,  740,  0, 0,  42,  'DE'],
            ['M. Solis',        'solis@example.com',     'Trade', 'Apr 2022', 38,  1420, 0, 0,  96,  'ES'],
            ['K. Holm',         'holm@example.com',      'Pro',   'May 2026', 1,   32,   0, 0,  0,   'DK'],
            ['N. Bauer',        'bauer@example.com',     'Pro',   'Jul 2024', 14,  560,  0, 0,  28,  'DE'],
        ];

        foreach ($customers as [$name, $email, $plan, $since, $orderCount, $rev, $disp, $ref, $credits, $country]) {
            $u = User::firstOrCreate(['email' => $email], ['name' => $name, 'password' => Hash::make('password')]);
            $u->syncRoles(['customer']);
            CustomerProfile::updateOrCreate(
                ['user_id' => $u->id],
                [
                    'plan'                => $plan,
                    'credit_balance'      => $credits,
                    'total_spent_pennies' => $rev * 100,
                    'country'             => substr($country, 0, 2),
                    'since_at'            => $this->parseSince($since),
                ]
            );
        }

        // -------------------- Tuners (match prototype) --------------------
        $tuners = [
            ['Aleks R.', 'aleks@tuningfiles.test', 'live',  4, 5, 4, '0m'],
            ['Mira T.',  'mira@tuningfiles.test',  'live',  3, 5, 3, '2m'],
            ['Lee O.',   'lee@tuningfiles.test',   'live',  1, 4, 1, '18m'],
            ['Hugo D.',  'hugo@tuningfiles.test',  'busy',  0, 4, 0, '1h'],
            ['Sara P.',  'sarap@tuningfiles.test', 'live',  2, 5, 2, '5m'],
            ['Ravi K.',  'ravi@tuningfiles.test',  'live',  3, 4, 3, '0m'],
            ['Niko B.',  'niko@tuningfiles.test',  'away',  0, 3, 0, '3h'],
            ['Yuki H.',  'yuki@tuningfiles.test',  'off',   0, 4, 0, '—'],
        ];

        foreach ($tuners as [$name, $email, $status, $workload, $cap, $active, $idle]) {
            $u = User::firstOrCreate(['email' => $email], ['name' => $name, 'password' => Hash::make('password')]);
            $u->syncRoles(['tuner']);
            TunerProfile::updateOrCreate(
                ['user_id' => $u->id],
                [
                    'status'       => $status,
                    'workload'     => $workload,
                    'capacity'     => $cap,
                    'active_count' => $active,
                    'idle'         => $idle,
                    'last_active_at' => $status === 'live' ? now() : now()->subHours(rand(1, 12)),
                ]
            );
        }

        // -------------------- Orders (match prototype #4471 -> #4460) --------------------
        $u  = fn (string $email) => User::where('email', $email)->value('id');
        $vi = fn (string $model) => Vehicle::where('model', $model)->value('id');
        $ei = fn (string $ident) => Ecu::where('identifier', $ident)->value('id');

        $orders = [
            // [ref, customer_email, vehicle_label, year, vehicle_model_key, ecu_label, ecu_identifier, options_label, options_array, status, tuner_email|null, credits, ageMin, origin, fileSize, sla, progress, breach]
            [4471, 'jamie@example.com',   'Golf R MK7',   2018, 'Golf R',       'Bosch MED17.1.62',  'Bosch MED17.1.62',  'Stage 1 + EGR off', ['stage_1','egr_off'], 'in_progress', 'aleks@tuningfiles.test', 32, 14,  'customer upload', '1.24 MB', '30m', 0.74, false],
            [4470, 'priya@example.com',   'BMW 335i',     2013, '335i',         'Bosch MEVD17.2.G', 'Bosch MEVD17.2.G', 'Stage 2',           ['stage_2'],            'in_progress', 'mira@tuningfiles.test',  40, 22,  'customer upload', '1.61 MB', '30m', 0.50, false],
            [4469, 'tom@example.com',     'Audi A6 3.0 TDI', 2017, 'A6 3.0 TDI', 'EDC17 CP44',       'EDC17 CP44',       'DPF off',           ['dpf_off'],            'in_progress', 'aleks@tuningfiles.test', 28, 31,  'trade portal',    '1.08 MB', '30m', 0.80, false],
            [4468, 'sara@example.com',    'Audi RS3 8V',  2019, 'RS3 8V',       'MED17.5.25',       'MED17.5.25',       'Stage 1',           ['stage_1'],            'review',      'mira@tuningfiles.test',  32, 42,  'VIP',             '1.92 MB', '60m', 1.0,  false],
            [4467, 'mo@example.com',      'Defender 2.0', 2022, 'Defender 2.0', 'Bosch MG1CS201',   'Bosch MG1CS201',   'Custom remap',      ['custom'],             'queued',      null,                     55, 64,  'customer upload', '2.32 MB', '4h',  0.0,  false],
            [4466, 'whitman@example.com', 'BMW M140i F20',2017, 'M140i F20',    'MEVD17.2.6',       'MEVD17.2.6',       'Pops & bangs',      ['pops'],               'in_progress', 'lee@tuningfiles.test',   18, 72,  'customer upload', '1.45 MB', '60m', 0.30, true],
            [4465, 'haejin@example.com',  'VW Polo GTI',  2020, 'Polo GTI',     'MED17.5.21',       'MED17.5.21',       'Stage 1',           ['stage_1'],            'delivered',   'lee@tuningfiles.test',   28, 158, 'trade portal',    '1.22 MB', '60m', 1.0,  false],
            [4464, 'banerjee@example.com','VW Polo GTI',  2019, 'Polo GTI',     'MED17.5.21',       'MED17.5.21',       'Stage 1',           ['stage_1'],            'refunded',    'lee@tuningfiles.test',   0,  182, 'customer upload', '1.20 MB', '60m', 1.0,  false],
            [4463, 'petrov@example.com',  'Audi A4 B9',   2017, 'A4 B9',        'MED17.1.62',       'Bosch MED17.1.62', 'Stage 1 + pops',    ['stage_1','pops'],     'delivered',   'sarap@tuningfiles.test', 36, 192, 'customer upload', '1.34 MB', '60m', 1.0,  false],
            [4462, 'solis@example.com',   'Seat Leon Cupra',2018,'Leon Cupra',  'MED17.5.25',       'MED17.5.25',       'Stage 2',           ['stage_2'],            'delivered',   'mira@tuningfiles.test',  44, 242, 'trade portal',    '1.72 MB', '60m', 1.0,  false],
            [4461, 'holm@example.com',    'Porsche Cayman 718',2021,'Cayman 718','MED17.1.21',      'MED17.1.21',       'Stage 2',           ['stage_2'],            'delivered',   'sarap@tuningfiles.test', 60, 321, 'VIP',             '2.04 MB', '60m', 1.0,  false],
            [4460, 'bauer@example.com',   'Skoda Octavia vRS',2019,'Octavia vRS','MED17.5.25',      'MED17.5.25',       'Stage 1',           ['stage_1'],            'delivered',   'ravi@tuningfiles.test',  28, 350, 'customer upload', '1.28 MB', '60m', 1.0,  false],
        ];

        foreach ($orders as $row) {
            [$ref, $custE, $vehLabel, $year, $vehKey, $ecuLabel, $ecuIdent, $optsLabel, $opts, $status, $tunerE, $credits, $ageMin, $origin, $fileSize, $sla, $progress, $breach] = $row;
            $createdAt = now()->subMinutes($ageMin);

            $order = Order::firstOrCreate(
                ['reference' => (string) $ref],
                [
                    'customer_id'       => $u($custE),
                    'vehicle_id'        => $vi($vehKey),
                    'ecu_id'            => $ei($ecuIdent),
                    'assigned_tuner_id' => $tunerE ? $u($tunerE) : null,
                    'status'            => $status,
                    'origin'            => $origin,
                    'vehicle_label'     => $vehLabel,
                    'vehicle_year'      => $year,
                    'ecu_label'         => $ecuLabel,
                    'options_label'     => $optsLabel,
                    'options'           => $opts,
                    'credits_cost'      => $credits,
                    'file_size'         => $fileSize,
                    'sla'               => $sla,
                    'progress'          => $progress,
                    'breach'            => $breach,
                    'queued_at'         => $createdAt,
                    'assigned_at'       => $tunerE ? $createdAt->copy()->addMinutes(2) : null,
                    'started_at'        => in_array($status, ['in_progress','review','ready','delivered'], true) ? $createdAt->copy()->addMinutes(5) : null,
                    'review_at'         => in_array($status, ['review','ready','delivered'], true)             ? $createdAt->copy()->addMinutes(15) : null,
                    'ready_at'          => in_array($status, ['ready','delivered'], true)                      ? $createdAt->copy()->addMinutes(22) : null,
                    'delivered_at'      => $status === 'delivered' ? $createdAt->copy()->addMinutes(rand(25, 60)) : null,
                    'refunded_at'       => $status === 'refunded'  ? $createdAt->copy()->addHours(2)             : null,
                    'sla_due_at'        => $createdAt->copy()->addMinutes(30),
                    'created_at'        => $createdAt,
                    'updated_at'        => $createdAt,
                ]
            );

            if ($order->events()->exists()) continue;

            $tunerId = $tunerE ? $u($tunerE) : null;
            $events = [];
            $events[] = ['stage' => 'file received', 'state' => 'done', 'note' => "{$origin} · {$fileSize} · md5 ok", 'happened_at' => $createdAt];
            $events[] = ['stage' => 'validated',     'state' => $status === 'queued' ? 'pending' : 'done', 'note' => "ECU id matches {$ecuIdent}", 'happened_at' => $createdAt->copy()->addMinute()];
            if ($tunerE) {
                $tname = User::where('email', $tunerE)->value('name');
                $events[] = ['stage' => 'assigned', 'state' => 'done', 'note' => "auto → {$tname}", 'happened_at' => $createdAt->copy()->addMinutes(2)];
            }
            if (in_array($status, ['in_progress','review','ready','delivered'], true)) {
                $events[] = ['stage' => 'tuning in progress', 'state' => $status === 'in_progress' ? 'active' : 'done', 'note' => "{$optsLabel} applied · ".(int)($progress * 100).'%', 'happened_at' => $createdAt->copy()->addMinutes(5)];
            }
            if (in_array($status, ['review','ready','delivered'], true)) {
                $events[] = ['stage' => 'review', 'state' => $status === 'review' ? 'active' : 'done', 'note' => $status === 'review' ? 'pending QA' : 'approved', 'happened_at' => $createdAt->copy()->addMinutes(15)];
            }
            if (in_array($status, ['ready','delivered'], true)) {
                $events[] = ['stage' => 'delivery', 'state' => 'done', 'note' => 'file sent to customer', 'happened_at' => $order->delivered_at ?? $createdAt->copy()->addMinutes(22)];
            }
            if ($status === 'refunded') {
                $events[] = ['stage' => 'refund', 'state' => 'done', 'note' => 'credits restored', 'happened_at' => $order->refunded_at];
            }

            foreach ($events as $e) {
                OrderEvent::create(array_merge(['order_id' => $order->id, 'actor_id' => $tunerId], $e));
            }
        }
    }

    private function parseSince(string $label): Carbon
    {
        // "Aug 2023" -> Carbon
        try { return Carbon::createFromFormat('M Y', $label)->startOfMonth(); }
        catch (\Throwable) { return now()->subMonths(rand(3, 36)); }
    }
}
