@extends('layouts.app')

@section('title', 'Pengaturan Kontak - MagangKu')
@section('page-title', 'Pengaturan Kontak & Media Sosial')

@section('content')
    <div class="card card-hover p-4" data-aos="fade-up">
        <div class="small text-muted-2 mb-4">
            <i class="bi bi-info-circle me-1 text-mk"></i>
            Data di bawah ini ditampilkan pada bagian <strong>Kontak Kami</strong> dan ikon media sosial di footer seluruh halaman publik.
        </div>

        <form method="POST" action="{{ route('admin.settings.update') }}" class="d-grid gap-4">
            @csrf

            @if($errors->any())
                <div class="alert alert-danger border-0 rounded-4">
                    <i class="bi bi-exclamation-circle me-1"></i>
                    <strong>Pengaturan tidak tersimpan.</strong> Periksa kembali isian di bawah ini:
                    <ul class="small mb-0 mt-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div>
                <h6 class="fw-bold mb-3"><i class="bi bi-telephone me-2 text-mk"></i>Kontak Kami</h6>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Alamat</label>
                        <input type="text" name="contact_address" value="{{ old('contact_address', $settings['contact_address']) }}" class="form-control @error('contact_address') is-invalid @enderror" placeholder="Jl. Karier Impian No. 1, Jakarta Selatan">
                        @error('contact_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="contact_email" value="{{ old('contact_email', $settings['contact_email']) }}" class="form-control @error('contact_email') is-invalid @enderror" placeholder="halo@magangku.id">
                        @error('contact_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Telepon</label>
                        <input type="text" name="contact_phone" value="{{ old('contact_phone', $settings['contact_phone']) }}" class="form-control @error('contact_phone') is-invalid @enderror" placeholder="+62 812-3456-7890">
                        @error('contact_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label">Jam Operasional</label>
                        <input type="text" name="contact_hours" value="{{ old('contact_hours', $settings['contact_hours']) }}" class="form-control @error('contact_hours') is-invalid @enderror" placeholder="Senin - Jumat, 09.00 - 17.00 WIB">
                        @error('contact_hours')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <hr style="border-color:var(--mk-border);">

            <div>
                <h6 class="fw-bold mb-1"><i class="bi bi-share me-2 text-mk"></i>Media Sosial</h6>
                <div class="small text-muted-2 mb-3">Isi dengan link lengkap (contoh: https://instagram.com/magangku). Kosongkan jika tidak dipakai.</div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label"><i class="bi bi-instagram me-1 text-mk"></i>Instagram</label>
                        <input type="url" name="social_instagram" value="{{ old('social_instagram', $settings['social_instagram']) }}" class="form-control @error('social_instagram') is-invalid @enderror" placeholder="https://instagram.com/magangku">
                        @error('social_instagram')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><i class="bi bi-linkedin me-1 text-mk"></i>LinkedIn</label>
                        <input type="url" name="social_linkedin" value="{{ old('social_linkedin', $settings['social_linkedin']) }}" class="form-control @error('social_linkedin') is-invalid @enderror" placeholder="https://linkedin.com/company/magangku">
                        @error('social_linkedin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><i class="bi bi-facebook me-1 text-mk"></i>Facebook</label>
                        <input type="url" name="social_facebook" value="{{ old('social_facebook', $settings['social_facebook']) }}" class="form-control @error('social_facebook') is-invalid @enderror" placeholder="https://facebook.com/magangku">
                        @error('social_facebook')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><i class="bi bi-tiktok me-1 text-mk"></i>TikTok</label>
                        <input type="url" name="social_tiktok" value="{{ old('social_tiktok', $settings['social_tiktok']) }}" class="form-control @error('social_tiktok') is-invalid @enderror" placeholder="https://tiktok.com/@magangku">
                        @error('social_tiktok')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><i class="bi bi-youtube me-1 text-mk"></i>YouTube</label>
                        <input type="url" name="social_youtube" value="{{ old('social_youtube', $settings['social_youtube']) }}" class="form-control @error('social_youtube') is-invalid @enderror" placeholder="https://youtube.com/@magangku">
                        @error('social_youtube')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <button class="btn btn-mk px-5"><i class="bi bi-check-lg me-1"></i>Simpan Pengaturan</button>
            </div>
        </form>
    </div>
@endsection
