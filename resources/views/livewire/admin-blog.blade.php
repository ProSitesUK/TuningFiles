<div class="page">
    <div class="page-head">
        <div>
            <h1 class="page-title">Blog</h1>
            <p class="page-sub">
                @if ($mode === 'list')
                    {{ $posts->count() }} post{{ $posts->count() === 1 ? '' : 's' }} · {{ $posts->where('is_published', true)->count() }} published
                @else
                    {{ $editingId ? 'Editing post' : 'New post' }}
                @endif
            </p>
        </div>
        <div class="page-actions">
            @if ($mode === 'list')
                <button type="button" wire:click="newPost" class="primary-btn primary-btn-sm">+ New post</button>
            @else
                <button type="button" wire:click="cancel" class="ghost-btn ghost-btn-sm">← Back to list</button>
            @endif
        </div>
    </div>

    @if ($flash)
        <div class="card card-pad" style="border-color: var(--success); background: var(--success-soft); margin-bottom: 16px">
            <span style="color: var(--success); font-weight: 500">✓ {{ $flash }}</span>
        </div>
    @endif

    @if ($mode === 'list')
        @if ($posts->isEmpty())
            <div class="card card-pad" style="text-align:center; padding: 40px">
                <div class="empty-title">No posts yet</div>
                <div class="t-mute small" style="margin-top:6px">Click "+ New post" to write the first one.</div>
            </div>
        @else
            <div style="display: grid; gap: 8px; max-width: 920px">
                @foreach ($posts as $post)
                    <div class="card card-pad" style="display:flex; align-items:center; gap:14px">
                        <div style="flex:1; min-width:0">
                            <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap">
                                <strong style="font-size:14.5px">{{ $post->title }}</strong>
                                @if ($post->is_published)
                                    <span class="badge badge-success">live</span>
                                @else
                                    <span class="badge badge-neutral">draft</span>
                                @endif
                            </div>
                            <div class="t-mute small mono" style="margin-top:3px">
                                /blog/{{ $post->slug }}
                                @if ($post->published_at) · {{ $post->published_at->format('j M Y') }} @endif
                                @if ($post->author) · {{ $post->author->name }} @endif
                            </div>
                        </div>
                        <div style="display:inline-flex; gap:4px">
                            <button type="button" wire:click="togglePublish({{ $post->id }})" class="ghost-btn ghost-btn-sm">{{ $post->is_published ? 'Unpublish' : 'Publish' }}</button>
                            <button type="button" wire:click="edit({{ $post->id }})" class="ghost-btn ghost-btn-sm">Edit</button>
                            <button type="button" wire:click="deletePost({{ $post->id }})" wire:confirm="Delete this post for good?" class="ghost-btn ghost-btn-sm" style="color:var(--danger)">Delete</button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @else
        <div class="ab-edit-grid">
            {{-- Form pane --}}
            <div>
                <div class="card card-pad">
                    <label class="va-field">
                        <span>Title</span>
                        <input type="text" wire:model.live.debounce.400ms="form.title" />
                        @error('form.title') <em class="va-err">{{ $message }}</em> @enderror
                    </label>
                    <label class="va-field">
                        <span>Slug <em class="t-mute small">(URL: /blog/{{ $form['slug'] ?: '...' }})</em></span>
                        <input type="text" wire:model.defer="form.slug" />
                        @error('form.slug') <em class="va-err">{{ $message }}</em> @enderror
                    </label>
                    <label class="va-field">
                        <span>Excerpt <em class="t-mute small">(shown on the listing card)</em></span>
                        <textarea wire:model.defer="form.excerpt" rows="2" maxlength="320"></textarea>
                        @error('form.excerpt') <em class="va-err">{{ $message }}</em> @enderror
                    </label>
                    <label class="va-field">
                        <span>Cover image URL</span>
                        <input type="url" wire:model.defer="form.cover_image" placeholder="https://images.unsplash.com/…" />
                        @error('form.cover_image') <em class="va-err">{{ $message }}</em> @enderror
                    </label>
                    <label class="va-field">
                        <span>Body <em class="t-mute small">(markdown — # for h2, ** for bold, etc)</em></span>
                        <textarea wire:model.live.debounce.500ms="form.body" rows="18" class="ab-body-input"></textarea>
                    </label>
                    <label class="va-field">
                        <span>SEO description <em class="t-mute small">(50–160 chars ideal, falls back to excerpt)</em></span>
                        <textarea wire:model.defer="form.seo_description" rows="2" maxlength="320"></textarea>
                        @error('form.seo_description') <em class="va-err">{{ $message }}</em> @enderror
                    </label>
                    <div class="va-grid-2">
                        <label class="va-field">
                            <span>Publish date <em class="t-mute small">(optional — blank = now)</em></span>
                            <input type="datetime-local" wire:model.defer="form.published_at" />
                        </label>
                        <label class="va-check" style="align-self:end; padding-bottom:8px">
                            <input type="checkbox" wire:model.defer="form.is_published" />
                            <span>Publish immediately</span>
                        </label>
                    </div>
                    <div class="va-form-actions">
                        <button type="button" wire:click="cancel" class="ghost-btn ghost-btn-sm">Cancel</button>
                        <button type="button" wire:click="save" class="primary-btn primary-btn-sm" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="save">Save</span>
                            <span wire:loading wire:target="save">Saving…</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Preview pane --}}
            <div>
                <div class="card card-pad" style="position:sticky; top:18px">
                    <div class="va-form-title">Live preview</div>
                    <h1 style="font-size:24px; font-weight:600; margin:6px 0 14px; letter-spacing:-0.015em">{{ $form['title'] ?: 'Post title' }}</h1>
                    @if ($form['cover_image'])
                        <div style="aspect-ratio:16/9; margin-bottom:14px; border-radius:8px; overflow:hidden; border:1px solid var(--border)">
                            <img src="{{ $form['cover_image'] }}" style="width:100%; height:100%; object-fit:cover" />
                        </div>
                    @endif
                    <div class="mk-prose">
                        @if ($preview)
                            {!! $preview !!}
                        @else
                            <p class="t-mute small"><em>Start typing in the body field to see a live preview here.</em></p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
