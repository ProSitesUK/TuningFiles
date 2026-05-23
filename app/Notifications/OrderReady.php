<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderReady extends Notification
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via(object $notifiable): array { return ['mail']; }

    public function toMail(object $notifiable): MailMessage
    {
        $o = $this->order;
        return (new MailMessage)
            ->subject("Order #{$o->reference} is ready")
            ->greeting("Hi {$notifiable->name},")
            ->line("Your tuned file for {$o->vehicle_label} ({$o->options_label}) is ready to download.")
            ->action('Download tuned file', route('app.orders.show', $o))
            ->line('Revisions are free for 30 days — just reply to this email if you need anything tweaked.');
    }
}
