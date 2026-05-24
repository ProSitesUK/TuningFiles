<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderQueued extends Notification
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via(object $notifiable): array { return ['mail', 'database']; }

    public function toMail(object $notifiable): MailMessage
    {
        $o = $this->order;
        return (new MailMessage)
            ->subject("Order #{$o->reference} is queued")
            ->greeting("Hi {$notifiable->name},")
            ->line('Your file has been uploaded and is in the queue. We\'ll notify you when a tuner picks it up.')
            ->action('View order', route('app.orders.show', $o))
            ->line('Thank you for choosing tuningfiles.');
    }

    public function toDatabase(object $notifiable): array
    {
        $o = $this->order;
        return [
            'message' => "Order #{$o->reference} queued",
            'url'     => route('app.orders.show', $o),
            'icon'    => 'queue',
        ];
    }
}
