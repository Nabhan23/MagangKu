@extends('layouts.auth')

@section('title', 'Reset Password - MagangKu')

@section('auth-content')
    <h5 class="fw-bold mb-1">Reset Password</h5>
    <p class="text-muted-2 small mb-4">Buat password baru untuk akunmu.</p>

    <form method="POST" action="{{ route('password.update') }}" class="d-grid gap-3">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

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
            <label class="form-label">Password Baru</label>
            <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0 rounded-start-4"><i class="bi bi-lock text-mk"></i></span>
                <input type="password" name="password" class="form-control rounded-start-0 {{ $errors->has('password') ? 'is-invalid' : '' }}" placeholder="Minimal 8 karakter" required>
                <button type="button" class="btn btn-soft rounded-end-4 toggle-password" tabindex="-1" aria-label="Tampilkan password"><i class="bi bi-eye"></i></button>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div>
            <label class="form-label">Konfirmasi Password</label>
            <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0 rounded-start-4"><i class="bi bi-shield-lock text-mk"></i></span>
                <input type="password" name="password_confirmation" class="form-control rounded-start-0" placeholder="Ulangi password baru" required>
                <button type="button" class="btn btn-soft rounded-end-4 toggle-password" tabindex="-1" aria-label="Tampilkan password"><i class="bi bi-eye"></i></button>
            </div>
        </div>

        <button type="submit" class="btn btn-mk btn-lg">Reset Password</button>
    </form>
@endsection
