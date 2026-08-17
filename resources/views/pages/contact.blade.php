@extends('layouts.guest')

@section('title', 'Kontak - MagangKu')

@section('content')
    <section class="hero-section" style="padding:5rem 0 4rem;">
        <div class="container position-relative" style="z-index:2;">
            <div class="text-center" data-aos="fade-up">
                <span class="badge-mk"><i class="bi bi-chat-dots me-1"></i>Hubungi Kami</span>
                <h1 class="hero-title mt-3">Mari <span class="grad">Berkomunikasi</span></h1>
                <p class="text-muted-2 mx-auto" style="max-width:600px;">Punya pertanyaan, masukan, atau ingin bekerja sama? Kami siap membantu.</p>
            </div>
        </div>
        <div class="wave-wrap">
            <svg class="wave-anim" viewBox="0 0 1440 90" preserveAspectRatio="none">
                <path fill="var(--mk-bg)" d="M0,60 C240,90 480,20 720,40 C960,60 1200,90 1440,50 L1440,90 L0,90 Z"></path>
            </svg>
        </div>
    </section>

    <section class="section pt-4" style="background:var(--mk-bg);">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="d-grid gap-3" data-aos="fade-right">
                        @foreach([
                            ['icon' => 'bi-geo-alt-fill', 'title' => 'Alamat', 'desc' => 'Jl. Karier Impian No. 1, Jakarta Selatan, DKI Jakarta 12950'],
                            ['icon' => 'bi-envelope-fill', 'title' => 'Email', 'desc' => 'halo@magangku.id'],
                            ['icon' => 'bi-telephone-fill', 'title' => 'Telepon', 'desc' => '+62 812-3456-7890'],
                            ['icon' => 'bi-clock-fill', 'title' => 'Jam Operasional', 'desc' => 'Senin - Jumat, 09.00 - 17.00 WIB'],
                        ] as $info)
                            <div class="card card-hover p-4 d-flex flex-row align-items-center gap-3">
                                <div class="feature-icon" style="width:52px;height:52px;font-size:1.2rem;flex-shrink:0;"><i class="bi {{ $info['icon'] }}"></i></div>
                                <div>
                                    <div class="fw-bold">{{ $info['title'] }}</div>
                                    <div class="small text-muted-2">{{ $info['desc'] }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="card card-hover p-4 p-md-5" data-aos="fade-left">
                        <h5 class="fw-bold mb-4"><i class="bi bi-send me-2 text-mk"></i>Kirim Pesan</h5>

                        @if(session('success'))
                            <div class="alert alert-success border-0 rounded-4">
                                <i class="bi bi-check-circle-fill me-1"></i>{{ session('success') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('contact.send') }}" class="d-grid gap-3">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nama Lengkap</label>
                                    <input type="text" name="nama" value="{{ old('nama', auth()->user()->name ?? '') }}" class="form-control {{ $errors->has('nama') ? 'is-invalid' : '' }}" required>
                                    @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" required>
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div>
                                <label class="form-label">Subjek</label>
                                <input type="text" name="subjek" value="{{ old('subjek') }}" class="form-control {{ $errors->has('subjek') ? 'is-invalid' : '' }}" placeholder="Konsultasi, Kerja sama, Masukan..." required>
                                @error('subjek')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div>
                                <label class="form-label">Pesan</label>
                                <textarea name="pesan" rows="5" class="form-control {{ $errors->has('pesan') ? 'is-invalid' : '' }}" placeholder="Tulis pesanmu di sini..." required>{{ old('pesan') }}</textarea>
                                @error('pesan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <button class="btn btn-mk btn-lg"><i class="bi bi-send me-1"></i>Kirim Pesan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
