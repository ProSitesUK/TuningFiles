<?php

namespace App\Livewire;

use App\Models\CreditPack;
use Livewire\Component;

class ResellerPricing extends Component
{
    public string $mode = 'list'; // list | form
    public ?int $editingId = null;

    public array $form = [
        'name'          => '',
        'credits'       => '',
        'price_pennies' => '',
        'is_active'     => true,
    ];

    public ?string $flash = null;

    /* ---- Credit Pack CRUD ---- */
    public function newPack(): void
    {
        $this->mode = 'form';
        $this->editingId = null;
        $this->form = [
            'name' => '', 'credits' => '', 'price_pennies' => '', 'is_active' => true,
        ];
    }

    public function editPack(int $id): void
    {
        $pack = CreditPack::where('tenant_id', auth()->id())->findOrFail($id);
        $this->mode = 'form';
        $this->editingId = $id;
        $this->form = [
            'name'          => $pack->name,
            'credits'       => (string) $pack->credits,
            'price_pennies' => (string) $pack->price_pennies,
            'is_active'     => (bool) $pack->is_active,
        ];
    }

    public function savePack(): void
    {
        $this->validate([
            'form.name'          => 'required|string|max:80',
            'form.credits'       => 'required|integer|min:1',
            'form.price_pennies' => 'required|integer|min:0',
            'form.is_active'     => 'boolean',
        ]);

        $attrs = [
            'name'          => $this->form['name'],
            'credits'       => (int) $this->form['credits'],
            'price_pennies' => (int) $this->form['price_pennies'],
            'is_active'     => (bool) $this->form['is_active'],
            'tenant_id'     => auth()->id(),
        ];

        if ($this->editingId) {
            CreditPack::where('tenant_id', auth()->id())->findOrFail($this->editingId)->update($attrs);
            $this->flash = 'Credit pack updated.';
        } else {
            CreditPack::create($attrs);
            $this->flash = 'Credit pack created.';
        }

        $this->mode = 'list';
        $this->editingId = null;
    }

    public function cancelPack(): void
    {
        $this->mode = 'list';
        $this->editingId = null;
    }

    public function toggleActive(int $id): void
    {
        $pack = CreditPack::where('tenant_id', auth()->id())->findOrFail($id);
        $pack->update(['is_active' => ! $pack->is_active]);
        $this->flash = $pack->is_active ? 'Pack activated.' : 'Pack archived.';
    }

    public function render()
    {
        $packs = CreditPack::where('tenant_id', auth()->id())
            ->orderByDesc('is_active')
            ->orderBy('credits')
            ->get();

        return view('livewire.reseller-pricing', [
            'packs' => $packs,
        ]);
    }
}
