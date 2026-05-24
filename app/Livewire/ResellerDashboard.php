<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\User;
use Livewire\Component;

class ResellerDashboard extends Component
{
    public function render()
    {
        $resellerId = auth()->id();

        $totalCustomers = auth()->user()->subCustomers()->count();
        $totalOrders = Order::where('reseller_id', $resellerId)->count();
        $ordersThisMonth = Order::where('reseller_id', $resellerId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $activeCustomerIds = Order::where('reseller_id', $resellerId)
            ->where('created_at', '>=', now()->subDays(30))
            ->distinct('customer_id')
            ->pluck('customer_id');
        $activeCustomers = $activeCustomerIds->count();

        $recentOrders = Order::where('reseller_id', $resellerId)
            ->with('customer:id,name')
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        return view('livewire.reseller-dashboard', [
            'totalCustomers'  => $totalCustomers,
            'totalOrders'     => $totalOrders,
            'ordersThisMonth' => $ordersThisMonth,
            'activeCustomers' => $activeCustomers,
            'recentOrders'    => $recentOrders,
        ]);
    }
}
