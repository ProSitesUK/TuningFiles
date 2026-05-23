<x-layouts.marketing>
    <div class="mk">
        @include('marketing.partials.nav')

        <section class="mk-section mk-section-narrow">
            <div class="mk-section-head">
                <span class="mk-kicker">Blog</span>
                <h1 class="mk-section-title">Tuning, mapped out.</h1>
                <p class="mk-section-sub">Stage explainers, ECU teardowns, vehicle deep-dives — written for workshops and enthusiasts who want to know what's actually happening when a file changes.</p>
            </div>

            @if ($posts->isEmpty())
                <div class="vb-empty">
                    <div class="empty-title">Nothing published yet</div>
                    <div class="t-mute small">First post is on the way.</div>
                </div>
            @else
                <div class="vb-grid">
                    @foreach ($posts as $post)
                        <a href="{{ route('blog.show', $post) }}" class="vb-card vb-card-link" style="text-decoration:none; color:inherit">
                            <div class="vb-card-media">
                                @if ($post->cover_image)
                                    <img src="{{ $post->cover_image }}" alt="{{ $post->title }}" loading="lazy" />
                                @else
                                    <div class="vb-card-media-fallback">
                                        <span class="mono">{{ \App\Models\SiteSetting::get('site_name', 'TuningFiles') }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="vb-card-body">
                                <div class="vb-card-make small mono">
                                    {{ optional($post->published_at)->format('j M Y') ?? '—' }}
                                    @if ($post->author) · by {{ $post->author->name }} @endif
                                </div>
                                <h2 class="vb-card-model" style="font-size:17px">{{ $post->title }}</h2>
                                @if ($post->excerpt)
                                    <p class="vb-card-meta small t-mute" style="margin-top:4px; line-height:1.5">{{ Str::limit($post->excerpt, 130) }}</p>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>

                @if ($posts->hasPages())
                    <nav class="mk-pager">
                        @if ($posts->onFirstPage())
                            <span class="ghost-btn ghost-btn-sm" style="opacity:.4">← Newer</span>
                        @else
                            <a href="{{ $posts->previousPageUrl() }}" class="ghost-btn ghost-btn-sm" style="text-decoration:none">← Newer</a>
                        @endif
                        <span class="t-mute small mono">page {{ $posts->currentPage() }} / {{ $posts->lastPage() }}</span>
                        @if ($posts->hasMorePages())
                            <a href="{{ $posts->nextPageUrl() }}" class="ghost-btn ghost-btn-sm" style="text-decoration:none">Older →</a>
                        @else
                            <span class="ghost-btn ghost-btn-sm" style="opacity:.4">Older →</span>
                        @endif
                    </nav>
                @endif
            @endif
        </section>

        @include('marketing.partials.footer')
    </div>
</x-layouts.marketing>
