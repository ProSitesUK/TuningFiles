<?php

namespace App\Livewire;

use App\Models\Vehicle;
use App\Models\VehicleMake;
use App\Models\VehicleModel;
use Illuminate\Support\Str;
use Livewire\Component;

class VehicleAdmin extends Component
{
    public ?int $selectedMakeId  = null;
    public ?int $selectedModelId = null;

    // null = closed, 0 = new, int = editing existing
    public ?int $makeForm    = null;
    public ?int $modelForm   = null;
    public ?int $variantForm = null;

    public array $makeData = [
        'name' => '', 'logo_url' => '', 'image_url' => '', 'seo_description' => '', 'intro' => '', 'is_active' => true,
    ];
    public array $modelData = [
        'name' => '', 'body_type' => '', 'image_url' => '', 'seo_description' => '', 'intro' => '', 'is_active' => true,
    ];
    public array $variantData = [
        'generation' => '', 'year_start' => null, 'year_end' => null,
        'fuel' => 'petrol', 'displacement' => '', 'stock_hp' => null, 'is_active' => true,
    ];

    /* -------------------- Make actions -------------------- */
    public function selectMake(int $id): void
    {
        $this->selectedMakeId = $id;
        $this->selectedModelId = null;
        $this->closeForms();
    }

    public function newMake(): void
    {
        $this->makeForm = 0;
        $this->modelForm = $this->variantForm = null;
        $this->makeData = ['name' => '', 'logo_url' => '', 'image_url' => '', 'seo_description' => '', 'intro' => '', 'is_active' => true];
    }

    public function editMake(int $id): void
    {
        $m = VehicleMake::findOrFail($id);
        $this->makeForm = $id;
        $this->modelForm = $this->variantForm = null;
        $this->makeData = [
            'name'            => $m->name,
            'logo_url'        => $m->logo_url ?? '',
            'image_url'       => $m->image_url ?? '',
            'seo_description' => $m->seo_description ?? '',
            'intro'           => $m->intro ?? '',
            'is_active'       => (bool) $m->is_active,
        ];
    }

    public function saveMake(): void
    {
        $this->validate([
            'makeData.name'            => 'required|string|max:80',
            'makeData.seo_description' => 'nullable|string|max:320',
        ]);

        $attrs = [
            'name'            => $this->makeData['name'],
            'slug'            => Str::slug($this->makeData['name']),
            'logo_url'        => $this->makeData['logo_url'] ?: null,
            'image_url'       => $this->makeData['image_url'] ?: null,
            'seo_description' => $this->makeData['seo_description'] ?: null,
            'intro'           => $this->makeData['intro'] ?: null,
            'is_active'       => (bool) $this->makeData['is_active'],
        ];

        if ($this->makeForm === 0) {
            $m = VehicleMake::create($attrs);
            $this->selectedMakeId = $m->id;
        } else {
            VehicleMake::findOrFail($this->makeForm)->update($attrs);
        }

        $this->makeForm = null;
    }

    public function toggleMakeActive(int $id): void
    {
        $m = VehicleMake::findOrFail($id);
        $m->update(['is_active' => ! $m->is_active]);
    }

    /* -------------------- Model actions -------------------- */
    public function selectModel(int $id): void
    {
        $this->selectedModelId = $id;
        $this->closeForms();
    }

    public function newModel(): void
    {
        if (! $this->selectedMakeId) return;
        $this->modelForm = 0;
        $this->makeForm = $this->variantForm = null;
        $this->modelData = ['name' => '', 'body_type' => '', 'image_url' => '', 'seo_description' => '', 'intro' => '', 'is_active' => true];
    }

    public function editModel(int $id): void
    {
        $m = VehicleModel::findOrFail($id);
        $this->modelForm = $id;
        $this->makeForm = $this->variantForm = null;
        $this->modelData = [
            'name'            => $m->name,
            'body_type'       => $m->body_type ?? '',
            'image_url'       => $m->image_url ?? '',
            'seo_description' => $m->seo_description ?? '',
            'intro'           => $m->intro ?? '',
            'is_active'       => (bool) $m->is_active,
        ];
    }

    public function saveModel(): void
    {
        $this->validate([
            'modelData.name'            => 'required|string|max:80',
            'modelData.seo_description' => 'nullable|string|max:320',
        ]);

        $attrs = [
            'name'            => $this->modelData['name'],
            'slug'            => Str::slug($this->modelData['name']),
            'body_type'       => $this->modelData['body_type'] ?: null,
            'image_url'       => $this->modelData['image_url'] ?: null,
            'seo_description' => $this->modelData['seo_description'] ?: null,
            'intro'           => $this->modelData['intro'] ?: null,
            'is_active'       => (bool) $this->modelData['is_active'],
        ];

        if ($this->modelForm === 0) {
            $attrs['make_id'] = $this->selectedMakeId;
            $m = VehicleModel::create($attrs);
            $this->selectedModelId = $m->id;
        } else {
            VehicleModel::findOrFail($this->modelForm)->update($attrs);
        }

        $this->modelForm = null;
    }

    public function toggleModelActive(int $id): void
    {
        $m = VehicleModel::findOrFail($id);
        $m->update(['is_active' => ! $m->is_active]);
    }

    /* -------------------- Variant actions -------------------- */
    public function newVariant(): void
    {
        if (! $this->selectedModelId) return;
        $this->variantForm = 0;
        $this->makeForm = $this->modelForm = null;
        $this->variantData = [
            'generation' => '', 'year_start' => null, 'year_end' => null,
            'fuel' => 'petrol', 'displacement' => '', 'stock_hp' => null, 'is_active' => true,
        ];
    }

    public function editVariant(int $id): void
    {
        $v = Vehicle::findOrFail($id);
        $this->variantForm = $id;
        $this->makeForm = $this->modelForm = null;
        $this->variantData = [
            'generation'   => $v->generation ?? '',
            'year_start'   => $v->year_start,
            'year_end'     => $v->year_end,
            'fuel'         => $v->fuel ?? 'petrol',
            'displacement' => $v->displacement ?? '',
            'stock_hp'     => $v->stock_hp,
            'is_active'    => (bool) $v->is_active,
        ];
    }

    public function saveVariant(): void
    {
        $this->validate([
            'variantData.year_start'   => 'required|integer|min:1980|max:2100',
            'variantData.year_end'     => 'nullable|integer|min:1980|max:2100|gte:variantData.year_start',
            'variantData.fuel'         => 'required|in:petrol,diesel,hybrid,electric',
            'variantData.stock_hp'     => 'nullable|integer|min:30|max:2000',
        ]);

        $attrs = [
            'generation'   => $this->variantData['generation'] ?: null,
            'year_start'   => (int) $this->variantData['year_start'],
            'year_end'     => $this->variantData['year_end'] ? (int) $this->variantData['year_end'] : null,
            'fuel'         => $this->variantData['fuel'],
            'displacement' => $this->variantData['displacement'] ?: null,
            'stock_hp'     => $this->variantData['stock_hp'] ? (int) $this->variantData['stock_hp'] : null,
            'is_active'    => (bool) $this->variantData['is_active'],
        ];

        if ($this->variantForm === 0) {
            $attrs['model_id'] = $this->selectedModelId;
            Vehicle::create($attrs);
        } else {
            Vehicle::findOrFail($this->variantForm)->update($attrs);
        }

        $this->variantForm = null;
    }

    public function toggleVariantActive(int $id): void
    {
        $v = Vehicle::findOrFail($id);
        $v->update(['is_active' => ! $v->is_active]);
    }

    public function closeForms(): void
    {
        $this->makeForm = $this->modelForm = $this->variantForm = null;
    }

    public function render()
    {
        $makes = VehicleMake::withCount('models')->orderBy('name')->get();

        $models = $this->selectedMakeId
            ? VehicleModel::where('make_id', $this->selectedMakeId)
                ->withCount('variants')->orderBy('name')->get()
            : collect();

        $variants = $this->selectedModelId
            ? Vehicle::where('model_id', $this->selectedModelId)
                ->orderBy('year_start', 'desc')->get()
            : collect();

        $selMake  = $this->selectedMakeId  ? $makes->firstWhere('id', $this->selectedMakeId)  : null;
        $selModel = $this->selectedModelId ? $models->firstWhere('id', $this->selectedModelId) : null;

        return view('livewire.vehicle-admin', [
            'makes'    => $makes,
            'models'   => $models,
            'variants' => $variants,
            'selMake'  => $selMake,
            'selModel' => $selModel,
        ]);
    }
}
