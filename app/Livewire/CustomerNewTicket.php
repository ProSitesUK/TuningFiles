<?php

namespace App\Livewire;

use App\Models\Ticket;
use App\Models\TicketMessage;
use Livewire\Component;

class CustomerNewTicket extends Component
{
    public string $subject = '';
    public string $body = '';
    public ?int $order_id = null;

    public function mount(): void
    {
        if (request()->has('order')) {
            $orderId = (int) request()->query('order');
            // Verify the order belongs to this customer
            $owns = auth()->user()->orders()->where('id', $orderId)->exists();
            if ($owns) {
                $this->order_id = $orderId;
            }
        }
    }

    public function submit()
    {
        $this->validate([
            'subject' => 'required|string|max:255',
            'body'    => 'required|string|max:5000',
            'order_id' => 'nullable|exists:orders,id',
        ]);

        // If order_id given, confirm it belongs to this customer
        if ($this->order_id) {
            $owns = auth()->user()->orders()->where('id', $this->order_id)->exists();
            if (! $owns) {
                $this->order_id = null;
            }
        }

        $ticket = Ticket::create([
            'customer_id' => auth()->id(),
            'order_id'    => $this->order_id,
            'subject'     => $this->subject,
            'status'      => 'open',
            'priority'    => 'normal',
        ]);

        TicketMessage::create([
            'ticket_id'   => $ticket->id,
            'user_id'     => auth()->id(),
            'body'        => $this->body,
            'is_internal' => false,
        ]);

        return redirect()->route('app.tickets.show', $ticket);
    }

    public function render()
    {
        $orders = auth()->user()->orders()->latest('created_at')->get(['id', 'reference', 'vehicle_label']);

        return view('livewire.customer-new-ticket', ['orders' => $orders]);
    }
}
