<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php($description = 'Online-Qualifikationstool für J+S-Kurse: Selbsteinschätzung der Teilnehmenden, Rückmeldungen der Leitenden und Qualifikation nach dem offiziellen Dokument.')
    <meta name="description" content="{{ $description }}">
    <meta name="author" content="Jérôme Sigg v/o Amigo">
    <meta name="robots" content="all,follow">

    {{-- Vorschau beim Teilen (WhatsApp, Signal, Teams); Bild 1200 × 630 aus der Luftaufnahme vom Lager --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ config('app.name') }}">
    <meta property="og:title" content="{{ isset($title) ? $title . ' - ' : '' }}{{ config('app.name') }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:image" content="{{ url('/img/og-image.jpg') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:locale" content="de_CH">
    <meta name="twitter:card" content="summary_large_image">

    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="{{asset('/manifest.webmanifest')}}">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#00aba9">
    <meta name="theme-color" content="#f3f4f6" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#1f2937" media="(prefers-color-scheme: dark)">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="Qualitool">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">

    <title>{{isset($title) ? $title . ' - ' : ''}}{{config('app.name')}}</title>

    <!-- Styles -->
    @vite([
        'resources/css/legacy.css',
        'resources/css/app.css',
        'resources/js/app.js'])
    <script>
        // It's best to inline this in `head` to avoid FOUC (flash of unstyled content) when changing pages or themes
        if (localStorage.getItem('color-theme') === 'dark' ||
            (!('color-theme' in localStorage) &&
                window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    @yield('styles')
</head>
