<?php

namespace App\Livewire;

use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use App\Notifications\TicketReply;
use Livewire\Component;

class AdminTickets extends Component
{
    public string $filter   = 'open';
    public string $search   = '';
    public ?int   $selected = null;
    public string $reply    = '';
    public bool   $internal = false;

    public function selectTicket(int $id): void
    {
        $this->selected = $id;
        $this->reply = '';
        $this->internal = false;
    }

    public function sendReply(): void
    {
        if (! $this->selected || trim($this->reply) === '') return;

        $ticket = Ticket::findOrFail($this->selected);

        TicketMessage::create([
            'ticket_id'   => $ticket->id,
            'user_id'     => auth()->id(),
            'body'        => trim($this->reply),
            'is_internal' => $this->internal,
        ]);

        if (! $ticket->assigned_to_id) {
            $ticket->update(['assigned_to_id' => auth()->id()]);
        }

        if (!$this->internal && $ticket->customer) {
            $ticket->customer->notify(new TicketReply($ticket));
        }

        $ticket->touch();
        $this->reply = '';
        $this->internal = false;
    }

    public function assignToMe(): void
    {
        if (! $this->selected) return;
        Ticket::findOrFail($this->selected)->update(['assigned_to_id' => auth()->id()]);
    }

    public function resolveTicket(): void
    {
        if (! $this->selected) return;
        Ticket::findOrFail($this->selected)->update(['status' => 'resolved']);
    }

    public function reopenTicket(): void
    {
        if (! $this->selected) return;
        Ticket::findOrFail($this->selected)->update(['status' => 'open']);
    }

    public function setPriority(string $priority): void
    {
        if (! $this->selected) return;
        if (! in_array($priority, ['low', 'normal', 'high', 'urgent'])) return;
        Ticket::findOrFail($this->selected)->update(['priority' => $priority]);
    }

    public function render()
    {
        $q = Ticket::with('customer:id,name', 'assignedTo:id,name,status', 'order:id,reference')
            ->withCount('messages');

        if ($this->filter !== 'all') {
            $q->where('status', $this->filter);
        }
        if ($this->search !== '') {
            $needle = $this->search;
            $q->where(function ($qq) use ($needle) {
                $qq->where('subject', 'like', "%{$needle}%")
                   ->orWhereHas('customer', fn ($qqq) => $qqq->where('name', 'like', "%{$needle}%"));
            });
        }

        $tickets = $q->orderByDesc('updated_at')->get();

        if ($tickets->isNotEmpty() && (! $this->selected || ! $tickets->contains('id', $this->selected))) {
            $this->selected = $tickets->first()->id;
        }

        $selTicket = $this->selected
            ? Ticket::with('customer:id,name', 'assignedTo:id,name,status', 'order:id,reference')
                ->find($this->selected)
            : null;

        $messages = $selTicket
            ? $selTicket->messages()->with('user:id,name,status')->orderBy('created_at')->get()
            : collect();

        $staffUsers = User::role(['admin', 'operations', 'tuner'])
            ->select('id', 'name', 'status')
            ->orderBy('name')
            ->get();

        return view('livewire.admin-tickets', [
            'tickets'    => $tickets,
            'selTicket'  => $selTicket,
            'messages'   => $messages,
            'staffUsers' => $staffUsers,
        ]);
    }
}
