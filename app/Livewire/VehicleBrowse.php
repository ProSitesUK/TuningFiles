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

    public function selectMake(string $slug): void
    {
        $this->makeSlug = $slug;
        $this->search = '';
    }

    public function clearMake(): void
    {
        $this->makeSlug = 'all';
        $this->search = '';
        $this->fuel = 'all';
        $this->body = 'all';
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->fuel = 'all';
        $this->body = 'all';
    }

    public function render()
    {
        $makes = VehicleMake::where('is_active', true)
            ->whereHas('models', fn ($q) => $q->where('is_active', true))
            ->withCount(['models' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('name')
            ->get();

        // Makes-first landing view: show grid of make tiles, no model query needed.
        if ($this->makeSlug === 'all' && $this->search === '' && $this->fuel === 'all' && $this->body === 'all') {
            return view('livewire.vehicle-browse', [
                'mode'      => 'makes',
                'makes'     => $makes,
                'models'    => collect(),
                'bodyTypes' => collect(),
                'selMake'   => null,
            ]);
        }

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

        $selMake = $this->makeSlug !== 'all'
            ? $makes->firstWhere('slug', $this->makeSlug)
            : null;

        return view('livewire.vehicle-browse', [
            'mode'      => 'models',
            'makes'     => $makes,
            'models'    => $models,
            'bodyTypes' => $bodyTypes,
            'selMake'   => $selMake,
        ]);
    }
}
