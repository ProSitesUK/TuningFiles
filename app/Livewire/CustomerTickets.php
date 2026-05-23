<?php

namespace App\Livewire;

use App\Models\Ticket;
use Livewire\Component;

class CustomerTickets extends Component
{
    public string $filter = 'all';

    public function setFilter(string $filter): void
    {
        $this->filter = $filter;
    }

    public function render()
    {
        $query = Ticket::where('customer_id', auth()->id())
            ->with(['order', 'messages' => fn ($q) => $q->latest()->limit(1)]);

        if ($this->filter === 'open') {
            $query->where('status', 'open');
        } elseif ($this->filter === 'resolved') {
            $query->where('status', 'resolved');
        }

        $tickets = $query->latest('updated_at')->get();

        return view('livewire.customer-tickets', ['tickets' => $tickets]);
    }
}
