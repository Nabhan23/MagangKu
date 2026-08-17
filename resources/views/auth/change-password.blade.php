@extends('layouts.app')

@section('title', 'Ganti Password - MagangKu')
@section('page-title', 'Ganti Password')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card card-hover p-4" data-aos="fade-up">
                <div class="text-center mb-4">
                    <div class="stat-icon mx-auto mb-3" style="background:var(--mk-primary)1A;color:var(--mk-primary);width:64px;height:64px;font-size:1.5rem;"><i class="bi bi-shield-lock"></i></div>
                    <h5 class="fw-bold mb-1">Ganti Password</h5>
                    <p class="small text-muted-2 mb-0">Masukkan password lama, lalu buat password baru (minimal 8 karakter).</p>
                </div>

                <form method="POST" action="{{ route('password.change.update') }}" class="d-grid gap-3">
                    @csrf

                    <div>
                        <label class="form-label">Password Saat Ini</label>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-end-0 rounded-start-4"><i class="bi bi-lock text-mk"></i></span>
                            <input type="password" name="current_password" class="form-control rounded-start-0 {{ $errors->has('current_password') ? 'is-invalid' : '' }}" placeholder="Password lama" required autofocus>
                            <button type="button" class="btn btn-soft rounded-end-4 toggle-password" tabindex="-1" aria-label="Tampilkan password"><i class="bi bi-eye"></i></button>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="form-label">Password Baru</label>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-end-0 rounded-start-4"><i class="bi bi-shield-lock text-mk"></i></span>
                            <input type="password" name="new_password" class="form-control rounded-start-0 {{ $errors->has('new_password') ? 'is-invalid' : '' }}" placeholder="Minimal 8 karakter" required>
                            <button type="button" class="btn btn-soft rounded-end-4 toggle-password" tabindex="-1" aria-label="Tampilkan password"><i class="bi bi-eye"></i></button>
                            @error('new_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="form-label">Konfirmasi Password Baru</label>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-end-0 rounded-start-4"><i class="bi bi-shield-check text-mk"></i></span>
                            <input type="password" name="new_password_confirmation" class="form-control rounded-start-0" placeholder="Ulangi password baru" required>
                            <button type="button" class="btn btn-soft rounded-end-4 toggle-password" tabindex="-1" aria-label="Tampilkan password"><i class="bi bi-eye"></i></button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-mk btn-lg"><i class="bi bi-check-lg me-1"></i>Ganti Password</button>
                </form>
            </div>
        </div>
    </div>
@endsection
