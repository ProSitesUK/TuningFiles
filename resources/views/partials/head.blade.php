<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta name="csrf-token" content="{{ csrf_token() }}" />

<x-seo />

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|jetbrains-mono:400,500,600&display=swap" rel="stylesheet" />

<script>
  // pre-paint theme — avoids flash on load
  (function () {
    var t = localStorage.getItem('theme');
    if (!t) t = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    document.documentElement.dataset.theme = t;
  })();
</script>

@vite(['resources/css/app.css', 'resources/js/app.js'])
