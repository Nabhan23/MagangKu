<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MagangKu - Langkah Awal Menuju Karier Impian')</title>
    <meta name="description" content="@yield('meta_description', 'Platform pencarian magang dan pekerjaan terpercaya di Indonesia. Ribuan peluang dari perusahaan terverifikasi.')">
    <meta name="keywords" content="magang, lowongan kerja, internship, fresh graduate, karier, pekerjaan, rekrutmen, job fair">
    <meta name="robots" content="index, follow">
    <meta name="author" content="MagangKu">
    <link rel="canonical" href="@yield('canonical', url()->current())">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:site_name" content="MagangKu">
    <meta property="og:title" content="@yield('og_title', 'MagangKu - Langkah Awal Menuju Karier Impian')">
    <meta property="og:description" content="@yield('og_description', 'Platform pencarian magang dan pekerjaan terpercaya di Indonesia. Ribuan peluang dari perusahaan terverifikasi.')">
    <meta property="og:url" content="@yield('canonical', url()->current())">
    <meta property="og:image" content="@yield('og_image', url('/img/pwa/icon-512.png'))">
    <meta property="og:locale" content="{{ app()->getLocale() === 'en' ? 'en_US' : 'id_ID' }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('og_title', 'MagangKu - Langkah Awal Menuju Karier Impian')">
    <meta name="twitter:description" content="@yield('og_description', 'Platform pencarian magang dan pekerjaan terpercaya di Indonesia.')">
    <meta name="twitter:image" content="@yield('og_image', url('/img/pwa/icon-512.png'))">
    <link rel="icon" type="image/svg+xml" href="{{ asset('img/favicon.svg') }}">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#4FC3F7">
    <link rel="apple-touch-icon" href="/img/pwa/apple-touch-icon.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="MagangKu">
    <link rel="apple-touch-icon" href="{{ asset('img/app-icon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    @stack('head')
</head>
<body class="d-flex flex-column min-vh-100">

    {{-- Loading Screen --}}
    <div id="loader">
        <div class="text-center">
            <div class="loader-logo mb-3"><x-logo variant="icon" height="72"/></div>
            <div class="spinner-border text-mk" role="status"></div>
        </div>
    </div>

    @include('partials.navbar')

    <main class="flex-grow-1" style="padding-top:72px;">
        @yield('content')
    </main>

    @include('partials.footer')

    <button class="back-to-top" id="backToTop" title="Kembali ke atas"><i class="bi bi-arrow-up"></i></button>

    <div id="toastContainer" class="position-fixed bottom-0 end-0 p-3" style="z-index:2000;"></div>

    @if(session('success'))
        <script>window.addEventListener('DOMContentLoaded', () => mkToast(@js(session('success')), 'success'));</script>
    @endif
    @if(session('error'))
        <script>window.addEventListener('DOMContentLoaded', () => mkToast(@js(session('error')), 'error'));</script>
    @endif

    @stack('scripts')
</body>
</html>
