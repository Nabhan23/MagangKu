<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard - MagangKu')</title>
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
    @stack('styles')
</head>
<body>

    {{-- Sidebar --}}
    <aside class="sidebar" id="appSidebar">
        <div class="p-3 d-flex align-items-center gap-2 border-bottom" style="border-color:rgba(255,255,255,.12) !important;">
            <x-logo variant="icon" height="38"/>
            <div>
                <div class="fw-bold text-white" style="letter-spacing:1px;">MAGANG<span class="text-mk">KU</span></div>
                <div class="small" style="color:rgba(255,255,255,.55);font-size:.7rem;">{{ auth()->user()->role_name }}</div>
            </div>
            <button class="btn btn-sm ms-auto d-lg-none text-white" id="sidebarClose" style="background:rgba(255,255,255,.1);"><i class="bi bi-x-lg"></i></button>
        </div>

        <nav class="nav flex-column pb-4">
            @if(auth()->user()->isPelamar())
                <a class="nav-link {{ request()->routeIs('pelamar.dashboard') ? 'active' : '' }}" href="{{ route('pelamar.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>

                <div class="sidebar-section">Karier</div>
                <a class="nav-link {{ request()->routeIs('pelamar.lamaran.*') ? 'active' : '' }}" href="{{ route('pelamar.lamaran.index') }}"><i class="bi bi-briefcase me-2"></i>Tracking Lamaran</a>
                <a class="nav-link {{ request()->routeIs('pelamar.saved.*') ? 'active' : '' }}" href="{{ route('pelamar.saved.index') }}"><i class="bi bi-bookmark-star me-2"></i>Lowongan Tersimpan</a>
                <a class="nav-link {{ request()->routeIs('assessment.*') ? 'active' : '' }}" href="{{ route('assessment.index') }}"><i class="bi bi-lightning-charge me-2"></i>Skill Assessment</a>
                <a class="nav-link {{ request()->routeIs('roadmaps.*') ? 'active' : '' }}" href="{{ route('roadmaps.index') }}"><i class="bi bi-signpost-split me-2"></i>Career Roadmap</a>

                <div class="sidebar-section">Akun</div>
                <a class="nav-link {{ request()->routeIs('pelamar.profile.*') ? 'active' : '' }}" href="{{ route('pelamar.profile.edit') }}"><i class="bi bi-person-gear me-2"></i>Profil & CV</a>
                <a class="nav-link {{ request()->routeIs('pelamar.chat.*') ? 'active' : '' }}" href="{{ route('pelamar.chat.index') }}">
                    <i class="bi bi-chat-dots me-2"></i>Chat
                    @if(auth()->user()->unread_messages_count)
                        <span class="badge rounded-pill text-bg-danger ms-auto">{{ auth()->user()->unread_messages_count }}</span>
                    @endif
                </a>
                <a class="nav-link {{ request()->routeIs('pelamar.notifications.*') ? 'active' : '' }}" href="{{ route('pelamar.notifications.index') }}">
                    <i class="bi bi-bell me-2"></i>Notifikasi
                    @if(auth()->user()->unreadNotifications()->count())
                        <span class="badge rounded-pill text-bg-danger ms-auto">{{ auth()->user()->unreadNotifications()->count() }}</span>
                    @endif
                </a>
                <a class="nav-link {{ request()->routeIs('notifikasi-prefs.*') ? 'active' : '' }}" href="{{ route('notifikasi-prefs.index') }}"><i class="bi bi-envelope-check me-2"></i>Preferensi Notifikasi</a>
                <a class="nav-link {{ request()->routeIs('password.change') ? 'active' : '' }}" href="{{ route('password.change') }}"><i class="bi bi-shield-lock me-2"></i>Ganti Password</a>
            @elseif(auth()->user()->isPerusahaan())
                <a class="nav-link {{ request()->routeIs('perusahaan.dashboard') ? 'active' : '' }}" href="{{ route('perusahaan.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard HRD</a>

                <div class="sidebar-section">Rekrutmen</div>
                <a class="nav-link {{ request()->routeIs('perusahaan.lowongan.*') ? 'active' : '' }}" href="{{ route('perusahaan.lowongan.index') }}"><i class="bi bi-briefcase me-2"></i>Kelola Lowongan</a>
                <a class="nav-link {{ request()->routeIs('perusahaan.pelamar.*') ? 'active' : '' }}" href="{{ route('perusahaan.pelamar.index') }}"><i class="bi bi-people me-2"></i>Kelola Pelamar</a>
                <a class="nav-link {{ request()->routeIs('perusahaan.interview.*') ? 'active' : '' }}" href="{{ route('perusahaan.interview.calendar') }}"><i class="bi bi-calendar-week me-2"></i>Kalender Interview</a>
                <a class="nav-link {{ request()->routeIs('perusahaan.chat.*') ? 'active' : '' }}" href="{{ route('perusahaan.chat.index') }}">
                    <i class="bi bi-chat-dots me-2"></i>Chat Pelamar
                    @if(auth()->user()->unread_messages_count)
                        <span class="badge rounded-pill text-bg-danger ms-auto">{{ auth()->user()->unread_messages_count }}</span>
                    @endif
                </a>

                <div class="sidebar-section">Profil</div>
                <a class="nav-link {{ request()->routeIs('perusahaan.profile.*') ? 'active' : '' }}" href="{{ route('perusahaan.profile.edit') }}"><i class="bi bi-building-gear me-2"></i>Profil Perusahaan</a>
                <a class="nav-link {{ request()->routeIs('notifikasi-prefs.*') ? 'active' : '' }}" href="{{ route('notifikasi-prefs.index') }}"><i class="bi bi-envelope-check me-2"></i>Preferensi Notifikasi</a>
                <a class="nav-link {{ request()->routeIs('password.change') ? 'active' : '' }}" href="{{ route('password.change') }}"><i class="bi bi-shield-lock me-2"></i>Ganti Password</a>
            @else
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>

                <div class="sidebar-section">Master Data</div>
                <a class="nav-link {{ request()->routeIs('admin.verifikasi.*') ? 'active' : '' }}" href="{{ route('admin.verifikasi.index') }}"><i class="bi bi-patch-check me-2"></i>Verifikasi Perusahaan</a>
                <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}"><i class="bi bi-people me-2"></i>Kelola Pengguna</a>
                <a class="nav-link {{ request()->routeIs('admin.lowongan.*') ? 'active' : '' }}" href="{{ route('admin.lowongan.index') }}"><i class="bi bi-briefcase me-2"></i>Kelola Lowongan</a>
                <a class="nav-link {{ request()->routeIs('admin.content.*') ? 'active' : '' }}" href="{{ route('admin.content.index') }}"><i class="bi bi-layout-text-window me-2"></i>Kelola Konten</a>

                <div class="sidebar-section">Sistem</div>
                <a class="nav-link {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}" href="{{ route('admin.notifications.index') }}"><i class="bi bi-megaphone me-2"></i>Broadcast Notifikasi</a>
                <a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" href="{{ route('admin.reports.index') }}"><i class="bi bi-graph-up me-2"></i>Laporan Sistem</a>
                <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.index') }}"><i class="bi bi-gear me-2"></i>Pengaturan Kontak</a>
                <a class="nav-link {{ request()->routeIs('admin.activity.*') ? 'active' : '' }}" href="{{ route('admin.activity.index') }}"><i class="bi bi-clock-history me-2"></i>Aktivitas Log</a>
                <a class="nav-link {{ request()->routeIs('notifikasi-prefs.*') ? 'active' : '' }}" href="{{ route('notifikasi-prefs.index') }}"><i class="bi bi-envelope-check me-2"></i>Preferensi Notifikasi</a>
                <a class="nav-link {{ request()->routeIs('password.change') ? 'active' : '' }}" href="{{ route('password.change') }}"><i class="bi bi-shield-lock me-2"></i>Ganti Password</a>
            @endif

            <hr style="border-color:rgba(255,255,255,.12);margin-inline:1rem;">

            <a class="nav-link" href="{{ route('home') }}"><i class="bi bi-house me-2"></i>Lihat Website</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-link w-100 text-start border-0 bg-transparent"><i class="bi bi-box-arrow-right me-2"></i>Keluar</button>
            </form>
        </nav>
    </aside>

    {{-- Overlay mobile --}}
    <div class="offcanvas-backdrop fade d-none" id="sidebarOverlay" style="z-index:1035;"></div>

    {{-- Main --}}
    <div class="main-content">
        {{-- Topbar --}}
        <nav class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-soft btn-sm d-lg-none" id="sidebarToggle"><i class="bi bi-list"></i></button>
                <div>
                    <h5 class="fw-bold mb-0">@yield('page-title', 'Dashboard')</h5>
                    <div class="small text-muted-2">{{ now()->translatedFormat('l, d F Y') }}</div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                @include('partials.locale-switch')
                <button class="theme-toggle" id="themeToggle"><i class="bi bi-moon-fill"></i></button>
                <a href="{{ route('home') }}" class="btn btn-outline-mk btn-sm"><i class="bi bi-house me-1"></i><span class="d-none d-sm-inline">Website</span></a>
            </div>
        </nav>

        @yield('content')
    </div>

    <div id="toastContainer" class="position-fixed bottom-0 end-0 p-3" style="z-index:2000;"></div>

    @if(session('success'))
        <script>window.addEventListener('DOMContentLoaded', () => mkToast(@js(session('success')), 'success'));</script>
    @endif
    @if(session('error'))
        <script>window.addEventListener('DOMContentLoaded', () => mkToast(@js(session('error')), 'error'));</script>
    @endif
    @if($errors->any())
        <script>window.addEventListener('DOMContentLoaded', () => mkToast(@js('Data tidak tersimpan: ' . $errors->first()), 'error'));</script>
    @endif

    @stack('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const sidebar = document.getElementById('appSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const show = () => { sidebar.classList.add('show'); overlay.classList.remove('d-none'); overlay.classList.add('show'); };
            const hide = () => { sidebar.classList.remove('show'); overlay.classList.add('d-none'); overlay.classList.remove('show'); };
            document.getElementById('sidebarToggle')?.addEventListener('click', show);
            document.getElementById('sidebarClose')?.addEventListener('click', hide);
            overlay?.addEventListener('click', hide);
        });
    </script>
</body>
</html>
