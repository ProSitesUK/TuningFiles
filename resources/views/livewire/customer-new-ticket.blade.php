<div class="page" style="max-width:640px">
    <div class="crumbs-sm" style="margin-bottom:8px">
        <a href="{{ route('app.tickets.index') }}" style="color:var(--muted); text-decoration:none">Tickets</a>
        <x-icon name="chevron" size="12" />
        <span class="crumb-active">New ticket</span>
    </div>

    <div class="page-head" style="margin-bottom:18px">
        <div>
            <h1 class="page-title">New support ticket</h1>
            <p class="page-sub">We'll get back to you as soon as possible.</p>
        </div>
    </div>

    <form wire:submit="submit" class="card card-pad" style="display:flex; flex-direction:column; gap:14px">
        <div class="va-field">
            <span>Subject</span>
            <input type="text" wire:model="subject" placeholder="What do you need help with?" autocomplete="off" />
            @error('subject') <em class="va-err">{{ $message }}</em> @enderror
        </div>

        <div class="va-field">
            <span>Related order (optional)</span>
            <select wire:model="order_id">
                <option value="">-- None --</option>
                @foreach ($orders as $o)
                    <option value="{{ $o->id }}">#{{ $o->reference }} &mdash; {{ $o->vehicle_label }}</option>
                @endforeach
            </select>
        </div>

        <div class="va-field">
            <span>Message</span>
            <textarea wire:model="body" rows="6" placeholder="Describe your issue in detail..." style="padding:10px 14px; border:1px solid var(--border); border-radius:var(--r-sm); background:var(--bg); color:var(--ink); font-size:13px; font-family:inherit; resize:vertical; outline:0;"></textarea>
            @error('body') <em class="va-err">{{ $message }}</em> @enderror
        </div>

        <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:4px">
            <a href="{{ route('app.tickets.index') }}" class="ghost-btn" style="text-decoration:none">Cancel</a>
            <button type="submit" class="primary-btn">Submit ticket</button>
        </div>
    </form>
</div>
