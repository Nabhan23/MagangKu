@extends('layouts.guest')

@section('title', 'Hasil Pencarian: ' . $q . ' - MagangKu')

@section('content')
    <section class="hero-section" style="padding:5rem 0 3rem;">
        <div class="container position-relative" style="z-index:2;">
            <div class="text-center" data-aos="fade-up">
                <span class="badge-mk"><i class="bi bi-search me-1"></i>Hasil Pencarian</span>
                <h1 class="hero-title mt-3">Pencarian untuk "<span class="grad">{{ $q }}</span>"</h1>
            </div>
        </div>
    </section>

    <section class="section pt-4" style="background:var(--mk-bg);">
        <div class="container">
            {{-- Lowongan --}}
            <h5 class="fw-bold mb-3"><i class="bi bi-briefcase text-mk me-2"></i>Lowongan ({{ $jobs->count() }})</h5>
            @if($jobs->count())
                <div class="row g-4 mb-5">
                    @foreach($jobs as $job)
                        <div class="col-md-6 col-lg-4"><x-job-card :job="$job"/></div>
                    @endforeach
                </div>
            @else
                <p class="text-muted-2 small mb-5">Tidak ada lowongan yang cocok.</p>
            @endif

            {{-- Perusahaan --}}
            <h5 class="fw-bold mb-3"><i class="bi bi-buildings text-mk me-2"></i>Perusahaan ({{ $companies->count() }})</h5>
            @if($companies->count())
                <div class="row g-3 mb-5">
                    @foreach($companies as $company)
                        <div class="col-md-6 col-lg-4">
                            <a href="{{ route('companies.show', $company) }}" class="text-decoration-none">
                                <div class="card card-hover p-3 d-flex flex-row align-items-center gap-3">
                                    <div class="avatar" style="width:48px;height:48px;border-radius:1rem;background:#4FC3F7;">{{ strtoupper(substr($company->nama_perusahaan, 0, 1)) }}</div>
                                    <div class="min-w-0">
                                        <div class="fw-semibold small text-accent text-truncate">{{ $company->nama_perusahaan }}</div>
                                        <div class="text-muted-2 small">{{ $company->industri }} • {{ $company->lokasi }}</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted-2 small mb-5">Tidak ada perusahaan yang cocok.</p>
            @endif

            {{-- Event --}}
            <h5 class="fw-bold mb-3"><i class="bi bi-calendar-event text-mk me-2"></i>Event ({{ $events->count() }})</h5>
            @if($events->count())
                <div class="row g-3 mb-5">
                    @foreach($events as $event)
                        <div class="col-md-4">
                            <a href="{{ route('events.show', $event) }}" class="text-decoration-none">
                                <div class="card card-hover p-3 d-flex align-items-center gap-3">
                                    <i class="bi bi-calendar-event-fill fs-2 text-mk"></i>
                                    <div class="min-w-0">
                                        <div class="fw-semibold small text-accent text-truncate">{{ $event->judul }}</div>
                                        <div class="text-muted-2 small">{{ $event->kategori_label }} • {{ $event->tanggal_mulai->translatedFormat('d M Y') }}</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted-2 small mb-5">Tidak ada event yang cocok.</p>
            @endif

            {{-- Artikel --}}
            <h5 class="fw-bold mb-3"><i class="bi bi-journal-text text-mk me-2"></i>Artikel ({{ $articles->count() }})</h5>
            @if($articles->count())
                <div class="row g-3">
                    @foreach($articles as $article)
                        <div class="col-md-4">
                            <a href="{{ route('articles.show', $article) }}" class="text-decoration-none">
                                <div class="card card-hover p-3 d-flex align-items-center gap-3">
                                    <i class="bi bi-file-earmark-text-fill fs-2 text-mk"></i>
                                    <div class="min-w-0">
                                        <div class="fw-semibold small text-accent text-truncate">{{ $article->judul }}</div>
                                        <div class="text-muted-2 small">{{ $article->kategori_label }}</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted-2 small">Tidak ada artikel yang cocok.</p>
            @endif
        </div>
    </section>
@endsection
