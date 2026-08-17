@extends('layouts.auth')

@section('title', 'Login - MagangKu')

@section('auth-content')
    <h5 class="fw-bold mb-1">Selamat Datang Kembali 👋</h5>
    <p class="text-muted-2 small mb-4">Masuk untuk melanjutkan pencarian kariermu.</p>

    @if(session('success'))
        <div class="alert alert-success border-0 rounded-4 small">
            <i class="bi bi-check-circle-fill me-1"></i>{{ session('success') }}
        </div>
    @endif

    @include('auth.partials.social-buttons')

    <form method="POST" action="{{ route('login') }}" class="d-grid gap-3">
        @csrf

        <div>
            <label class="form-label">Email</label>
            <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0 rounded-start-4"><i class="bi bi-envelope text-mk"></i></span>
                <input type="email" name="email" value="{{ old('email') }}" class="form-control rounded-start-0 {{ $errors->has('email') ? 'is-invalid' : '' }}" placeholder="nama@email.com" required autofocus>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div>
            <label class="form-label">Password</label>
            <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0 rounded-start-4"><i class="bi bi-lock text-mk"></i></span>
                <input type="password" name="password" class="form-control rounded-start-0" placeholder="••••••••" required>
                <button type="button" class="btn btn-soft rounded-end-4 toggle-password" tabindex="-1" aria-label="Tampilkan password"><i class="bi bi-eye"></i></button>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                <label class="form-check-label small" for="remember">Ingat saya</label>
            </div>
            <a href="{{ route('password.request') }}" class="small text-mk text-decoration-none fw-semibold">Lupa Password?</a>
        </div>

        <button type="submit" class="btn btn-mk btn-lg">Login <i class="bi bi-box-arrow-in-right ms-1"></i></button>
    </form>

    <div class="text-center small text-muted-2 mt-4">
        Belum punya akun? <a href="{{ route('register') }}" class="text-mk fw-semibold text-decoration-none">Daftar Gratis</a>
    </div>
@endsection
