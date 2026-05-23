<div>
    <div class="page-head" style="margin-bottom:24px">
        <div>
            <h1 class="page-title">New tune</h1>
            <p class="page-sub">Step {{ $step }} of 4 — vehicle → ECU → tune options → upload</p>
        </div>
    </div>

    <div class="auth-steps" style="margin-bottom:24px">
        @foreach (range(1, 4) as $s)
            <span class="auth-step {{ $step >= $s ? 'auth-step-on' : '' }}"></span>
        @endforeach
    </div>

    <div class="card card-pad" style="max-width:760px">
        @if ($step === 1)
            <div class="metric-label" style="margin-bottom:10px">Pick your vehicle</div>

            <div class="va-grid-2" style="margin-bottom:14px">
                <label class="va-field">
                    <span>Make</span>
                    <select wire:model.live="makeId">
                        <option value="">Select make…</option>
                        @foreach ($makes as $m)
                            <option value="{{ $m->id }}">{{ $m->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="va-field">
                    <span>Model</span>
                    <select wire:model.live="modelId" {{ $makeId ? '' : 'disabled' }}>
                        <option value="">{{ $makeId ? 'Select model…' : 'Pick a make first' }}</option>
                        @foreach ($models as $m)
                            <option value="{{ $m->id }}">{{ $m->name }}</option>
                        @endforeach
                    </select>
                </label>
            </div>

            @if ($modelId)
                <div class="metric-label" style="margin-bottom:8px">Pick the variant</div>
                <div class="auth-plans" style="max-height:360px; overflow-y:auto">
                    @forelse ($variants as $v)
                        <button type="button" wire:click="$set('vehicleId', {{ $v->id }})"
                                class="auth-plan {{ $vehicleId === $v->id ? 'auth-plan-on' : '' }}">
                            <div class="auth-plan-head">
                                <span>{{ $v->generation ?: $v->displayName() }}</span>
                                <span class="auth-plan-price mono">{{ $v->yearRange() }}</span>
                            </div>
                            <div class="auth-plan-sub small">{{ $v->fuel }} · {{ $v->displacement }} · stock {{ $v->stock_hp }} hp</div>
                        </button>
                    @empty
                        <div class="t-mute small">No active variants for this model yet.</div>
                    @endforelse
                </div>
            @endif

            @error('vehicleId') <div class="auth-hint" style="color:var(--danger); margin-top:8px">{{ $message }}</div> @enderror
        @endif

        @if ($step === 2)
            <div class="metric-label" style="margin-bottom:10px">Pick your ECU</div>
            <div class="auth-plans">
                @forelse ($ecus as $ecu)
                    <button type="button" wire:click="$set('ecuId', {{ $ecu->id }})"
                            class="auth-plan {{ $ecuId === $ecu->id ? 'auth-plan-on' : '' }}">
                        <div class="auth-plan-head">
                            <span>{{ $ecu->identifier }}</span>
                            <span class="auth-plan-price mono">{{ $ecu->vendor }}</span>
                        </div>
                        <div class="auth-plan-sub small">family {{ $ecu->family }} · variant {{ $ecu->variant }}</div>
                    </button>
                @empty
                    <div class="t-mute small">No ECUs paired with this vehicle. Pick one manually.</div>
                @endforelse
            </div>
            @error('ecuId') <div class="auth-hint" style="color:var(--danger); margin-top:8px">{{ $message }}</div> @enderror
        @endif

        @if ($step === 3)
            <div class="metric-label" style="margin-bottom:10px">Pick tune options</div>
            <div class="auth-plans">
                @foreach ($tunes as $t)
                    <button type="button" wire:click="toggleTune('{{ $t->slug }}')"
                            class="auth-plan {{ in_array($t->slug, $tuneSlugs) ? 'auth-plan-on' : '' }}">
                        <div class="auth-plan-head">
                            <span>{{ $t->label }}</span>
                            <span class="auth-plan-price mono">{{ $t->credit_cost }} cr</span>
                        </div>
                        <div class="auth-plan-sub small">{{ $t->description }}</div>
                    </button>
                @endforeach
            </div>
            <div style="margin-top:12px; display:flex; justify-content:space-between; align-items:center">
                <span class="t-mute small">Balance: <b class="mono">{{ $balance }} cr</b></span>
                <span class="metric-label">Total: <b class="mono" style="color:var(--accent); font-size:14px">{{ $totalCost }} cr</b></span>
            </div>
            @error('tuneSlugs') <div class="auth-hint" style="color:var(--danger); margin-top:8px">{{ $message }}</div> @enderror
        @endif

        @if ($step === 4)
            <div class="metric-label" style="margin-bottom:10px">Upload your file</div>
            <form wire:submit="submit">
                <label class="auth-field">
                    <span>ECU read (.bin, .ori, .frf — up to 10 MB)</span>
                    <input wire:model="upload" type="file" />
                    @error('upload') <span class="auth-hint" style="color:var(--danger)">{{ $message }}</span> @enderror
                </label>
                <label class="auth-field" style="margin-top:14px">
                    <span>Note for the tuner (optional)</span>
                    <input wire:model="note" type="text" placeholder="e.g. pump 99 only, keep DPF in place" />
                </label>

                <div class="card card-pad" style="background:var(--surface-2); margin-top:16px">
                    <div class="metric-label">Order summary</div>
                    <div class="meta-grid" style="grid-template-columns: repeat(2, 1fr); padding:8px 0; background:transparent; border:0">
                        <div class="meta"><div class="metric-label">Vehicle</div><div class="meta-val">{{ optional(\App\Models\Vehicle::find($vehicleId))->displayName() }}</div></div>
                        <div class="meta"><div class="metric-label">ECU</div><div class="meta-val mono">{{ optional($ecus->firstWhere('id', $ecuId))->identifier }}</div></div>
                        <div class="meta"><div class="metric-label">Tune</div><div class="meta-val">{{ $tunes->whereIn('slug', $tuneSlugs)->pluck('label')->implode(' + ') }}</div></div>
                        <div class="meta"><div class="metric-label">Cost</div><div class="meta-val mono">{{ $totalCost }} cr</div></div>
                    </div>
                </div>

                <div style="display:flex; gap:8px; margin-top:18px">
                    <button type="button" wire:click="back" class="ghost-btn ghost-btn-lg">← Back</button>
                    <button type="submit" class="primary-btn primary-btn-lg" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="submit">Submit order →</span>
                        <span wire:loading wire:target="submit">Uploading…</span>
                    </button>
                </div>
            </form>
        @else
            <div style="display:flex; gap:8px; margin-top:18px">
                @if ($step > 1)
                    <button type="button" wire:click="back" class="ghost-btn ghost-btn-lg">← Back</button>
                @endif
                <button type="button" wire:click="next" class="primary-btn primary-btn-lg">Continue →</button>
            </div>
        @endif
    </div>
</div>
