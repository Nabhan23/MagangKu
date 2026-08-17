@extends('layouts.app')

@section('title', 'Dashboard Pelamar - MagangKu')
@section('page-title', 'Dashboard')

@section('content')
    @php
        $badges = [
            'cv_lengkap' => ['CV Lengkap', 'success', 'bi-file-earmark-check-fill'],
            'pelamar_aktif' => ['Pelamar Aktif', 'info', 'bi-lightning-charge-fill'],
            'top_talent' => ['Top Talent', 'warning', 'bi-trophy-fill'],
            'interview_expert' => ['Interview Expert', 'primary', 'bi-mic-fill'],
        ];
        $badge = $badges[$profile->badge] ?? $badges['pelamar_aktif'];
    @endphp

    {{-- Kartu sambutan --}}
    <div class="card border-0 bg-mk-gradient-dark text-white p-4 mb-4" data-aos="fade-up">
        <div class="d-flex flex-wrap align-items-center gap-3">
            <div class="position-relative">
                @if($profile->foto_url)
                    <img src="{{ $profile->foto_url }}" class="rounded-circle border border-3 border-white" width="72" height="72" style="object-fit:cover;">
                @else
                    <div class="avatar avatar-lg border border-3 border-white" style="background:rgba(255,255,255,.25);">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                @endif
                <span class="badge rounded-circle position-absolute bottom-0 end-0 text-bg-{{ $badge[1] }}" title="{{ $badge[0] }}" style="font-size:.65rem;"><i class="bi {{ $badge[2] }}"></i></span>
            </div>
            <div class="flex-grow-1">
                <div class="fw-bold fs-5">{{ $profile->nama ?: auth()->user()->name }}</div>
                <div class="small" style="color:rgba(255,255,255,.9);">
                    <i class="bi bi-mortarboard me-1"></i>{{ $profile->universitas ?: 'Belum mengisi universitas' }}
                    @if($profile->jurusan) • {{ $profile->jurusan }} @endif
                </div>
                <div class="small mt-1">
                    <span class="badge rounded-pill text-bg-{{ $badge[1] }}"><i class="bi {{ $badge[2] }} me-1"></i>{{ $badge[0] }}</span>
                    @if(!$profile->cv_path)
                        <span class="badge rounded-pill text-bg-warning"><i class="bi bi-exclamation-triangle me-1"></i>CV belum diunggah</span>
                    @endif
                </div>
            </div>
            <a href="{{ route('pelamar.profile.edit') }}" class="btn btn-light fw-semibold btn-sm"><i class="bi bi-pencil me-1"></i>Lengkapi Profil</a>
        </div>
    </div>

    {{-- Statistik --}}
    <div class="row g-3 mb-4">
        @foreach([
            ['icon' => 'bi-briefcase-fill', 'color' => '#4FC3F7', 'label' => 'Total Lamaran', 'value' => $totalLamaran, 'link' => route('pelamar.lamaran.index')],
            ['icon' => 'bi-activity', 'color' => '#F59E0B', 'label' => 'Lamaran Aktif', 'value' => $lamaranAktif, 'link' => route('pelamar.lamaran.index')],
            ['icon' => 'bi-check-circle-fill', 'color' => '#22C55E', 'label' => 'Diterima', 'value' => $diterima, 'link' => route('pelamar.lamaran.index')],
            ['icon' => 'bi-bookmark-star-fill', 'color' => '#8B5CF6', 'label' => 'Tersimpan', 'value' => $totalSimpan, 'link' => route('pelamar.saved.index')],
        ] as $stat)
            <div class="col-6 col-xl-3" data-aos="fade-up" data-aos-delay="{{ $loop->index * 60 }}">
                <a href="{{ $stat['link'] }}" class="text-decoration-none">
                    <div class="card card-hover stat-card">
                        <div class="d-flex align-items-center gap-3">
                            <div class="stat-icon" style="background:{{ $stat['color'] }}1A;color:{{ $stat['color'] }};"><i class="bi {{ $stat['icon'] }}"></i></div>
                            <div>
                                <div class="fs-4 fw-bold text-accent">{{ $stat['value'] }}</div>
                                <div class="small text-muted-2">{{ $stat['label'] }}</div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    {{-- Jalur karier & skill assessment --}}
    <div class="row g-4 mb-4">
        <div class="col-md-6" data-aos="fade-up">
            <div class="card card-hover p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0"><i class="bi bi-signpost-split me-2 text-mk"></i>Jalur Karier Saya</h6>
                    <a href="{{ route('roadmaps.index') }}" class="small text-mk fw-semibold text-decoration-none">Jelajahi</a>
                </div>
                @if($roadmap)
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="feature-icon" style="width:48px;height:48px;font-size:1.2rem;margin:0;"><i class="bi {{ $roadmap->ikon }}"></i></div>
                        <div class="min-w-0">
                            <div class="fw-semibold small text-truncate">{{ $roadmap->judul }}</div>
                            <div class="small text-muted-2">{{ $roadmap->bidang_label }}</div>
                        </div>
                    </div>
                    <div class="progress mb-2" style="height:8px;">
                        <div class="progress-bar bg-mk" style="width:{{ $roadmapPersen }}%"></div>
                    </div>
                    <div class="d-flex justify-content-between small text-muted-2 mb-3">
                        <span>{{ count($profile->roadmap_progress ?? []) }}/{{ $roadmap->total_tahapan }} tahapan selesai</span>
                        <b class="text-mk">{{ $roadmapPersen }}%</b>
                    </div>
                    <a href="{{ route('roadmaps.show', $roadmap) }}" class="btn btn-soft btn-sm w-100"><i class="bi bi-map me-1"></i>Lanjutkan Jalur</a>
                @else
                    <p class="small text-muted-2 mb-3">Belum memilih jalur karier. Pilih bidang impianmu dan lihat tahapan menuju karier yang kamu inginkan.</p>
                    <a href="{{ route('roadmaps.index') }}" class="btn btn-mk btn-sm"><i class="bi bi-map me-1"></i>Pilih Jalur Karier</a>
                @endif
            </div>
        </div>
        <div class="col-md-6" data-aos="fade-up" data-aos-delay="80">
            <div class="card card-hover p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0"><i class="bi bi-lightning-charge-fill me-2 text-mk"></i>Skill Assessment</h6>
                    <a href="{{ route('assessment.index') }}" class="small text-mk fw-semibold text-decoration-none">Semua Tes</a>
                </div>
                @forelse($hasilTesTerbaru as $tes)
                    <div class="d-flex align-items-center justify-content-between gap-2 py-2 {{ !$loop->last ? 'border-bottom' : '' }}" style="border-color:var(--mk-border) !important;">
                        <div class="min-w-0">
                            <div class="fw-semibold small text-truncate">{{ $tes->assessment->judul }}</div>
                            <div class="small text-muted-2">{{ $tes->created_at->translatedFormat('d M Y') }}</div>
                        </div>
                        <span class="badge rounded-pill text-nowrap {{ $tes->is_top_talent ? 'text-bg-warning' : ($tes->is_lulus ? 'text-bg-success' : 'text-bg-danger') }}">
                            {{ $tes->skor }}{{ $tes->is_top_talent ? ' 🏆' : '' }}
                        </span>
                    </div>
                @empty
                    <p class="small text-muted-2 mb-3">Ukur kemampuanmu dengan tes gratis dan raih badge <b>Top Talent</b> agar profilmu lebih dilirik recruiter.</p>
                    <a href="{{ route('assessment.index') }}" class="btn btn-mk btn-sm"><i class="bi bi-play-fill me-1"></i>Ikuti Tes</a>
                @endforelse
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Riwayat terbaru --}}
        <div class="col-lg-8">
            <div class="card card-hover p-4" data-aos="fade-up">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0"><i class="bi bi-clock-history me-2 text-mk"></i>Riwayat Lamaran Terbaru</h6>
                    <a href="{{ route('pelamar.lamaran.index') }}" class="small text-mk fw-semibold text-decoration-none">Lihat Semua</a>
                </div>
                @forelse($riwayatTerbaru as $lamaran)
                    <div class="d-flex align-items-center justify-content-between gap-3 py-3 {{ !$loop->last ? 'border-bottom' : '' }}" style="border-color:var(--mk-border) !important;">
                        <div class="min-w-0">
                            <div class="fw-semibold small text-truncate">{{ $lamaran->lowongan->posisi }}</div>
                            <div class="small text-muted-2">{{ $lamaran->lowongan->perusahaan->nama_perusahaan }} • {{ $lamaran->created_at->translatedFormat('d M Y') }}</div>
                        </div>
                        <span class="badge rounded-pill text-bg-{{ $lamaran->status_color }} text-nowrap">{{ $lamaran->status_label }}</span>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <i class="bi bi-inbox fs-1 text-muted-2"></i>
                        <p class="text-muted-2 small mt-2 mb-0">Belum ada lamaran. Yuk mulai cari lowongan!</p>
                        <a href="{{ route('jobs.index') }}" class="btn btn-mk btn-sm mt-3">Cari Lowongan</a>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Notifikasi & aksi cepat --}}
        <div class="col-lg-4">
            <div class="card card-hover p-4 mb-4" data-aos="fade-up">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0"><i class="bi bi-bell me-2 text-mk"></i>Notifikasi</h6>
                    <a href="{{ route('pelamar.notifications.index') }}" class="small text-mk fw-semibold text-decoration-none">Lihat Semua</a>
                </div>
                @php $notifs = auth()->user()->notifications()->latest()->take(3)->get(); @endphp
                @forelse($notifs as $notif)
                    <div class="small py-2 {{ !$loop->last ? 'border-bottom' : '' }}" style="border-color:var(--mk-border) !important;">
                        <div class="fw-semibold {{ $notif->is_read ? 'text-muted-2' : 'text-accent' }}">
                            @if(!$notif->is_read)<span class="badge rounded-circle text-bg-danger me-1" style="width:7px;height:7px;display:inline-block;padding:0;"></span>@endif
                            {{ $notif->judul }}
                        </div>
                        <div class="text-muted-2 text-truncate">{{ $notif->pesan }}</div>
                    </div>
                @empty
                    <p class="text-muted-2 small mb-0 text-center py-3">Tidak ada notifikasi.</p>
                @endforelse
            </div>

            <div class="card card-hover p-4" data-aos="fade-up">
                <h6 class="fw-bold mb-3"><i class="bi bi-lightning me-2 text-mk"></i>Aksi Cepat</h6>
                <div class="d-grid gap-2">
                    <a href="{{ route('jobs.index') }}" class="btn btn-mk"><i class="bi bi-search me-1"></i>Cari Lowongan</a>
                    <a href="{{ route('assessment.index') }}" class="btn btn-soft"><i class="bi bi-lightning-charge me-1"></i>Skill Assessment</a>
                    <a href="{{ route('roadmaps.index') }}" class="btn btn-soft"><i class="bi bi-signpost-split me-1"></i>Career Roadmap</a>
                    <a href="{{ route('events.index') }}" class="btn btn-soft"><i class="bi bi-calendar-event me-1"></i>Event Karier</a>
                    <a href="{{ route('pelamar.profile.edit') }}" class="btn btn-soft"><i class="bi bi-file-earmark-arrow-up me-1"></i>Upload CV</a>
                </div>
            </div>
        </div>
    </div>
@endsection
