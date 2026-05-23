<title>{{ $meta['title'] }}</title>
<meta name="description" content="{{ $meta['description'] }}" />
<meta name="robots" content="{{ $meta['robots'] }}" />
<link rel="canonical" href="{{ $meta['canonical'] }}" />

{{-- Open Graph --}}
<meta property="og:type" content="website" />
<meta property="og:site_name" content="{{ $meta['siteName'] }}" />
<meta property="og:title" content="{{ $meta['title'] }}" />
<meta property="og:description" content="{{ $meta['description'] }}" />
<meta property="og:url" content="{{ $meta['canonical'] }}" />
@if ($meta['ogImage'])
    <meta property="og:image" content="{{ $meta['ogImage'] }}" />
@endif

{{-- Twitter --}}
<meta name="twitter:card" content="{{ $meta['ogImage'] ? 'summary_large_image' : 'summary' }}" />
<meta name="twitter:title" content="{{ $meta['title'] }}" />
<meta name="twitter:description" content="{{ $meta['description'] }}" />
@if ($meta['ogImage'])
    <meta name="twitter:image" content="{{ $meta['ogImage'] }}" />
@endif

{{-- Structured data --}}
@if (! empty($meta['structuredData']))
    <script type="application/ld+json">{!! json_encode($meta['structuredData'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endif

{{-- Google Search Console verification --}}
@if ($gscVerification)
    <meta name="google-site-verification" content="{{ $gscVerification }}" />
@endif

{{-- Google Analytics 4 (production only) --}}
@if ($ga4Id && app()->environment('production'))
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $ga4Id }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ $ga4Id }}', { anonymize_ip: true });
    </script>
@endif
