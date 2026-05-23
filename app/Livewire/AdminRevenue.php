<?php

namespace App\Livewire;

use App\Models\CreditTransaction;
use App\Models\Order;
use Livewire\Component;

class AdminRevenue extends Component
{
    public string $filter = 'all';

    public function render()
    {
        // KPIs
        $totalRevenue = CreditTransaction::where('type', 'purchase')->sum('amount_pennies');

        $avgOrderValue = Order::whereNotNull('credits_cost')->avg('credits_cost');

        $totalOrders = Order::count();
        $refundedOrders = Order::where('status', 'refunded')->count();
        $refundRate = $totalOrders > 0 ? round(($refundedOrders / $totalOrders) * 100, 1) : 0;

        $activeCustomers = Order::distinct('customer_id')->count('customer_id');

        // Transaction log
        $txQuery = CreditTransaction::with('user:id,name')
            ->orderByDesc('created_at');

        if ($this->filter !== 'all') {
            $txQuery->where('type', $this->filter);
        }

        $transactions = $txQuery->limit(100)->get();

        return view('livewire.admin-revenue', [
            'totalRevenue'    => $totalRevenue,
            'avgOrderValue'   => $avgOrderValue,
            'refundRate'      => $refundRate,
            'activeCustomers' => $activeCustomers,
            'transactions'    => $transactions,
        ]);
    }
}
