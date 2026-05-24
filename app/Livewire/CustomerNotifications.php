<?php

namespace App\Livewire;

use Livewire\Component;

class CustomerNotifications extends Component
{
    public function markAllRead(): void
    {
        auth()->user()->unreadNotifications->markAsRead();
    }

    public function render()
    {
        $notifications = auth()->user()->unreadNotifications->take(10);

        return view('livewire.customer-notifications', [
            'items' => $notifications,
            'count' => auth()->user()->unreadNotifications->count(),
        ]);
    }
}
