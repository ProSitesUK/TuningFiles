<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TunerAssigned extends Notification
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via(object $notifiable): array { return ['mail', 'database']; }

    public function toMail(object $notifiable): MailMessage
    {
        $o = $this->order;
        $tunerName = $o->assignedTuner?->name ?? 'a tuner';
        return (new MailMessage)
            ->subject("A tuner is working on #{$o->reference}")
            ->greeting("Hi {$notifiable->name},")
            ->line("{$tunerName} has been assigned to your order #{$o->reference} ({$o->vehicle_label}). Estimated delivery within {$o->sla}.")
            ->action('View order', route('app.orders.show', $o))
            ->line('We\'ll notify you as soon as your tuned file is ready.');
    }

    public function toDatabase(object $notifiable): array
    {
        $o = $this->order;
        $tunerName = $o->assignedTuner?->name ?? 'A tuner';
        return [
            'message' => "Tuner {$tunerName} assigned to #{$o->reference}",
            'url'     => route('app.orders.show', $o),
            'icon'    => 'tuners',
        ];
    }
}
