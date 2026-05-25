<?php

namespace App\Livewire;

use App\Models\CreditTransaction;
use App\Models\Order;
use App\Models\Referral;
use App\Models\ResellerProfile;
use App\Models\User;
use Livewire\Component;

class AdminResellers extends Component
{
    public ?int $selected = null;
    public string $search = '';

    public function selectReseller(int $userId): void
    {
        $this->selected = $userId;
    }

    public function render()
    {
        $resellers = ResellerProfile::query()
            ->with('user:id,name,email')
            ->when($this->search, fn ($q) => $q->where('business_name', 'like', '%'.$this->search.'%'))
            ->orderBy('business_name')
            ->get()
            ->map(function ($r) {
                $r->customer_count = User::where('reseller_id', $r->user_id)->count();
                $r->order_count = Order::where('reseller_id', $r->user_id)->count();
                $r->revenue_pennies = CreditTransaction::whereIn('user_id',
                    User::where('reseller_id', $r->user_id)->pluck('id')
                )->where('type', 'purchase')->sum('amount_pennies');
                return $r;
            });

        // KPIs
        $totalResellers = ResellerProfile::where('is_active', true)->count();
        $totalSubCustomers = User::whereNotNull('reseller_id')->count();
        $totalResellerOrders = Order::whereNotNull('reseller_id')->count();
        $totalResellerRevenue = 0;
        foreach ($resellers as $r) {
            $totalResellerRevenue += $r->revenue_pennies;
        }

        // Selected reseller detail
        $selReseller = $this->selected
            ? $resellers->firstWhere('user_id', $this->selected)
            : null;

        $selCustomers = $selReseller
            ? User::where('reseller_id', $selReseller->user_id)
                ->with('customerProfile')
                ->withCount('orders')
                ->orderByDesc('orders_count')
                ->limit(10)->get()
            : collect();

        $selRecentOrders = $selReseller
            ? Order::where('reseller_id', $selReseller->user_id)
                ->with('customer:id,name')
                ->orderByDesc('created_at')
                ->limit(10)->get()
            : collect();

        $selCommissionPaid = $selReseller
            ? CreditTransaction::where('type', 'promo')
                ->whereNotNull('referral_id')
                ->whereHas('referral', fn ($q) => $q->where('referrer_id', $selReseller->user_id))
                ->sum('amount_pennies')
            : 0;

        return view('livewire.admin-resellers', [
            'resellers' => $resellers,
            'totalResellers' => $totalResellers,
            'totalSubCustomers' => $totalSubCustomers,
            'totalResellerOrders' => $totalResellerOrders,
            'totalResellerRevenue' => $totalResellerRevenue,
            'selReseller' => $selReseller,
            'selCustomers' => $selCustomers,
            'selRecentOrders' => $selRecentOrders,
            'selCommissionPaid' => $selCommissionPaid,
        ]);
    }
}
