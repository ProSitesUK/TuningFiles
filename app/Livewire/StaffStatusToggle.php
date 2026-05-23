<?php

namespace App\Livewire;

use Livewire\Component;

class StaffStatusToggle extends Component
{
    public string $status = 'off';

    public function mount(): void
    {
        $this->status = auth()->user()->status ?? 'off';
    }

    public function setStatus(string $value): void
    {
        $allowed = ['online', 'away', 'busy', 'holiday', 'off'];
        if (! in_array($value, $allowed)) return;

        auth()->user()->update(['status' => $value]);
        $this->status = $value;
    }

    public function render()
    {
        return view('livewire.staff-status-toggle');
    }
}
