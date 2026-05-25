<?php

namespace App\Livewire;

use App\Models\CustomerProfile;
use App\Models\Dispute;
use App\Models\Order;
use App\Models\ResellerProfile;
use App\Models\User;
use Livewire\Component;

class CustomersScreen extends Component
{
    public string $filter   = 'all';
    public string $search   = '';
    public ?int   $selected = null;

    public function selectCustomer(int $id): void
    {
        $this->selected = $id;
    }

    public function toggleCanInvoice(int $userId): void
    {
        $profile = CustomerProfile::where('user_id', $userId)->first();
        if ($profile) {
            $profile->update(['can_invoice' => ! $profile->can_invoice]);
        }
    }

    public function makeReseller(int $userId): void
    {
        $user = User::findOrFail($userId);
        $user->syncRoles(['customer', 'reseller', 'tuner']);

        ResellerProfile::firstOrCreate(
            ['user_id' => $user->id],
            [
                'business_name' => $user->name . "'s Tuning",
                'slug' => \Illuminate\Support\Str::slug($user->name . '-tuning-' . $user->id),
                'is_active' => true,
            ]
        );
    }

    public function removeReseller(int $userId): void
    {
        $user = User::findOrFail($userId);
        $user->removeRole('reseller');
        $user->removeRole('tuner');
    }

    public function render()
    {
        $q = User::role('customer')->with('customerProfile')->withCount('orders');

        if ($this->search !== '') {
            $q->where('name', 'like', '%'.$this->search.'%');
        }
        if (in_array($this->filter, ['Pro','Trade','VIP'], true)) {
            $q->whereHas('customerProfile', fn ($x) => $x->where('plan', $this->filter));
        }
        if ($this->filter === 'flag') {
            $q->whereExists(function ($x) {
                $x->select('id')->from('orders')->whereColumn('orders.customer_id', 'users.id')
                  ->whereExists(function ($y) {
                      $y->select('id')->from('disputes')->whereColumn('disputes.order_id', 'orders.id')->where('disputes.status', 'open');
                  });
            });
        }

        $customers = $q->orderByDesc('orders_count')->get();

        if ($this->selected && $customers->whereStrict('id', $this->selected)->isEmpty()) {
            $this->selected = null;
        }

        $sel = $this->selected
            ? User::with('customerProfile')->withCount('orders')->find($this->selected)
            : null;

        $orders = $sel
            ? Order::where('customer_id', $sel->id)->with('vehicle', 'ecu')->orderByDesc('reference')->limit(12)->get()
            : collect();

        $disputesCount = $sel ? Dispute::whereIn('order_id', $orders->pluck('id'))->where('status', 'open')->count() : 0;
        $refundsTotal  = $sel ? Order::where('customer_id', $sel->id)->where('status', 'refunded')->sum('credits_cost') : 0;

        return view('livewire.customers-screen', [
            'customers'     => $customers,
            'sel'           => $sel,
            'orders'        => $orders,
            'disputesCount' => $disputesCount,
            'refundsTotal'  => $refundsTotal,
        ]);
    }
}
