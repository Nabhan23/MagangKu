@extends('layouts.app')

@section('title', 'Dashboard HRD - MagangKu')
@section('page-title', 'Dashboard HRD')

@section('content')
    {{-- Status verifikasi --}}
    @php
        $status = $perusahaan->status_verifikasi;
    @endphp
    @if($status === 'menunggu')
        <div class="card border-0 rounded-4 mb-4 overflow-hidden" data-aos="fade-up" style="background:linear-gradient(135deg,#FEF3C7,#FDE68A);">
            <div class="p-4 d-flex flex-wrap align-items-center gap-3">
                <div class="stat-icon" style="background:#F59E0B1A;color:#B45309;width:56px;height:56px;font-size:1.4rem;"><i class="bi bi-hourglass-split"></i></div>
                <div class="flex-grow-1 min-w-0">
                    <h6 class="fw-bold mb-1" style="color:#92400E;">Perusahaan Anda Sedang Menunggu Verifikasi</h6>
                    <p class="small mb-0" style="color:#78350F;">Unggah dokumen legal perusahaan (Akta, NIB, atau SIUP) untuk mempercepat proses verifikasi oleh admin. Setelah terverifikasi, Anda bisa memposting lowongan dan lebih dipercaya pelamar.</p>
                    <div class="d-flex flex-wrap gap-2 mt-2">
                        <span class="badge rounded-pill text-bg-success"><i class="bi bi-check-lg me-1"></i>Akun dibuat</span>
                        <span class="badge rounded-pill text-bg-light border"><i class="bi bi-cloud-arrow-up me-1"></i>Kirim dokumen</span>
                        <span class="badge rounded-pill text-bg-light border"><i class="bi bi-patch-check me-1"></i>Verifikasi admin</span>
                    </div>
                </div>
                <a href="{{ route('perusahaan.profile.edit') }}" class="btn btn-warning btn-sm fw-semibold"><i class="bi bi-cloud-arrow-up me-1"></i>Upload Dokumen</a>
            </div>
        </div>
    @elseif($status === 'rejected')
        <div class="alert alert-danger border-0 rounded-4 d-flex align-items-center justify-content-between flex-wrap gap-2" data-aos="fade-up">
            <div>
                <i class="bi bi-x-circle me-1"></i>
                <strong>Verifikasi perusahaan ditolak.</strong>
                <span class="small d-block">{{ $perusahaan->pesan_verifikasi ?: 'Silakan perbaiki dokumen dan kirim ulang, atau hubungi admin.' }}</span>
            </div>
            <a href="{{ route('perusahaan.profile.edit') }}" class="btn btn-danger btn-sm fw-semibold"><i class="bi bi-cloud-arrow-up me-1"></i>Kirim Ulang Dokumen</a>
        </div>
    @elseif($status === 'verified')
        <div class="alert alert-success border-0 rounded-4 d-flex align-items-center gap-2" data-aos="fade-up">
            <i class="bi bi-patch-check-fill fs-5"></i>
            <div>
                <strong>Perusahaan Anda terverifikasi.</strong>
                <span class="small d-block">Anda kini bisa memposting lowongan dan lebih dipercaya pelamar.</span>
            </div>
        </div>
    @endif

    {{-- Statistik --}}
    <div class="row g-3 mb-4">
        @foreach([
            ['icon' => 'bi-briefcase-fill', 'color' => '#4FC3F7', 'label' => 'Total Lowongan', 'value' => $stats['totalLowongan'], 'link' => route('perusahaan.lowongan.index')],
            ['icon' => 'bi-lightning-charge-fill', 'color' => '#22C55E', 'label' => 'Lowongan Aktif', 'value' => $stats['lowonganAktif'], 'link' => route('perusahaan.lowongan.index')],
            ['icon' => 'bi-people-fill', 'color' => '#F59E0B', 'label' => 'Total Pelamar', 'value' => $stats['totalPelamar'], 'link' => route('perusahaan.pelamar.index')],
            ['icon' => 'bi-camera-video-fill', 'color' => '#8B5CF6', 'label' => 'Interview Terjadwal', 'value' => $stats['interview'], 'link' => route('perusahaan.pelamar.index')],
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

    <div class="row g-4">
        {{-- Grafik --}}
        <div class="col-lg-8">
            <div class="card card-hover p-4 mb-4" data-aos="fade-up">
                <h6 class="fw-bold mb-4"><i class="bi bi-graph-up me-2 text-mk"></i>Pelamar per Bulan (6 Bulan Terakhir)</h6>
                <div class="d-flex align-items-end gap-2" style="height:220px;">
                    @foreach($bulan as $b)
                        @php
                            $max = max($bulan->max('jumlah'), 1);
                            $height = max(6, round(($b['jumlah'] / $max) * 100));
                        @endphp
                        <div class="flex-grow-1 d-flex flex-column align-items-center gap-2">
                            <div class="fw-bold small text-accent">{{ $b['jumlah'] }}</div>
                            <div class="w-100 rounded-3" style="height:{{ $height }}%;background:var(--mk-gradient);min-height:8px;" title="{{ $b['bulan'] }}: {{ $b['jumlah'] }} pelamar"></div>
                            <div class="small text-muted-2">{{ $b['bulan'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="card card-hover p-4 mb-4" data-aos="fade-up">
                <h6 class="fw-bold mb-4"><i class="bi bi-bar-chart me-2 text-mk"></i>Lamaran per Lowongan (Top 5)</h6>
                @php $maxLowongan = max($lamaranPerLowongan->max('lamarans_count') ?? 0, 1); @endphp
                @forelse($lamaranPerLowongan as $item)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div class="small fw-semibold text-truncate" style="max-width:70%;">{{ $item->posisi }}</div>
                            <span class="small fw-bold text-accent">{{ $item->lamarans_count }}</span>
                        </div>
                        <div class="progress" style="height:8px;">
                            <div class="progress-bar bg-mk" style="width:{{ max(4, round(($item->lamarans_count / $maxLowongan) * 100)) }}%;"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted-2 small text-center py-3 mb-0">Belum ada data lamaran.</p>
                @endforelse
            </div>

            <div class="card card-hover p-4" data-aos="fade-up">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0"><i class="bi bi-clock-history me-2 text-mk"></i>Lamaran Terbaru</h6>
                    <a href="{{ route('perusahaan.pelamar.index') }}" class="small text-mk fw-semibold text-decoration-none">Lihat Semua</a>
                </div>
                @forelse($lamaranTerbaru as $lamaran)
                    <div class="d-flex align-items-center justify-content-between gap-3 py-3 {{ !$loop->last ? 'border-bottom' : '' }}" style="border-color:var(--mk-border) !important;">
                        <div class="d-flex align-items-center gap-3 min-w-0">
                            @if($lamaran->pelamar->foto_url)
                                <img src="{{ $lamaran->pelamar->foto_url }}" class="rounded-circle" width="40" height="40" style="object-fit:cover;">
                            @else
                                <div class="avatar avatar-sm" style="background:var(--mk-gradient);">{{ strtoupper(substr($lamaran->pelamar->nama ?? '?', 0, 1)) }}</div>
                            @endif
                            <div class="min-w-0">
                                <div class="fw-semibold small text-truncate">{{ $lamaran->pelamar->nama }}</div>
                                <div class="small text-muted-2 text-truncate">{{ $lamaran->lowongan->posisi }}</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge rounded-pill text-bg-{{ $lamaran->status_color }}">{{ $lamaran->status_label }}</span>
                            <a href="{{ route('perusahaan.pelamar.show', $lamaran) }}" class="btn btn-soft btn-sm">Detail</a>
                        </div>
                    </div>
                @empty
                    <p class="text-muted-2 small text-center py-3 mb-0">Belum ada lamaran masuk.</p>
                @endforelse
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card card-hover p-4 mb-4" data-aos="fade-up">
                <h6 class="fw-bold mb-3"><i class="bi bi-pie-chart me-2 text-mk"></i>Distribusi Status Lamaran</h6>
                @php $totalStatus = max($distribusiStatus->sum(), 1); $colors = ['secondary', 'info', 'warning', 'primary', 'success', 'danger']; @endphp
                @foreach($distribusiStatus as $key => $count)
                    <div class="d-flex align-items-center gap-2 py-1">
                        <span class="badge rounded-pill text-bg-{{ $colors[$loop->index] }} d-inline-flex align-items-center gap-1" style="width:96px;">
                            <i class="bi bi-circle-fill" style="font-size:.4rem;"></i>{{ \App\Models\Lamaran::STATUS[$key][0] }}
                        </span>
                        <div class="progress flex-grow-1" style="height:6px;">
                            <div class="progress-bar bg-{{ $colors[$loop->index] }}" style="width:{{ round(($count / $totalStatus) * 100) }}%;"></div>
                        </div>
                        <span class="small fw-bold text-accent" style="width:28px;text-align:right;">{{ $count }}</span>
                    </div>
                @endforeach
                <hr style="border-color:var(--mk-border);">
                <div class="d-flex justify-content-between small">
                    <span class="text-muted-2">Rata-rata pelamar / lowongan</span>
                    <b class="text-accent">{{ $rataPelamar }}</b>
                </div>
            </div>

            <div class="card card-hover p-4 mb-4" data-aos="fade-up">
                <h6 class="fw-bold mb-3"><i class="bi bi-megaphone me-2 text-mk"></i>Aksi Cepat</h6>
                <div class="d-grid gap-2">
                    <a href="{{ route('perusahaan.lowongan.create') }}" class="btn btn-mk"><i class="bi bi-plus-circle me-1"></i>Posting Lowongan Baru</a>
                    <a href="{{ route('perusahaan.pelamar.index') }}" class="btn btn-soft"><i class="bi bi-people me-1"></i>Kelola Pelamar</a>
                    <a href="{{ route('perusahaan.profile.edit') }}" class="btn btn-soft"><i class="bi bi-building me-1"></i>Edit Profil Perusahaan</a>
                </div>
            </div>

            <div class="card card-hover p-4" data-aos="fade-up">
                <h6 class="fw-bold mb-3"><i class="bi bi-patch-check me-2 text-mk"></i>Status Verifikasi</h6>
                <div class="text-center py-2">
                    <span class="badge rounded-pill text-bg-{{ $perusahaan->status_verifikasi_badge[0] }} px-3 py-2">
                        <i class="bi {{ $status === 'verified' ? 'bi-patch-check-fill' : ($status === 'menunggu' ? 'bi-hourglass-split' : 'bi-x-circle') }} me-1"></i>
                        {{ $perusahaan->status_verifikasi_badge[1] }}
                    </span>
                    <p class="small text-muted-2 mb-3 mt-3">
                        @if($status === 'verified')
                            Perusahaan Anda terverifikasi. Lowongan Anda lebih dipercaya pelamar.
                        @elseif($status === 'menunggu')
                            Dokumen sedang ditinjau oleh admin.
                        @else
                            {{ $perusahaan->pesan_verifikasi ?: 'Hubungi admin untuk info lebih lanjut.' }}
                        @endif
                    </p>
                    @if($status !== 'verified')
                        <a href="{{ route('perusahaan.profile.edit') }}" class="btn btn-soft btn-sm w-100"><i class="bi bi-cloud-arrow-up me-1"></i>{{ $status === 'menunggu' ? 'Upload / Perbarui Dokumen' : 'Kirim Ulang Dokumen' }}</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
