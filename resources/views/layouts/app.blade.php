<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="@yield('meta_description', \App\Support\SiteSettings::legalName().' — '.\App\Support\SiteSettings::tagline())">

    <title>@yield('title', \App\Support\SiteSettings::shortName())</title>

    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="icon" href="{{ \App\Support\SiteSettings::logoUrl() }}" type="image/png">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-brand-white text-brand-ink font-sans antialiased flex flex-col">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:bg-brand-navy focus:px-4 focus:py-2 focus:text-brand-white">
        Skip to content
    </a>

    <x-site-header />

    <main id="main-content" class="flex-1">
        @yield('content')
    </main>

    <x-client-logo-marquee />

    <x-site-footer />

    <x-whatsapp-button />
</body>
</html>
