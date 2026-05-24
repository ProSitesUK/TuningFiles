<?php

namespace App\Livewire;

use App\Models\DynoResult;
use Livewire\Component;

class AdminDynoResults extends Component
{
    public string $filter = 'pending';

    public function approve(int $id): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        DynoResult::findOrFail($id)->update(['is_approved' => true, 'is_public' => true]);
    }

    public function reject(int $id): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        DynoResult::findOrFail($id)->update(['is_approved' => false, 'is_public' => false]);
    }

    public function render()
    {
        $q = DynoResult::with('user:id,name', 'order:id,reference');

        if ($this->filter === 'pending') {
            $q->where('is_approved', false)->where('is_public', true);
        } elseif ($this->filter === 'approved') {
            $q->where('is_approved', true);
        }

        $results = $q->orderByDesc('created_at')->paginate(20);

        return view('livewire.admin-dyno-results', [
            'results' => $results,
        ]);
    }
}
