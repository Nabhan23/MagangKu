<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MagangKu')</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('img/favicon.svg') }}">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#4FC3F7">
    <link rel="apple-touch-icon" href="/img/pwa/apple-touch-icon.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="MagangKu">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-soft">

    <div id="loader">
        <div class="text-center">
            <div class="loader-logo mb-3"><x-logo variant="icon" height="72"/></div>
            <div class="spinner-border text-mk" role="status"></div>
        </div>
    </div>

    <div class="position-absolute top-0 end-0 p-3" style="z-index:5;">
        @include('partials.locale-switch')
    </div>

    <div class="min-vh-100 d-flex align-items-center justify-content-center p-3 position-relative overflow-hidden">
        {{-- Dekorasi --}}
        <div class="floating-shape" style="width:220px;height:220px;background:rgba(79,195,247,.18);top:-60px;left:-60px;"></div>
        <div class="floating-shape" style="width:160px;height:160px;background:rgba(129,212,250,.25);bottom:-40px;right:-40px;animation-delay:1.2s;"></div>

        <div class="w-100" style="max-width:480px;">
            <div class="text-center mb-4">
                <a href="{{ route('home') }}" class="d-inline-block mb-3"><x-logo variant="icon" height="64"/></a>
                <h4 class="fw-bold mb-1">MAGANG<span class="text-mk">KU</span></h4>
                <p class="text-muted-2 small mb-0">Langkah Awal Menuju Karier Impian</p>
            </div>

            <div class="glass-card rounded-4 p-4 p-md-5">
                @yield('auth-content')
            </div>

            <div class="text-center mt-4">
                <a href="{{ route('home') }}" class="text-muted-2 small text-decoration-none"><i class="bi bi-arrow-left me-1"></i>Kembali ke Beranda</a>
            </div>
        </div>
    </div>

    <div id="toastContainer" class="position-fixed bottom-0 end-0 p-3" style="z-index:2000;"></div>

    @stack('scripts')
</body>
</html>
