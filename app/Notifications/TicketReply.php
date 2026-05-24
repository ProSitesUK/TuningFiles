<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketReply extends Notification
{
    use Queueable;

    public function __construct(public Ticket $ticket) {}

    public function via(object $notifiable): array { return ['mail', 'database']; }

    public function toMail(object $notifiable): MailMessage
    {
        $t = $this->ticket;
        $latestMessage = $t->messages()->latest()->first();
        $preview = $latestMessage ? \Illuminate\Support\Str::limit($latestMessage->body, 200) : '';

        return (new MailMessage)
            ->subject("Reply on ticket: {$t->subject}")
            ->greeting("Hi {$notifiable->name},")
            ->line("There's a new reply on your ticket \"{$t->subject}\":")
            ->line($preview)
            ->action('View ticket', route('app.tickets.show', $t))
            ->line('Reply to this ticket directly from your dashboard.');
    }

    public function toDatabase(object $notifiable): array
    {
        $t = $this->ticket;
        return [
            'message' => "New reply on: {$t->subject}",
            'url'     => route('app.tickets.show', $t),
            'icon'    => 'tickets',
        ];
    }
}
