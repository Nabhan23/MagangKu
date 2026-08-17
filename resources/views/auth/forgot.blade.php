@extends('layouts.auth')

@section('title', 'Lupa Password - MagangKu')

@section('auth-content')
    <h5 class="fw-bold mb-1">Lupa Password? 🔑</h5>
    <p class="text-muted-2 small mb-4">Masukkan email kamu dan kami akan mengirimkan tautan untuk mereset password.</p>

    @if(session('status'))
        <div class="alert alert-success border-0 rounded-4 small">
            <i class="bi bi-check-circle-fill me-1"></i>{{ session('status') }}
        </div>
    @endif

    @if(session('reset_link'))
        <div class="alert alert-info border-0 rounded-4 small">
            <div class="fw-semibold mb-1"><i class="bi bi-info-circle-fill me-1"></i>Mode Pengembangan</div>
            Tautan reset (email tidak dikonfigurasi):
            <a href="{{ session('reset_link') }}" class="d-inline-block text-break fw-semibold">{{ session('reset_link') }}</a>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="d-grid gap-3">
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

        <button type="submit" class="btn btn-mk btn-lg">Kirim Tautan Reset</button>
    </form>

    <div class="text-center small text-muted-2 mt-4">
        Ingat password? <a href="{{ route('login') }}" class="text-mk fw-semibold text-decoration-none">Login</a>
    </div>
@endsection
