<?php

namespace App\Http\Controllers;

use App\Models\VehicleMake;
use App\Models\VehicleModel;
use Illuminate\Http\Request;

class VehiclePagesController extends Controller
{
    public function showMake(VehicleMake $make)
    {
        abort_unless($make->is_active, 404);

        $models = $make->models()
            ->where('is_active', true)
            ->whereHas('variants', fn ($q) => $q->where('is_active', true))
            ->withCount(['variants' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('name')
            ->get();

        return view('marketing.make', [
            'make'   => $make,
            'models' => $models,
        ]);
    }

    public function showModel(VehicleMake $make, VehicleModel $model)
    {
        abort_unless($make->is_active && $model->is_active && $model->make_id === $make->id, 404);

        $variants = $model->variants()
            ->where('is_active', true)
            ->with('ecus')
            ->orderBy('year_start', 'desc')
            ->get();

        // 3 closest related models within the same make (prefer matching body_type)
        $related = $make->models()
            ->where('is_active', true)
            ->where('id', '!=', $model->id)
            ->whereHas('variants', fn ($q) => $q->where('is_active', true))
            ->when($model->body_type, fn ($q) => $q->orderByRaw('CASE WHEN body_type = ? THEN 0 ELSE 1 END', [$model->body_type]))
            ->orderBy('name')
            ->limit(3)
            ->get();

        $tunes = \App\Models\Tune::where('is_active', true)->orderBy('credit_cost')->get();

        return view('marketing.model', [
            'make'     => $make,
            'model'    => $model,
            'variants' => $variants,
            'related'  => $related,
            'tunes'    => $tunes,
        ]);
    }
}
