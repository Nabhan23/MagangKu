@extends('layouts.app')

@section('title', 'Dashboard Admin - MagangKu')
@section('page-title', 'Dashboard Admin')

@section('content')
    {{-- Statistik --}}
    <div class="row g-3 mb-4">
        @foreach([
            ['icon' => 'bi-people-fill', 'color' => '#4FC3F7', 'label' => 'Pelamar', 'value' => $stats['pelamar'], 'link' => route('admin.users.index', ['role' => 'pelamar'])],
            ['icon' => 'bi-buildings-fill', 'color' => '#8B5CF6', 'label' => 'Perusahaan', 'value' => $stats['perusahaan'], 'link' => route('admin.users.index', ['role' => 'perusahaan'])],
            ['icon' => 'bi-briefcase-fill', 'color' => '#22C55E', 'label' => 'Lowongan', 'value' => $stats['lowongan'], 'link' => route('admin.lowongan.index')],
            ['icon' => 'bi-send-fill', 'color' => '#F59E0B', 'label' => 'Lamaran', 'value' => $stats['lamaran'], 'link' => route('admin.reports.index')],
            ['icon' => 'bi-calendar-event-fill', 'color' => '#EC4899', 'label' => 'Event', 'value' => $stats['event'], 'link' => route('admin.content.index', ['tab' => 'event'])],
            ['icon' => 'bi-journal-richtext', 'color' => '#14B8A6', 'label' => 'Artikel', 'value' => $stats['artikel'], 'link' => route('admin.content.index', ['tab' => 'artikel'])],
        ] as $stat)
            <div class="col-6 col-md-4 col-xl-2" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                <a href="{{ $stat['link'] }}" class="text-decoration-none">
                    <div class="card card-hover stat-card text-center">
                        <div class="stat-icon mx-auto mb-2" style="background:{{ $stat['color'] }}1A;color:{{ $stat['color'] }};"><i class="bi {{ $stat['icon'] }}"></i></div>
                        <div class="fs-4 fw-bold text-accent">{{ number_format($stat['value']) }}</div>
                        <div class="small text-muted-2">{{ $stat['label'] }}</div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card card-hover p-4 mb-4" data-aos="fade-up">
                <h6 class="fw-bold mb-4"><i class="bi bi-send me-2 text-mk"></i>Lamaran per Bulan (6 Bulan)</h6>
                <div class="d-flex align-items-end gap-2" style="height:200px;">
                    @foreach($grafikLamaran as $b)
                        @php $max = max($grafikLamaran->max('jumlah'), 1); $height = max(6, round(($b['jumlah'] / $max) * 100)); @endphp
                        <div class="flex-grow-1 d-flex flex-column align-items-center gap-2">
                            <div class="fw-bold small text-accent">{{ $b['jumlah'] }}</div>
                            <div class="w-100 rounded-3" style="height:{{ $height }}%;background:var(--mk-gradient);min-height:8px;"></div>
                            <div class="small text-muted-2">{{ $b['bulan'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="card card-hover p-4" data-aos="fade-up">
                <h6 class="fw-bold mb-4"><i class="bi bi-person-plus me-2 text-mk"></i>Pendaftar Baru per Bulan</h6>
                <div class="d-flex align-items-end gap-2" style="height:180px;">
                    @foreach($grafikUser as $b)
                        @php
                            $max = max($grafikUser->max('pelamar'), $grafikUser->max('perusahaan'), 1);
                            $h1 = max(6, round(($b['pelamar'] / $max) * 100));
                            $h2 = max(6, round(($b['perusahaan'] / $max) * 100));
                        @endphp
                        <div class="flex-grow-1 d-flex flex-column align-items-center gap-2">
                            <div class="d-flex align-items-end gap-1 flex-grow-1 w-100 justify-content-center">
                                <div class="rounded-3" style="height:{{ $h1 }}%;width:35%;background:#4FC3F7;min-height:6px;" title="Pelamar: {{ $b['pelamar'] }}"></div>
                                <div class="rounded-3" style="height:{{ $h2 }}%;width:35%;background:#8B5CF6;min-height:6px;" title="Perusahaan: {{ $b['perusahaan'] }}"></div>
                            </div>
                            <div class="small text-muted-2">{{ $b['bulan'] }}</div>
                        </div>
                    @endforeach
                </div>
                <div class="d-flex gap-3 mt-3 small text-muted-2">
                    <span><span class="d-inline-block rounded-3 me-1" style="width:12px;height:12px;background:#4FC3F7;"></span>Pelamar</span>
                    <span><span class="d-inline-block rounded-3 me-1" style="width:12px;height:12px;background:#8B5CF6;"></span>Perusahaan</span>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card card-hover p-4 mb-4" data-aos="fade-up">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0"><i class="bi bi-patch-check me-2 text-mk"></i>Verifikasi Perusahaan</h6>
                    <a href="{{ route('admin.verifikasi.index') }}" class="small text-mk fw-semibold text-decoration-none">Kelola</a>
                </div>
                @if($verifikasiTerbaru->count())
                    @foreach($verifikasiTerbaru as $perusahaan)
                        <div class="d-flex justify-content-between align-items-center py-2 {{ !$loop->last ? 'border-bottom' : '' }}" style="border-color:var(--mk-border) !important;">
                            <div class="min-w-0">
                                <div class="fw-semibold small text-truncate">{{ $perusahaan->nama_perusahaan }}</div>
                                <div class="small text-muted-2">{{ $perusahaan->created_at->diffForHumans() }}</div>
                            </div>
                            <span class="badge rounded-pill text-bg-warning">Menunggu</span>
                        </div>
                    @endforeach
                @else
                    <p class="text-muted-2 small text-center py-3 mb-0">Tidak ada yang menunggu verifikasi. ✅</p>
                @endif
                <div class="mt-3 small text-muted-2">
                    Terverifikasi: <strong class="text-success">{{ $stats['perusahaanTerverifikasi'] }}</strong> dari {{ $stats['perusahaan'] }} perusahaan
                </div>
            </div>

            <div class="card card-hover p-4" data-aos="fade-up">
                <h6 class="fw-bold mb-3"><i class="bi bi-activity me-2 text-mk"></i>Aktivitas Terbaru</h6>
                @forelse($aktivitasTerbaru as $log)
                    <div class="small py-2 {{ !$loop->last ? 'border-bottom' : '' }}" style="border-color:var(--mk-border) !important;">
                        <div class="fw-semibold small text-truncate">{{ $log->aksi }}</div>
                        <div class="text-muted-2 text-truncate" style="font-size:.72rem;">
                            {{ $log->user?->name ?? 'Sistem' }} • {{ $log->created_at->diffForHumans() }}
                        </div>
                    </div>
                @empty
                    <p class="text-muted-2 small text-center py-3 mb-0">Belum ada aktivitas.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Analytics dalam --}}
    <div class="row g-4 mt-1">
        {{-- Top lowongan paling dilirik --}}
        <div class="col-lg-4">
            <div class="card card-hover p-4 h-100" data-aos="fade-up">
                <h6 class="fw-bold mb-3"><i class="bi bi-fire me-2 text-mk"></i>Top Lowongan Paling Dilirik</h6>
                @forelse($topLowongan as $i => $lowongan)
                    <div class="d-flex align-items-center gap-3 py-2 {{ !$loop->last ? 'border-bottom' : '' }}" style="border-color:var(--mk-border) !important;">
                        <div class="fw-bold fs-5 {{ $i === 0 ? 'text-warning' : 'text-muted-2' }}">#{{ $i + 1 }}</div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="fw-semibold small text-truncate">{{ $lowongan->posisi }}</div>
                            <div class="small text-muted-2 text-truncate">{{ $lowongan->perusahaan->nama_perusahaan }}</div>
                        </div>
                        <span class="badge rounded-pill text-bg-info"><i class="bi bi-eye me-1"></i>{{ number_format($lowongan->views) }}</span>
                    </div>
                @empty
                    <p class="text-muted-2 small text-center py-3 mb-0">Belum ada data.</p>
                @endforelse
            </div>
        </div>

        {{-- Lowongan per kategori --}}
        <div class="col-lg-4">
            <div class="card card-hover p-4 h-100" data-aos="fade-up" data-aos-delay="60">
                <h6 class="fw-bold mb-3"><i class="bi bi-tags me-2 text-mk"></i>Lowongan per Kategori</h6>
                @php $maxKat = max($lowonganPerKategori->max('lowongans_count') ?? 0, 1); @endphp
                @forelse($lowonganPerKategori as $kategori)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small fw-semibold"><i class="bi {{ $kategori->icon ?: 'bi-folder' }} me-1 text-mk"></i>{{ $kategori->nama }}</span>
                            <span class="small fw-bold text-accent">{{ $kategori->lowongans_count }}</span>
                        </div>
                        <div class="progress" style="height:7px;">
                            <div class="progress-bar bg-mk" style="width:{{ max(4, round(($kategori->lowongans_count / $maxKat) * 100)) }}%;"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted-2 small text-center py-3 mb-0">Belum ada kategori.</p>
                @endforelse
            </div>
        </div>

        {{-- Skill paling dicari --}}
        <div class="col-lg-4">
            <div class="card card-hover p-4 h-100" data-aos="fade-up" data-aos-delay="120">
                <h6 class="fw-bold mb-3"><i class="bi bi-lightning-charge me-2 text-mk"></i>Skill Paling Dicari</h6>
                <div class="d-flex flex-wrap gap-2">
                    @forelse($skillTerpopuler as $skill)
                        <span class="badge rounded-pill border px-3 py-2" style="background:var(--mk-soft);color:var(--mk-primary);">
                            {{ $skill->nama }} <span class="text-muted-2">× {{ $skill->lowongans_count }}</span>
                        </span>
                    @empty
                        <p class="text-muted-2 small text-center py-3 mb-0 w-100">Belum ada data.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
