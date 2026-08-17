@extends('layouts.auth')

@section('title', 'Verifikasi Email - MagangKu')

@section('auth-content')
<div class="text-center">
    @if(session('success'))
        <div class="alert alert-success border-0 rounded-4 small mb-3">{{ session('success') }}</div>
    @endif

    <div class="stat-icon mx-auto mb-3" style="background:#0EA5E91A;color:#0EA5E9;width:64px;height:64px;font-size:1.6rem;">
        <i class="bi bi-envelope-check"></i>
    </div>

    <h1 class="h5 fw-bold mb-2">Verifikasi Email Anda</h1>
    <p class="small text-muted-2 mb-1">
        Kami telah mengirim email verifikasi ke
    </p>
    <p class="fw-semibold small mb-3">{{ auth()->user()->email }}</p>
    <p class="small text-muted-2 mb-4">
        Klik tombol verifikasi di email tersebut untuk mengaktifkan akun Anda. <br>
        Belum menerima email? Periksa folder spam, atau kirim ulang di bawah ini.
    </p>

    <form method="POST" action="{{ route('verification.send') }}" class="d-grid gap-2">
        @csrf
        <button type="submit" class="btn btn-mk w-100"><i class="bi bi-envelope-arrow-up me-1"></i>Kirim Ulang Email Verifikasi</button>
    </form>

    <form method="POST" action="{{ route('logout') }}" class="mt-2">
        @csrf
        <button type="submit" class="btn btn-link btn-sm text-muted-2 text-decoration-none">Ganti akun / keluar</button>
    </form>
</div>
@endsection
