<?php

namespace App\Livewire;

use App\Models\Ticket;
use App\Models\TicketMessage;
use Livewire\Component;

class CustomerTicketThread extends Component
{
    public Ticket $ticket;
    public string $reply = '';

    public function mount(int $ticketId): void
    {
        $this->ticket = Ticket::findOrFail($ticketId);
        abort_unless(
            $this->ticket->customer_id === auth()->id() || auth()->user()->isAdmin(),
            403
        );
    }

    public function sendReply(): void
    {
        $this->validate([
            'reply' => 'required|string|max:5000',
        ]);

        TicketMessage::create([
            'ticket_id'   => $this->ticket->id,
            'user_id'     => auth()->id(),
            'body'        => $this->reply,
            'is_internal' => false,
        ]);

        $this->ticket->touch();
        $this->reply = '';
    }

    public function render()
    {
        $messages = $this->ticket->messages()
            ->where('is_internal', false)
            ->with('user')
            ->orderBy('created_at')
            ->get();

        $assignee = $this->ticket->assignedTo;

        return view('livewire.customer-ticket-thread', [
            'messages' => $messages,
            'assignee' => $assignee,
        ]);
    }
}
