<?php

namespace App\Livewire;

use App\Models\SeoMeta;
use Livewire\Component;

class AdminSeo extends Component
{
    public ?int $editing = null;
    public string $editingType = '';
    public string $editingKey  = '';

    public array $form = [
        'title' => '', 'description' => '', 'og_image' => '',
        'canonical' => '', 'robots' => '',
    ];

    public ?string $flash = null;

    public string $tab = 'pages';

    /**
     * SEO-able subjects, grouped by tab so the page stays scannable
     * once there are hundreds of make/model entries.
     */
    public function subjects(): array
    {
        if ($this->tab === 'makes') {
            return \App\Models\VehicleMake::where('is_active', true)->orderBy('name')->get()
                ->map(fn ($m) => [
                    'type'  => 'make',
                    'key'   => (string) $m->id,
                    'label' => $m->name,
                    'path'  => '/vehicles/'.$m->slug,
                    'hint'  => 'Make landing page · auto-title: "'.$m->name.' Tuning Files & ECU Remaps"',
                ])->all();
        }
        if ($this->tab === 'models') {
            return \App\Models\VehicleModel::where('is_active', true)
                ->with('make')
                ->orderBy('name')
                ->get()
                ->map(fn ($m) => [
                    'type'  => 'model',
                    'key'   => (string) $m->id,
                    'label' => $m->make->name.' '.$m->name,
                    'path'  => '/vehicles/'.$m->make->slug.'/'.$m->slug,
                    'hint'  => 'Model landing page',
                ])->all();
        }
        if ($this->tab === 'posts') {
            return \App\Models\Post::orderByDesc('updated_at')->get()
                ->map(fn ($p) => [
                    'type'  => 'post',
                    'key'   => (string) $p->id,
                    'label' => $p->title.($p->is_published ? '' : ' (draft)'),
                    'path'  => '/blog/'.$p->slug,
                    'hint'  => 'Blog post · auto-uses excerpt for description if no override',
                ])->all();
        }

        return [
            ['type' => 'route', 'key' => 'home',     'label' => 'Homepage',        'path' => '/',         'hint' => 'The marketing landing page (/)'],
            ['type' => 'route', 'key' => 'vehicles', 'label' => 'Vehicles browse', 'path' => '/vehicles', 'hint' => 'Make-first browse at /vehicles'],
            ['type' => 'route', 'key' => 'blog.index', 'label' => 'Blog index',    'path' => '/blog',     'hint' => 'Blog listing'],
        ];
    }

    public function edit(string $type, string $key): void
    {
        $row = SeoMeta::forSubject($type, $key);
        $this->editing = $row?->id ?? 0;
        $this->editingType = $type;
        $this->editingKey  = $key;
        $this->form = [
            'title'       => $row->title ?? '',
            'description' => $row->description ?? '',
            'og_image'    => $row->og_image ?? '',
            'canonical'   => $row->canonical ?? '',
            'robots'      => $row->robots ?? '',
        ];
    }

    public function save(): void
    {
        $this->validate([
            'form.title'       => 'nullable|string|max:191',
            'form.description' => 'nullable|string|max:320',
            'form.og_image'    => 'nullable|url|max:255',
            'form.canonical'   => 'nullable|url|max:255',
            'form.robots'      => 'nullable|string|max:64',
        ]);

        // Treat an "all blank" save as a clear: delete the row.
        $allEmpty = collect($this->form)->every(fn ($v) => trim((string) $v) === '');

        if ($allEmpty) {
            SeoMeta::where('subject_type', $this->editingType)
                ->where('subject_key', $this->editingKey)
                ->delete();
        } else {
            SeoMeta::updateOrCreate(
                ['subject_type' => $this->editingType, 'subject_key' => $this->editingKey],
                array_map(fn ($v) => trim((string) $v) === '' ? null : $v, $this->form),
            );
        }

        $this->cancel();
        $this->flash = 'SEO meta saved.';
    }

    public function cancel(): void
    {
        $this->editing = null;
        $this->editingType = '';
        $this->editingKey = '';
        $this->form = ['title' => '', 'description' => '', 'og_image' => '', 'canonical' => '', 'robots' => ''];
    }

    public function render()
    {
        $overrides = SeoMeta::all()->keyBy(fn ($r) => $r->subject_type.':'.$r->subject_key);

        return view('livewire.admin-seo', [
            'subjects'  => $this->subjects(),
            'overrides' => $overrides,
        ]);
    }
}
