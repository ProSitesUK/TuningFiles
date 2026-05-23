<?php

namespace App\Livewire;

use App\Models\VehicleMake;
use App\Models\VehicleModel;
use Livewire\Component;

class VehicleBrowse extends Component
{
    public string $search   = '';
    public string $makeSlug = 'all';
    public string $fuel     = 'all';
    public string $body     = 'all';

    public function resetFilters(): void
    {
        $this->search = '';
        $this->makeSlug = 'all';
        $this->fuel = 'all';
        $this->body = 'all';
    }

    public function render()
    {
        $makes = VehicleMake::where('is_active', true)
            ->whereHas('models', fn ($q) => $q->where('is_active', true))
            ->orderBy('name')
            ->get();

        $q = VehicleModel::query()
            ->where('is_active', true)
            ->whereHas('make', fn ($qq) => $qq->where('is_active', true))
            ->whereHas('variants', fn ($qq) => $qq->where('is_active', true))
            ->with('make')
            ->withCount(['variants' => fn ($qq) => $qq->where('is_active', true)]);

        if ($this->search !== '') {
            $needle = trim($this->search);
            $q->where(function ($qq) use ($needle) {
                $qq->where('name', 'like', '%'.$needle.'%')
                   ->orWhereHas('make', fn ($qqq) => $qqq->where('name', 'like', '%'.$needle.'%'));
            });
        }

        if ($this->makeSlug !== 'all') {
            $q->whereHas('make', fn ($qq) => $qq->where('slug', $this->makeSlug));
        }

        if ($this->fuel !== 'all') {
            $fuel = $this->fuel;
            $q->whereHas('variants', fn ($qq) => $qq->where('fuel', $fuel)->where('is_active', true));
        }

        if ($this->body !== 'all') {
            $q->where('body_type', $this->body);
        }

        $models = $q->orderBy('name')->get();

        // body type options derived from data so we don't hardcode wrong values
        $bodyTypes = VehicleModel::whereNotNull('body_type')
            ->where('is_active', true)
            ->distinct()
            ->orderBy('body_type')
            ->pluck('body_type');

        return view('livewire.vehicle-browse', [
            'makes'     => $makes,
            'models'    => $models,
            'bodyTypes' => $bodyTypes,
        ]);
    }
}
