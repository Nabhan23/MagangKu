@extends('layouts.guest')

@section('title', 'Tentang Kami - MagangKu')

@section('content')
    <section class="hero-section" style="padding:5rem 0 4rem;">
        <div class="container position-relative" style="z-index:2;">
            <div class="text-center" data-aos="fade-up">
                <span class="badge-mk"><i class="bi bi-info-circle me-1"></i>Tentang Kami</span>
                <h1 class="hero-title mt-3">Tentang <span class="grad">MagangKu</span></h1>
                <p class="text-muted-2 mx-auto" style="max-width:640px;">
                    Platform rekrutmen dan magang yang menghubungkan mahasiswa, fresh graduate, pencari kerja,
                    serta perusahaan dalam satu ekosistem digital modern.
                </p>
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
            <div class="row g-5 align-items-center">
                <div class="col-lg-6" data-aos="fade-right">
                    <img src="{{ asset('img/hero-illustration.svg') }}" alt="Tentang MagangKu" class="img-fluid">
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <h2 class="section-title mb-3">Misi Kami</h2>
                    <p class="text-muted-2 mb-4">
                        MagangKu lahir dari keprihatinan akan sulitnya mahasiswa dan fresh graduate Indonesia
                        menemukan peluang magang dan pekerjaan yang terpercaya. Kami percaya setiap talenta
                        muda berhak mendapatkan <strong>langkah awal menuju karier impian</strong> mereka.
                    </p>
                    <div class="d-grid gap-3">
                        @foreach([
                            ['icon' => 'bi-people-fill', 'title' => 'Menghubungkan Talenta & Perusahaan', 'desc' => 'Jembatan digital antara pencari kerja dan perusahaan terverifikasi.'],
                            ['icon' => 'bi-shield-check-fill', 'title' => 'Keamanan & Kepercayaan', 'desc' => 'Setiap perusahaan diverifikasi untuk melindungi pengguna dari penipuan.'],
                            ['icon' => 'bi-graph-up-arrow', 'title' => 'Transparansi Proses', 'desc' => 'Tracking lamaran realtime sehingga kamu selalu tahu posisimu.'],
                            ['icon' => 'bi-mortarboard-fill', 'title' => 'Pengembangan Talenta', 'desc' => 'Event karier, artikel, dan skill assessment untuk mengembangkan diri.'],
                        ] as $item)
                            <div class="d-flex gap-3">
                                <div class="feature-icon" style="width:52px;height:52px;font-size:1.2rem;flex-shrink:0;"><i class="bi {{ $item['icon'] }}"></i></div>
                                <div>
                                    <h6 class="fw-bold mb-1">{{ $item['title'] }}</h6>
                                    <p class="small text-muted-2 mb-0">{{ $item['desc'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section bg-soft">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="section-title">Nilai-Nilai Kami</h2>
            </div>
            <div class="row g-4">
                @foreach([
                    ['icon' => 'bi-emoji-smile-fill', 'title' => 'Berpihak pada Talenta'],
                    ['icon' => 'bi-heart-fill', 'title' => 'Integritas Tinggi'],
                    ['icon' => 'bi-lightbulb-fill', 'title' => 'Inovasi Terus-Menerus'],
                    ['icon' => 'bi-hand-thumbs-up-fill', 'title' => 'Kolaborasi & Inklusif'],
                ] as $value)
                    <div class="col-md-6 col-lg-3" data-aos="zoom-in" data-aos-delay="{{ $loop->index * 100 }}">
                        <div class="card card-hover feature-card h-100">
                            <div class="feature-icon"><i class="bi {{ $value['icon'] }}"></i></div>
                            <h6 class="fw-bold">{{ $value['title'] }}</h6>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
