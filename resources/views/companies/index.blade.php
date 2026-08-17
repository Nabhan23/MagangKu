@extends('layouts.guest')

@section('title', 'Daftar Perusahaan Terverifikasi - MagangKu')

@section('content')
    <section class="hero-section" style="padding:5rem 0 4rem;">
        <div class="container position-relative" style="z-index:2;">
            <div class="text-center" data-aos="fade-up">
                <span class="badge-mk"><i class="bi bi-buildings me-1"></i>Perusahaan Terverifikasi</span>
                <h1 class="hero-title mt-3">Jelajahi <span class="grad">Perusahaan</span> Terpercaya</h1>
                <p class="text-muted-2 mx-auto" style="max-width:600px;">Temukan perusahaan impianmu dari berbagai industri di seluruh Indonesia.</p>
            </div>

            <form method="GET" class="glass rounded-4 p-3 mt-4" data-aos="fade-up" data-aos-delay="100">
                <div class="row g-2 align-items-end">
                    <div class="col-lg-7">
                        <label class="form-label small mb-1"><i class="bi bi-search me-1 text-mk"></i>Cari Perusahaan</label>
                        <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="Nama perusahaan, industri, lokasi...">
                    </div>
                    <div class="col-lg-3">
                        <label class="form-label small mb-1"><i class="bi bi-tag me-1 text-mk"></i>Industri</label>
                        <select name="industri" class="form-select">
                            <option value="">Semua Industri</option>
                            @foreach($industriList as $industri)
                                <option value="{{ $industri }}" @selected(request('industri') === $industri)>{{ $industri }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 d-grid">
                        <button class="btn btn-mk"><i class="bi bi-search me-1"></i>Cari</button>
                    </div>
                </div>
            </form>
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
                @forelse($companies as $company)
                    @php
                        $colors = ['#4FC3F7', '#26A69A', '#5C6BC0', '#FFA726', '#EC407A', '#66BB6A', '#AB47BC', '#8D6E63'];
                        $initial = strtoupper(substr($company->nama_perusahaan, 0, 1));
                    @endphp
                    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 60 }}">
                        <a href="{{ route('companies.show', $company) }}" class="text-decoration-none">
                            <div class="card card-hover h-100 p-4">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="company-logo-lg" style="width:64px;height:64px;font-size:1.5rem;border-radius:1rem;background:{{ $colors[$company->id % count($colors)] }};">
                                        @if($company->logo_url)
                                            <img src="{{ $company->logo_url }}" width="64" height="64" style="object-fit:cover;border-radius:1rem;">
                                        @else
                                            {{ $initial }}
                                        @endif
                                    </div>
                                    <div class="flex-grow-1 min-w-0">
                                        <h6 class="fw-bold text-accent text-truncate mb-1">{{ $company->nama_perusahaan }}</h6>
                                        <div class="small text-muted-2 mb-1"><i class="bi bi-tag me-1 text-mk"></i>{{ $company->industri ?: '-' }}</div>
                                        <div class="small text-muted-2"><i class="bi bi-geo-alt me-1 text-mk"></i>{{ $company->lokasi ?: '-' }}</div>
                                    </div>
                                    @if($company->is_featured)
                                        <span class="badge text-bg-warning" style="font-size:.65rem;"><i class="bi bi-star-fill me-1"></i>Featured</span>
                                    @endif
                                </div>
                                <div class="d-flex align-items-center justify-content-between mt-3 pt-3 border-top" style="border-color:var(--mk-border) !important;">
                                    <div class="small">
                                        <span class="fw-bold text-accent">{{ $company->lowongan_aktif_count }}</span> <span class="text-muted-2">lowongan</span>
                                        <span class="mx-2 text-muted-2">•</span>
                                        <span class="rating"><i class="bi bi-star-fill"></i> {{ $company->rating }}</span>
                                        <span class="text-muted-2">({{ $company->reviews_count }})</span>
                                    </div>
                                    <span class="text-mk small fw-semibold">Lihat <i class="bi bi-arrow-right"></i></span>
                                </div>
                            </div>
                        </a>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <i class="bi bi-buildings fs-1 text-muted-2"></i>
                        <h6 class="fw-bold mt-3">Tidak Ada Perusahaan Ditemukan</h6>
                    </div>
                @endforelse
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $companies->links() }}
            </div>
        </div>
    </section>
@endsection
