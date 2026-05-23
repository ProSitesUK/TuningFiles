<x-layouts.marketing>
    <div class="mk">
        @include('marketing.partials.nav')

        <section class="mk-section mk-section-narrow">
            <div class="mk-crumbs">
                <a href="{{ route('home') }}">Home</a>
                <span>·</span>
                <a href="{{ route('blog.index') }}">Blog</a>
                <span>·</span>
                <span class="crumb-active">{{ Str::limit($post->title, 48) }}</span>
            </div>

            <article class="mk-article">
                <header class="mk-article-head">
                    <h1 class="mk-article-title">{{ $post->title }}</h1>
                    <div class="mk-article-meta">
                        <span class="mono">{{ optional($post->published_at)->format('j M Y') ?? 'draft' }}</span>
                        @if ($post->author) · <span>by {{ $post->author->name }}</span> @endif
                        · <span>{{ $post->readingMinutes() }} min read</span>
                    </div>
                </header>

                @if ($post->cover_image)
                    <div class="mk-article-cover">
                        <img src="{{ $post->cover_image }}" alt="{{ $post->title }}" />
                    </div>
                @endif

                <div class="mk-prose mk-article-body">
                    {!! $post->bodyHtml() !!}
                </div>
            </article>

            @if ($related->isNotEmpty())
                <h2 class="mk-h2">More from the blog</h2>
                <div class="vb-grid">
                    @foreach ($related as $r)
                        <a href="{{ route('blog.show', $r) }}" class="vb-card vb-card-link" style="text-decoration:none; color:inherit">
                            <div class="vb-card-media">
                                @if ($r->cover_image)
                                    <img src="{{ $r->cover_image }}" alt="{{ $r->title }}" loading="lazy" />
                                @else
                                    <div class="vb-card-media-fallback">
                                        <span class="mono">Blog</span>
                                    </div>
                                @endif
                            </div>
                            <div class="vb-card-body">
                                <div class="vb-card-make small mono">{{ optional($r->published_at)->format('j M Y') }}</div>
                                <h3 class="vb-card-model" style="font-size:16px">{{ $r->title }}</h3>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>

        @include('marketing.partials.footer')
    </div>
</x-layouts.marketing>
