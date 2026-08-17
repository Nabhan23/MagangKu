@extends('layouts.guest')

@section('title', 'Skill Assessment - MagangKu')
@section('meta_description', 'Ikuti tes kemampuan dasar gratis di MagangKu untuk mengukur skill-mu dan menonjol di mata recruiter.')

@section('content')
    <section class="section">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <span class="badge-mk"><i class="bi bi-lightning-charge-fill me-1"></i>Skill Assessment</span>
                <h1 class="section-title mt-3">Ukur Kemampuanmu, Buktikan ke Recruiter</h1>
                <p class="section-sub mx-auto">Kerjakan tes kemampuan dasar secara gratis, dapatkan skor, dan raih badge <b>Top Talent</b> untuk meningkatkan peluang dilirik perusahaan.</p>
            </div>

            <div class="row g-4">
                @forelse($assessments as $assessment)
                    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 70 }}">
                        <div class="card card-hover h-100 p-4 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="feature-icon" style="width:56px;height:56px;font-size:1.4rem;">
                                    <i class="bi {{ match ($assessment->kategori) {
                                        'teknologi' => 'bi-code-slash',
                                        'desain' => 'bi-palette',
                                        'marketing' => 'bi-megaphone',
                                        'keuangan' => 'bi-cash-coin',
                                        default => 'bi-person-check',
                                    } }}"></i>
                                </div>
                                <span class="badge rounded-pill text-bg-info text-white">{{ $assessment->kategori_label }}</span>
                            </div>

                            <h5 class="fw-bold mb-1">{{ $assessment->judul }}</h5>
                            <p class="small text-muted-2 mb-3">{{ $assessment->deskripsi }}</p>

                            <div class="d-flex flex-wrap gap-3 small text-muted-2 mb-3">
                                <span><i class="bi bi-patch-question me-1 text-mk"></i>{{ $assessment->questions_count }} Soal</span>
                                <span><i class="bi bi-stopwatch me-1 text-mk"></i>{{ $assessment->durasi_menit }} Menit</span>
                                <span><i class="bi bi-people me-1 text-mk"></i>{{ $assessment->jumlah_peserta }} Peserta</span>
                            </div>

                            <div class="mt-auto">
                                <a href="{{ route('assessment.show', $assessment) }}" class="btn btn-mk w-100">
                                    <i class="bi bi-play-fill me-1"></i>Ikuti Tes Gratis
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <i class="bi bi-journal-x fs-1 text-muted-2"></i>
                        <p class="text-muted-2 mt-3">Belum ada tes tersedia. Nantikan segera!</p>
                    </div>
                @endforelse
            </div>

            <div class="text-center mt-5" data-aos="fade-up">
                <div class="card border-0 bg-soft p-4 d-inline-flex flex-wrap align-items-center gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-trophy-fill text-warning fs-4"></i>
                        <div class="text-start">
                            <div class="fw-bold small">Badge Top Talent</div>
                            <div class="small text-muted-2">Skor ≥ 85 langsung mendapat badge</div>
                        </div>
                    </div>
                    <div class="vr d-none d-md-block"></div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-patch-check-fill text-success fs-4"></i>
                        <div class="text-start">
                            <div class="fw-bold small">Sertifikat Skor</div>
                            <div class="small text-muted-2">Hasil terlihat di dashboard & profil</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
