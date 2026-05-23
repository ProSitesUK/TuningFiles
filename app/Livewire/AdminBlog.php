<?php

namespace App\Livewire;

use App\Models\Post;
use Illuminate\Support\Str;
use Livewire\Component;

class AdminBlog extends Component
{
    public string $mode = 'list'; // list | edit
    public ?int $editingId = null;

    public array $form = [
        'title' => '', 'slug' => '', 'excerpt' => '', 'body' => '',
        'cover_image' => '', 'seo_description' => '',
        'is_published' => false, 'published_at' => '',
    ];

    public ?string $flash = null;

    public function newPost(): void
    {
        $this->mode = 'edit';
        $this->editingId = null;
        $this->form = [
            'title' => '', 'slug' => '', 'excerpt' => '', 'body' => '',
            'cover_image' => '', 'seo_description' => '',
            'is_published' => false, 'published_at' => '',
        ];
    }

    public function edit(int $id): void
    {
        $p = Post::findOrFail($id);
        $this->mode = 'edit';
        $this->editingId = $id;
        $this->form = [
            'title'           => $p->title,
            'slug'            => $p->slug,
            'excerpt'         => $p->excerpt ?? '',
            'body'            => $p->body ?? '',
            'cover_image'     => $p->cover_image ?? '',
            'seo_description' => $p->seo_description ?? '',
            'is_published'    => (bool) $p->is_published,
            'published_at'    => $p->published_at?->format('Y-m-d\TH:i') ?? '',
        ];
    }

    public function updatedFormTitle(): void
    {
        // Auto-fill slug only if blank (don't clobber a hand-edited slug)
        if ($this->form['slug'] === '' && $this->editingId === null) {
            $this->form['slug'] = Str::slug($this->form['title']);
        }
    }

    public function save(): void
    {
        $this->validate([
            'form.title'           => 'required|string|max:191',
            'form.slug'            => 'required|string|max:191|regex:/^[a-z0-9-]+$/'.($this->editingId ? '|unique:posts,slug,'.$this->editingId : '|unique:posts,slug'),
            'form.excerpt'         => 'nullable|string|max:320',
            'form.body'            => 'nullable|string',
            'form.cover_image'     => 'nullable|url|max:255',
            'form.seo_description' => 'nullable|string|max:320',
            'form.is_published'    => 'boolean',
            'form.published_at'    => 'nullable|date',
        ]);

        $attrs = [
            'title'           => $this->form['title'],
            'slug'            => $this->form['slug'],
            'excerpt'         => $this->form['excerpt'] ?: null,
            'body'            => $this->form['body'] ?: null,
            'cover_image'     => $this->form['cover_image'] ?: null,
            'seo_description' => $this->form['seo_description'] ?: null,
            'is_published'    => (bool) $this->form['is_published'],
            'published_at'    => $this->form['published_at'] ?: ($this->form['is_published'] ? now() : null),
            'author_id'       => auth()->id(),
        ];

        if ($this->editingId) {
            Post::findOrFail($this->editingId)->update($attrs);
            $this->flash = 'Post updated.';
        } else {
            $post = Post::create($attrs);
            $this->editingId = $post->id;
            $this->flash = 'Post created.';
        }

        $this->mode = 'list';
    }

    public function togglePublish(int $id): void
    {
        $p = Post::findOrFail($id);
        $p->is_published = ! $p->is_published;
        if ($p->is_published && ! $p->published_at) {
            $p->published_at = now();
        }
        $p->save();
    }

    public function deletePost(int $id): void
    {
        Post::findOrFail($id)->delete();
        $this->flash = 'Post deleted.';
    }

    public function cancel(): void
    {
        $this->mode = 'list';
        $this->editingId = null;
    }

    public function getPreviewProperty(): string
    {
        return $this->form['body'] ? Str::markdown($this->form['body']) : '';
    }

    public function render()
    {
        $posts = Post::with('author:id,name')
            ->orderByDesc('updated_at')
            ->get();

        return view('livewire.admin-blog', [
            'posts' => $posts,
            'preview' => $this->preview,
        ]);
    }
}
