<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderRefunded extends Notification
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via(object $notifiable): array { return ['mail']; }

    public function toMail(object $notifiable): MailMessage
    {
        $o = $this->order;
        return (new MailMessage)
            ->subject("Order #{$o->reference} refunded")
            ->greeting("Hi {$notifiable->name},")
            ->line("Order #{$o->reference} ({$o->vehicle_label}) has been refunded.")
            ->line("We have restored {$o->credits_cost} credits to your account.")
            ->action('See your credits', route('app.credits'))
            ->line('Sorry we could not get this one right — replies welcome.');
    }
}
