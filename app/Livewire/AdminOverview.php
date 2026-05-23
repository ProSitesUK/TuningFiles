<?php

namespace App\Livewire;

use App\Models\CustomerProfile;
use App\Support\Charts;
use Livewire\Component;

class AdminOverview extends Component
{
    public string $range = '7d';

    public function render()
    {
        $topCustomers = CustomerProfile::with('user')
            ->orderByDesc('total_spent_pennies')
            ->limit(7)
            ->get();

        $deltas = [18, 9, 22, 4, 2, 1, -1]; // mirrors prototype

        return view('livewire.admin-overview', [
            'topCustomers' => $topCustomers,
            'deltas'       => $deltas,
            'series'       => [
                'orders'     => Charts::ORDERS_14D,
                'revenue'    => Charts::REVENUE_14D,
                'turnaround' => Charts::TURNAROUND_14D,
                'tuners'     => Charts::TUNERS_14D,
                'disputes'   => Charts::DISPUTES_14D,
            ],
            'activity' => [
                ['ok',   'order #4471 delivered to Jamie M.',  'just now'],
                ['err',  'dispute opened on #4421',            '38s ago'],
                ['mute', 'new customer · K. Holm signed up',    '1m ago'],
                ['ok',   'tuner Aleks finished #4470',          '2m ago'],
                ['warn', 'SLA breach risk on #4466',            '4m ago'],
                ['ok',   'refund £40 processed · #4464',        '6m ago'],
                ['mute', 'credit pack +250 bought by Priya N.', '9m ago'],
                ['ok',   'tuner Mira accepted #4476',           '11m ago'],
                ['mute', 'ticket #882 closed',                  '14m ago'],
            ],
        ]);
    }
}
