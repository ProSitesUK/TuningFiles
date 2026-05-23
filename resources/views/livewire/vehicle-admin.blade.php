<div class="page page-flush">
    <div class="three-pane">
        {{-- ===================== MAKES PANE ===================== --}}
        <div class="pane pane-l">
            <div class="pane-head">
                <h2 class="pane-title">Makes <span class="t-mute mono small">{{ $makes->count() }}</span></h2>
                <button type="button" wire:click="newMake" class="primary-btn primary-btn-sm">+ New</button>
            </div>

            @if ($makeForm !== null)
                <div class="va-form">
                    <div class="va-form-title">{{ $makeForm === 0 ? 'New make' : 'Edit make' }}</div>
                    <label class="va-field">
                        <span>Name</span>
                        <input type="text" wire:model.live="makeData.name" placeholder="e.g. Volkswagen" />
                        @error('makeData.name') <em class="va-err">{{ $message }}</em> @enderror
                    </label>
                    <label class="va-field">
                        <span>Logo URL</span>
                        <input type="text" wire:model.defer="makeData.logo_url" placeholder="https://logo.clearbit.com/…" />
                    </label>
                    <label class="va-field">
                        <span>Hero image URL</span>
                        <input type="text" wire:model.defer="makeData.image_url" placeholder="https://images.unsplash.com/…" />
                    </label>
                    <label class="va-field">
                        <span>SEO description <em class="t-mute small">(50–160 chars ideal, 320 max)</em></span>
                        <textarea wire:model.defer="makeData.seo_description" rows="2" placeholder="Used for meta description and search snippets"></textarea>
                        @error('makeData.seo_description') <em class="va-err">{{ $message }}</em> @enderror
                    </label>
                    <label class="va-field">
                        <span>Intro copy <em class="t-mute small">(markdown — appears above the model grid on /vehicles/{slug})</em></span>
                        <textarea wire:model.defer="makeData.intro" rows="5" placeholder="## About {{ '{Make}' }} tuning&#10;&#10;Lorem ipsum…"></textarea>
                    </label>
                    <label class="va-check">
                        <input type="checkbox" wire:model.defer="makeData.is_active" />
                        <span>Active</span>
                    </label>
                    <div class="va-form-actions">
                        <button type="button" wire:click="closeForms" class="ghost-btn ghost-btn-sm">Cancel</button>
                        <button type="button" wire:click="saveMake" class="primary-btn primary-btn-sm">Save</button>
                    </div>
                </div>
            @endif

            <div class="cust-list">
                @foreach ($makes as $m)
                    <div class="va-row {{ $m->id === $selectedMakeId ? 'cust-row-active' : '' }} {{ ! $m->is_active ? 'va-row-off' : '' }}">
                        <button type="button" wire:click="selectMake({{ $m->id }})" class="va-row-btn">
                            @if ($m->logo_url)
                                <img src="{{ $m->logo_url }}" alt="{{ $m->name }}" class="va-logo" loading="lazy" />
                            @else
                                <span class="va-logo va-logo-fallback mono">{{ strtoupper(substr($m->name, 0, 2)) }}</span>
                            @endif
                            <span class="va-row-text">
                                <span class="va-row-name">{{ $m->name }}</span>
                                <span class="va-row-meta mono small">{{ $m->models_count }} models</span>
                            </span>
                        </button>
                        <div class="va-row-actions">
                            <button type="button" wire:click="editMake({{ $m->id }})" class="icon-btn" title="Edit">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5 a2.121 2.121 0 1 1 3 3 L7 19 l-4 1 1-4z"/></svg>
                            </button>
                            <button type="button" wire:click="toggleMakeActive({{ $m->id }})" class="icon-btn" title="{{ $m->is_active ? 'Archive' : 'Restore' }}">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    @if ($m->is_active)
                                        <path d="M21 8 v13 a2 2 0 0 1 -2 2 H5 a2 2 0 0 1 -2 -2 V8"/><rect x="1" y="3" width="22" height="5"/><path d="M10 12 h4"/>
                                    @else
                                        <path d="M3 12 h18 M3 12 l4 -4 M3 12 l4 4"/>
                                    @endif
                                </svg>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ===================== MODELS PANE ===================== --}}
        <div class="pane pane-m">
            <div class="pane-head">
                <h2 class="pane-title">
                    Models
                    @if ($selMake)
                        <span class="t-mute small">· {{ $selMake->name }}</span>
                    @endif
                    <span class="t-mute mono small">{{ $models->count() }}</span>
                </h2>
                @if ($selectedMakeId)
                    <button type="button" wire:click="newModel" class="primary-btn primary-btn-sm">+ New</button>
                @endif
            </div>

            @if (! $selectedMakeId)
                <div class="pane-empty">
                    <div class="empty-title">Pick a make</div>
                    <div class="t-mute small">Choose from the left to manage its models.</div>
                </div>
            @else
                @if ($modelForm !== null)
                    <div class="va-form">
                        <div class="va-form-title">{{ $modelForm === 0 ? 'New model' : 'Edit model' }}</div>
                        <label class="va-field">
                            <span>Name</span>
                            <input type="text" wire:model.live="modelData.name" placeholder="e.g. Golf R" />
                            @error('modelData.name') <em class="va-err">{{ $message }}</em> @enderror
                        </label>
                        <label class="va-field">
                            <span>Body type</span>
                            <input type="text" wire:model.defer="modelData.body_type" placeholder="hatch / saloon / SUV / coupe…" />
                        </label>
                        <label class="va-field">
                            <span>Image URL</span>
                            <input type="text" wire:model.defer="modelData.image_url" placeholder="https://…" />
                        </label>
                        <label class="va-field">
                            <span>SEO description <em class="t-mute small">(50–160 chars ideal)</em></span>
                            <textarea wire:model.defer="modelData.seo_description" rows="2"></textarea>
                            @error('modelData.seo_description') <em class="va-err">{{ $message }}</em> @enderror
                        </label>
                        <label class="va-field">
                            <span>Intro copy <em class="t-mute small">(markdown — appears above the variants table)</em></span>
                            <textarea wire:model.defer="modelData.intro" rows="5"></textarea>
                        </label>
                        <label class="va-check">
                            <input type="checkbox" wire:model.defer="modelData.is_active" />
                            <span>Active</span>
                        </label>
                        <div class="va-form-actions">
                            <button type="button" wire:click="closeForms" class="ghost-btn ghost-btn-sm">Cancel</button>
                            <button type="button" wire:click="saveModel" class="primary-btn primary-btn-sm">Save</button>
                        </div>
                    </div>
                @endif

                <div class="cust-list">
                    @foreach ($models as $m)
                        <div class="va-row {{ $m->id === $selectedModelId ? 'cust-row-active' : '' }} {{ ! $m->is_active ? 'va-row-off' : '' }}">
                            <button type="button" wire:click="selectModel({{ $m->id }})" class="va-row-btn">
                                <span class="va-row-text">
                                    <span class="va-row-name">{{ $m->name }}</span>
                                    <span class="va-row-meta mono small">
                                        {{ $m->variants_count }} variant{{ $m->variants_count === 1 ? '' : 's' }}
                                        @if ($m->body_type) · {{ $m->body_type }} @endif
                                    </span>
                                </span>
                            </button>
                            <div class="va-row-actions">
                                <button type="button" wire:click="editModel({{ $m->id }})" class="icon-btn" title="Edit">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5 a2.121 2.121 0 1 1 3 3 L7 19 l-4 1 1-4z"/></svg>
                                </button>
                                <button type="button" wire:click="toggleModelActive({{ $m->id }})" class="icon-btn" title="{{ $m->is_active ? 'Archive' : 'Restore' }}">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        @if ($m->is_active)
                                            <path d="M21 8 v13 a2 2 0 0 1 -2 2 H5 a2 2 0 0 1 -2 -2 V8"/><rect x="1" y="3" width="22" height="5"/><path d="M10 12 h4"/>
                                        @else
                                            <path d="M3 12 h18 M3 12 l4 -4 M3 12 l4 4"/>
                                        @endif
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ===================== VARIANTS PANE ===================== --}}
        <div class="pane pane-r">
            <div class="pane-head">
                <h2 class="pane-title">
                    Variants
                    @if ($selModel)
                        <span class="t-mute small">· {{ $selModel->name }}</span>
                    @endif
                    <span class="t-mute mono small">{{ $variants->count() }}</span>
                </h2>
                @if ($selectedModelId)
                    <button type="button" wire:click="newVariant" class="primary-btn primary-btn-sm">+ New</button>
                @endif
            </div>

            @if (! $selectedModelId)
                <div class="pane-empty">
                    <div class="empty-title">Pick a model</div>
                    <div class="t-mute small">Variants are gen + year + drivetrain rows under a model.</div>
                </div>
            @else
                @if ($variantForm !== null)
                    <div class="va-form">
                        <div class="va-form-title">{{ $variantForm === 0 ? 'New variant' : 'Edit variant' }}</div>
                        <div class="va-grid-2">
                            <label class="va-field">
                                <span>Generation / chassis</span>
                                <input type="text" wire:model.defer="variantData.generation" placeholder="e.g. MK7, B9, F30" />
                            </label>
                            <label class="va-field">
                                <span>Body / fuel</span>
                                <select wire:model.defer="variantData.fuel">
                                    <option value="petrol">petrol</option>
                                    <option value="diesel">diesel</option>
                                    <option value="hybrid">hybrid</option>
                                    <option value="electric">electric</option>
                                </select>
                            </label>
                        </div>
                        <div class="va-grid-2">
                            <label class="va-field">
                                <span>Year start</span>
                                <input type="number" wire:model.defer="variantData.year_start" min="1980" max="2100" placeholder="e.g. 2014" />
                                @error('variantData.year_start') <em class="va-err">{{ $message }}</em> @enderror
                            </label>
                            <label class="va-field">
                                <span>Year end <em class="t-mute small">(blank if current)</em></span>
                                <input type="number" wire:model.defer="variantData.year_end" min="1980" max="2100" />
                                @error('variantData.year_end') <em class="va-err">{{ $message }}</em> @enderror
                            </label>
                        </div>
                        <div class="va-grid-2">
                            <label class="va-field">
                                <span>Displacement / engine</span>
                                <input type="text" wire:model.defer="variantData.displacement" placeholder="e.g. 2.0 TSI" />
                            </label>
                            <label class="va-field">
                                <span>Stock HP</span>
                                <input type="number" wire:model.defer="variantData.stock_hp" min="30" max="2000" />
                                @error('variantData.stock_hp') <em class="va-err">{{ $message }}</em> @enderror
                            </label>
                        </div>
                        <label class="va-check">
                            <input type="checkbox" wire:model.defer="variantData.is_active" />
                            <span>Active</span>
                        </label>
                        <div class="va-form-actions">
                            <button type="button" wire:click="closeForms" class="ghost-btn ghost-btn-sm">Cancel</button>
                            <button type="button" wire:click="saveVariant" class="primary-btn primary-btn-sm">Save</button>
                        </div>
                    </div>
                @endif

                <div class="cust-list">
                    @forelse ($variants as $v)
                        <div class="va-row {{ ! $v->is_active ? 'va-row-off' : '' }}">
                            <div class="va-row-btn" style="cursor:default">
                                <span class="va-row-text">
                                    <span class="va-row-name">
                                        {{ $v->generation ?: '—' }}
                                        <span class="badge badge-neutral">{{ $v->yearRange() }}</span>
                                    </span>
                                    <span class="va-row-meta mono small">
                                        {{ $v->fuel ?? '—' }}{{ $v->displacement ? ' · '.$v->displacement : '' }}{{ $v->stock_hp ? ' · '.$v->stock_hp.' hp' : '' }}
                                    </span>
                                </span>
                            </div>
                            <div class="va-row-actions">
                                <button type="button" wire:click="editVariant({{ $v->id }})" class="icon-btn" title="Edit">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5 a2.121 2.121 0 1 1 3 3 L7 19 l-4 1 1-4z"/></svg>
                                </button>
                                <button type="button" wire:click="toggleVariantActive({{ $v->id }})" class="icon-btn" title="{{ $v->is_active ? 'Archive' : 'Restore' }}">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        @if ($v->is_active)
                                            <path d="M21 8 v13 a2 2 0 0 1 -2 2 H5 a2 2 0 0 1 -2 -2 V8"/><rect x="1" y="3" width="22" height="5"/><path d="M10 12 h4"/>
                                        @else
                                            <path d="M3 12 h18 M3 12 l4 -4 M3 12 l4 4"/>
                                        @endif
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="pane-empty">
                            <div class="empty-title">No variants yet</div>
                            <div class="t-mute small">Click "+ New" to add the first one.</div>
                        </div>
                    @endforelse
                </div>
            @endif
        </div>
    </div>
</div>
