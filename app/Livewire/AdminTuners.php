<?php

namespace App\Livewire;

use App\Models\TunerProfile;
use Livewire\Component;

class AdminTuners extends Component
{
    public ?int $editing = null;

    public array $form = [
        'status'      => 'live',
        'capacity'    => 5,
        'specialties' => '',
    ];

    public ?string $flash = null;

    public function edit(int $id): void
    {
        $profile = TunerProfile::findOrFail($id);
        $this->editing = $id;
        $this->form = [
            'status'      => $profile->status,
            'capacity'    => $profile->capacity,
            'specialties' => is_array($profile->specialties) ? implode(', ', $profile->specialties) : '',
        ];
    }

    public function cancel(): void
    {
        $this->editing = null;
        $this->form = ['status' => 'live', 'capacity' => 5, 'specialties' => ''];
    }

    public function save(): void
    {
        $this->validate([
            'form.status'   => 'required|in:live,busy,away,off',
            'form.capacity' => 'required|integer|min:0|max:50',
        ]);

        $profile = TunerProfile::findOrFail($this->editing);

        $specialties = array_values(array_filter(
            array_map('trim', explode(',', $this->form['specialties'])),
            fn ($s) => $s !== '',
        ));

        $profile->update([
            'status'      => $this->form['status'],
            'capacity'    => (int) $this->form['capacity'],
            'specialties' => $specialties,
        ]);

        $this->cancel();
        $this->flash = 'Tuner profile updated.';
    }

    public function render()
    {
        $tuners = TunerProfile::with('user:id,name,email')->orderBy('status')->get();

        return view('livewire.admin-tuners', [
            'tuners' => $tuners,
        ]);
    }
}
