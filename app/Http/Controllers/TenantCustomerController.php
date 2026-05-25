<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ResellerProfile;

class TenantCustomerController extends Controller
{
    private function verifyAccess(ResellerProfile $tenant): void
    {
        abort_unless($tenant->is_active, 404);
        abort_unless(auth()->user()->reseller_id === $tenant->user_id, 403);
    }

    public function dashboard(ResellerProfile $tenant)
    {
        $this->verifyAccess($tenant);

        return view('tenant.dashboard', ['tenant' => $tenant]);
    }

    public function orders(ResellerProfile $tenant)
    {
        $this->verifyAccess($tenant);

        return view('tenant.orders.index', ['tenant' => $tenant]);
    }

    public function newOrder(ResellerProfile $tenant)
    {
        $this->verifyAccess($tenant);

        return view('tenant.orders.new', ['tenant' => $tenant]);
    }

    public function showOrder(ResellerProfile $tenant, Order $order)
    {
        $this->verifyAccess($tenant);
        abort_unless($order->customer_id === auth()->id(), 403);

        return view('tenant.orders.show', ['tenant' => $tenant, 'order' => $order]);
    }

    public function credits(ResellerProfile $tenant)
    {
        $this->verifyAccess($tenant);

        return view('tenant.credits', ['tenant' => $tenant]);
    }

    public function tickets(ResellerProfile $tenant)
    {
        $this->verifyAccess($tenant);

        return view('tenant.tickets', ['tenant' => $tenant]);
    }
}
