<nav class="navbar navbar-expand-lg navbar-mk fixed-top" id="mainNavbar">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
            <x-logo variant="icon" height="38"/>
            <span class="fw-bold fs-5 text-accent" style="letter-spacing:1px;">MAGANG<span class="text-mk">KU</span></span>
        </a>

        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <i class="bi bi-list fs-1 text-accent"></i>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 align-items-lg-center">
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Beranda</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('jobs.*') ? 'active' : '' }}" href="{{ route('jobs.index') }}">Lowongan</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('jobs.index') && request('jenis') == 'magang' ? 'active' : '' }}" href="{{ route('jobs.index', ['jenis' => 'magang']) }}">Magang</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('companies.*') ? 'active' : '' }}" href="{{ route('companies.index') }}">Perusahaan</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('events.*') ? 'active' : '' }}" href="{{ route('events.index') }}">Event Karier</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('articles.*') ? 'active' : '' }}" href="{{ route('articles.index') }}">Artikel</a></li>
                <li class="nav-item d-lg-none"><a class="nav-link" href="{{ route('about') }}">Tentang Kami</a></li>
                <li class="nav-item d-lg-none"><a class="nav-link" href="{{ route('contact') }}">Kontak</a></li>
            </ul>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                @include('partials.locale-switch')

                <button class="theme-toggle" id="themeToggle" title="Mode Gelap/Terang">
                    <i class="bi bi-moon-fill"></i>
                </button>

                @auth
                    <div class="dropdown">
                        <button class="btn btn-soft dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                            @if(auth()->user()->avatar_url)
                                <img src="{{ auth()->user()->avatar_url }}" class="rounded-circle" width="26" height="26" style="object-fit:cover;">
                            @else
                                <i class="bi bi-person-circle"></i>
                            @endif
                            <span class="d-none d-sm-inline">{{ auth()->user()->name }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><span class="dropdown-item-text text-muted small">{{ auth()->user()->role_name }}</span></li>
                            <li><hr class="dropdown-divider"></li>
                            @if(auth()->user()->isPelamar())
                                <li><a class="dropdown-item" href="{{ route('pelamar.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                                <li><a class="dropdown-item" href="{{ route('pelamar.lamaran.index') }}"><i class="bi bi-briefcase me-2"></i>Lamaran Saya</a></li>
                            @elseif(auth()->user()->isPerusahaan())
                                <li><a class="dropdown-item" href="{{ route('perusahaan.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard HRD</a></li>
                            @else
                                <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard Admin</a></li>
                            @endif
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Keluar</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-mk btn-sm px-3">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-mk btn-sm px-3">Daftar</a>
                @endauth
            </div>
        </div>
    </div>
</nav>
