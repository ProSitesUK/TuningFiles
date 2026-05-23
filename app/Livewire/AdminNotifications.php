<?php

namespace App\Livewire;

use App\Models\Dispute;
use App\Models\Order;
use App\Models\Ticket;
use Livewire\Component;

class AdminNotifications extends Component
{
    public function render()
    {
        $items = collect();

        // Queued orders in last 24h
        Order::where('status', 'queued')
            ->where('created_at', '>=', now()->subDay())
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->each(fn ($o) => $items->push([
                'icon'  => 'queue',
                'text'  => "Order #{$o->reference} queued — {$o->vehicle_label}",
                'url'   => route('admin.queue'),
                'time'  => $o->created_at,
            ]));

        // SLA breaches
        Order::where('breach', true)
            ->whereIn('status', ['in_progress', 'review', 'queued'])
            ->orderByDesc('updated_at')
            ->limit(3)
            ->get()
            ->each(fn ($o) => $items->push([
                'icon'  => 'disputes',
                'text'  => "SLA breach on #{$o->reference}",
                'url'   => route('admin.queue'),
                'time'  => $o->updated_at,
            ]));

        // Open tickets (unassigned or assigned to me)
        Ticket::where('status', 'open')
            ->where(fn ($q) => $q->whereNull('assigned_to_id')->orWhere('assigned_to_id', auth()->id()))
            ->orderByDesc('updated_at')
            ->limit(3)
            ->get()
            ->each(fn ($t) => $items->push([
                'icon'  => 'tickets',
                'text'  => "Ticket: {$t->subject}",
                'url'   => route('admin.tickets'),
                'time'  => $t->updated_at,
            ]));

        // Open disputes
        Dispute::where('status', 'open')
            ->orderByDesc('created_at')
            ->limit(3)
            ->get()
            ->each(fn ($d) => $items->push([
                'icon'  => 'disputes',
                'text'  => "Dispute opened — order #{$d->order?->reference}",
                'url'   => route('admin.disputes'),
                'time'  => $d->created_at,
            ]));

        $items = $items->sortByDesc('time')->take(10)->values();

        return view('livewire.admin-notifications', [
            'items' => $items,
            'count' => $items->count(),
        ]);
    }
}
