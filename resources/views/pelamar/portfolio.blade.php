@extends('layouts.guest')

@section('title', 'Portofolio ' . $profile->nama . ' - MagangKu')
@section('meta_description', 'Portofolio publik ' . $profile->nama . ' di MagangKu.')

@section('content')
    <section class="section" style="padding-top:3rem;">
        <div class="container" style="max-width:900px;">
            <div class="card card-hover p-4 p-md-5 text-center mb-4" data-aos="fade-up">
                @if($profile->foto_url)
                    <img src="{{ $profile->foto_url }}" class="rounded-circle mx-auto mb-3" width="120" height="120" style="object-fit:cover;border:4px solid var(--mk-secondary);">
                @else
                    <div class="avatar avatar-lg mx-auto mb-3" style="background:var(--mk-gradient);width:120px;height:120px;font-size:2.6rem;">{{ strtoupper(substr($profile->nama, 0, 1)) }}</div>
                @endif

                <h3 class="fw-bold">{{ $profile->nama }}</h3>
                <div class="text-muted-2">
                    <i class="bi bi-mortarboard me-1 text-mk"></i>{{ $profile->universitas ?: 'Universitas' }} @if($profile->jurusan) • {{ $profile->jurusan }} @endif
                </div>
                <div class="text-muted-2 small"><i class="bi bi-geo-alt me-1 text-mk"></i>{{ $profile->lokasi ?: '-' }}</div>

                <div class="d-flex justify-content-center gap-2 mt-3 flex-wrap">
                    @if($profile->website_portfolio)
                        <a href="{{ $profile->website_portfolio }}" target="_blank" class="btn btn-soft btn-sm"><i class="bi bi-globe me-1"></i>Website</a>
                    @endif
                    @if($profile->linkedin)
                        <a href="{{ $profile->linkedin }}" target="_blank" class="btn btn-soft btn-sm"><i class="bi bi-linkedin me-1"></i>LinkedIn</a>
                    @endif
                    @if($profile->github)
                        <a href="{{ $profile->github }}" target="_blank" class="btn btn-soft btn-sm"><i class="bi bi-github me-1"></i>GitHub</a>
                    @endif
                    @if($profile->cv_path && auth()->check() && auth()->user()->pelamarProfile?->id === $profile->id)
                        <a href="{{ route('pelamar.profile.downloadCv') }}" class="btn btn-mk btn-sm"><i class="bi bi-file-earmark-text me-1"></i>Download CV</a>
                    @endif
                </div>
            </div>

            @if($profile->keahlian)
                <div class="card card-hover p-4 mb-4" data-aos="fade-up">
                    <h6 class="fw-bold mb-3"><i class="bi bi-lightning-charge me-2 text-mk"></i>Keahlian</h6>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach(explode(',', $profile->keahlian) as $skill)
                            <span class="chip" style="cursor:default;">{{ trim($skill) }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($profile->tentang)
                <div class="card card-hover p-4 mb-4" data-aos="fade-up">
                    <h6 class="fw-bold mb-3"><i class="bi bi-person me-2 text-mk"></i>Tentang Saya</h6>
                    <p class="text-muted-2 mb-0" style="white-space:pre-line;">{{ $profile->tentang }}</p>
                </div>
            @endif

            @if($profile->lamarans->count())
                <div class="card card-hover p-4 mb-4" data-aos="fade-up">
                    <h6 class="fw-bold mb-3"><i class="bi bi-briefcase me-2 text-mk"></i>Pengalaman Melamar</h6>
                    <div class="d-grid gap-3">
                        @foreach($profile->lamarans->take(5) as $lamaran)
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-semibold small">{{ $lamaran->lowongan->posisi }}</div>
                                    <div class="small text-muted-2">{{ $lamaran->lowongan->perusahaan->nama_perusahaan }}</div>
                                </div>
                                <span class="badge rounded-pill text-bg-{{ $lamaran->status_color }}">{{ $lamaran->status_label }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="text-center small text-muted-2">
                Portofolio publik • {{ $profile->views }}x dilihat • MagangKu
            </div>
        </div>
    </section>
@endsection
