@extends('layouts.guest')

@section('title', 'Hasil ' . $assessment->judul . ' - MagangKu')
@section('meta_description', 'Hasil Skill Assessment ' . $assessment->judul . ' di MagangKu.')

@section('content')
    @php
        $lulus = $result->is_lulus;
        $top = $result->is_top_talent;
        $color = $top ? '#F59E0B' : ($lulus ? '#22C55E' : '#EF4444');
    @endphp

    <section class="section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card border-0 p-4 p-md-5 text-center mb-4" data-aos="zoom-in"
                         style="background:linear-gradient(135deg, {{ $color }}15, #ffffff 60%);border:1px solid {{ $color }}40;">
                        <div class="mx-auto mb-3 d-flex align-items-center justify-content-center"
                             style="width:110px;height:110px;border-radius:50%;background:{{ $color }}15;border:6px solid {{ $color }};">
                            <div>
                                <div class="display-5 fw-bold" style="color:{{ $color }};">{{ $result->skor }}</div>
                                <div class="small text-muted-2">dari 100</div>
                            </div>
                        </div>

                        <span class="badge rounded-pill mb-2 mx-auto" style="background:{{ $color }};color:#fff;font-size:.8rem;">
                            <i class="bi {{ $top ? 'bi-trophy-fill' : ($lulus ? 'bi-check-circle-fill' : 'bi-x-circle-fill') }} me-1"></i>
                            {{ $top ? 'Top Talent! 🏆' : ($lulus ? 'Lulus' : 'Belum Lulus') }}
                        </span>

                        <h1 class="fw-bold fs-3 mb-2">{{ $assessment->judul }}</h1>
                        <p class="text-muted-2 mb-4">
                            Benar <b class="text-success">{{ $result->benar }}</b> dari <b>{{ $result->total_soal }}</b> soal •
                            Salah <b class="text-danger">{{ $result->salah }}</b>
                        </p>

                        @if($top)
                            <div class="alert alert-warning border-0 rounded-4 text-start">
                                <div class="fw-semibold mb-1"><i class="bi bi-trophy-fill me-1"></i>Selamat! Kamu meraih badge <b>Top Talent</b></div>
                                <div class="small">Skor ≥ {{ $batasTopTalent }} membuat profilmu mendapat badge prestasi yang terlihat oleh recruiter. Badge meningkatkan peluang lamaranmu dilirik.</div>
                            </div>
                        @elseif($lulus)
                            <div class="alert alert-success border-0 rounded-4 text-start">
                                <div class="fw-semibold mb-1"><i class="bi bi-check-circle-fill me-1"></i>Bagus! Kamu lulus tes ini</div>
                                <div class="small">Skor kamu mencapai batas lulus ({{ $batasLulus }}). Coba raih skor ≥ {{ $batasTopTalent }} untuk badge Top Talent.</div>
                            </div>
                        @else
                            <div class="alert alert-danger border-0 rounded-4 text-start">
                                <div class="fw-semibold mb-1"><i class="bi bi-x-circle-fill me-1"></i>Jangan menyerah!</div>
                                <div class="small">Batas lulus adalah {{ $batasLulus }}. Pelajari pembahasan di bawah, lalu coba lagi untuk skor lebih baik.</div>
                            </div>
                        @endif

                        <div class="d-flex flex-wrap justify-content-center gap-2 mt-3">
                            @if($lulus)
                                <a href="{{ route('assessment.certificate', $result) }}" class="btn btn-outline-mk"><i class="bi bi-award me-1"></i>Unduh Sertifikat</a>
                            @endif
                            <a href="{{ route('assessment.show', $assessment) }}" class="btn btn-mk"><i class="bi bi-arrow-repeat me-1"></i>Ulangi Tes</a>
                            <a href="{{ route('assessment.index') }}" class="btn btn-soft"><i class="bi bi-grid me-1"></i>Semua Tes</a>
                        </div>
                    </div>

                    {{-- Pembahasan --}}
                    <div class="card card-hover p-4 mb-4" data-aos="fade-up">
                        <h6 class="fw-bold mb-3"><i class="bi bi-journal-check me-2 text-mk"></i>Pembahasan Jawaban</h6>
                        @foreach($result->detail as $i => $d)
                            @php $q = $assessment->questions->firstWhere('id', $d['question_id']); @endphp
                            @if($q)
                                <div class="mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}" style="border-color:var(--mk-border) !important;">
                                    <div class="fw-semibold small mb-1">
                                        {{ $i + 1 }}. {{ $q->pertanyaan }}
                                        <span class="badge rounded-pill ms-1 {{ $d['is_correct'] ? 'text-bg-success' : 'text-bg-danger' }}">
                                            <i class="bi {{ $d['is_correct'] ? 'bi-check-lg' : 'bi-x-lg' }}"></i>
                                        </span>
                                    </div>
                                    <div class="small">
                                        @if(!$d['is_correct'])
                                            <div class="text-danger">Jawaban kamu: {{ $q->{('opsi_' . ($d['picked'] ?? 'a'))} ?? 'Tidak dijawab' }}</div>
                                        @endif
                                        <div class="text-success">Jawaban benar: <b>{{ $q->{'opsi_' . $d['correct']} }}</b></div>
                                        @if($d['pembahasan'])
                                            <div class="text-muted-2 mt-1"><i class="bi bi-lightbulb me-1"></i>{{ $d['pembahasan'] }}</div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    {{-- Rekomendasi lowongan --}}
                    @if($rekomendasi->isNotEmpty())
                        <div class="card card-hover p-4" data-aos="fade-up">
                            <h6 class="fw-bold mb-3"><i class="bi bi-briefcase me-2 text-mk"></i>Lowongan yang Cocok dengan Bidangmu</h6>
                            <div class="row g-3">
                                @foreach($rekomendasi as $lowongan)
                                    <div class="col-md-6">
                                        <a href="{{ route('jobs.show', $lowongan) }}" class="text-decoration-none">
                                            <div class="d-flex align-items-center gap-3 p-3 rounded-4" style="background:var(--mk-bg);">
                                                <img src="{{ asset($lowongan->perusahaan->logo) }}" class="rounded-3" width="44" height="44" style="object-fit:contain;background:#fff;" alt="{{ $lowongan->perusahaan->nama_perusahaan }}">
                                                <div class="min-w-0">
                                                    <div class="fw-semibold small text-accent text-truncate">{{ $lowongan->posisi }}</div>
                                                    <div class="small text-muted-2 text-truncate">{{ $lowongan->perusahaan->nama_perusahaan }} • {{ $lowongan->lokasi }}</div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
