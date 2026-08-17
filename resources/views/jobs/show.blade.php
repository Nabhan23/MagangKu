@extends('layouts.guest')

@section('title', $lowongan->posisi . ' - MagangKu')
@section('meta_description', Str::limit(strip_tags($lowongan->deskripsi), 160))
@section('og_title', $lowongan->posisi . ' di ' . $lowongan->perusahaan->nama_perusahaan)
@section('og_description', 'Lowongan ' . $lowongan->posisi . ' di ' . $lowongan->perusahaan->nama_perusahaan . ' (' . $lowongan->lokasi . '). Lamar sekarang di MagangKu!')
@section('og_image', asset($lowongan->perusahaan->logo))
@section('og_type', 'website')
@section('canonical', route('jobs.show', $lowongan))

@push('head')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'JobPosting',
    'title' => $lowongan->posisi,
    'description' => Str::limit(strip_tags($lowongan->deskripsi . "\n\n" . ($lowongan->persyaratan ?? '')), 1500),
    'datePosted' => $lowongan->created_at->toIso8601String(),
    'validThrough' => $lowongan->deadline->toIso8601String(),
    'employmentType' => match ($lowongan->jenis) {
        'magang' => 'INTERN',
        'part_time' => 'PART_TIME',
        'freelance' => 'CONTRACTOR',
        default => 'FULL_TIME',
    },
    'hiringOrganization' => [
        '@type' => 'Organization',
        'name' => $lowongan->perusahaan->nama_perusahaan,
        'sameAs' => $lowongan->perusahaan->website,
        'logo' => $lowongan->perusahaan->logo_url ?: url('/img/pwa/icon-512.png'),
    ],
    'jobLocation' => [
        '@type' => 'Place',
        'address' => [
            '@type' => 'PostalAddress',
            'addressLocality' => $lowongan->lokasi,
            'streetAddress' => $lowongan->perusahaan->alamat_lengkap,
        ],
    ],
    'baseSalary' => [
        '@type' => 'MonetaryAmount',
        'currency' => 'IDR',
        'value' => [
            '@type' => 'QuantitativeValue',
            'minValue' => $lowongan->gaji_min,
            'maxValue' => $lowongan->gaji_max,
            'unitText' => 'MONTH',
        ],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush

@section('content')
    <section class="section" style="padding-top:3rem;">
        <div class="container">
            <nav aria-label="breadcrumb" class="mb-4" data-aos="fade-up">
                <ol class="breadcrumb small mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-mk text-decoration-none">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('jobs.index') }}" class="text-mk text-decoration-none">Lowongan</a></li>
                    <li class="breadcrumb-item active text-muted-2">{{ $lowongan->posisi }}</li>
                </ol>
            </nav>

            <div class="row g-4">
                <div class="col-lg-8">
                    {{-- Header lowongan --}}
                    <div class="card card-hover p-4 mb-4" data-aos="fade-up">
                        <div class="d-flex flex-wrap align-items-start gap-3">
                            @php
                                $colors = ['#4FC3F7', '#26A69A', '#5C6BC0', '#FFA726', '#EC407A', '#66BB6A'];
                                $initial = strtoupper(substr($lowongan->perusahaan->nama_perusahaan, 0, 1));
                            @endphp
                            <div class="company-logo-lg" style="background:{{ $colors[$lowongan->perusahaan_id % count($colors)] }};">
                                @if($lowongan->perusahaan->logo_url)
                                    <img src="{{ $lowongan->perusahaan->logo_url }}" alt="logo" width="88" height="88" style="object-fit:cover;border-radius:1.25rem;">
                                @else
                                    {{ $initial }}
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <h3 class="fw-bold mb-1">{{ $lowongan->posisi }}</h3>
                                <a href="{{ route('companies.show', $lowongan->perusahaan) }}" class="text-mk text-decoration-none fw-semibold">
                                    {{ $lowongan->perusahaan->nama_perusahaan }}
                                    @if($lowongan->perusahaan->status_verifikasi === 'verified')
                                        <i class="bi bi-patch-check-fill" title="Terverifikasi"></i>
                                    @endif
                                </a>
                                <div class="d-flex flex-wrap gap-2 mt-2">
                                    <span class="job-type" style="background:rgba(79,195,247,.12);color:var(--mk-primary);">{{ $lowongan->jenis_label }}</span>
                                    <span class="job-type" style="background:rgba(30,58,95,.08);color:var(--mk-accent);"><i class="bi bi-geo-alt me-1"></i>{{ $lowongan->lokasi ?: 'Remote' }}</span>
                                    @if($lowongan->is_remote)
                                        <span class="job-type" style="background:rgba(34,197,94,.12);color:#16A34A;"><i class="bi bi-wifi me-1"></i>Remote</span>
                                    @endif
                                    <span class="job-type" style="background:rgba(245,158,11,.12);color:#D97706;"><i class="bi bi-cash-stack me-1"></i>{{ $lowongan->gaji_label }}</span>
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="small text-muted-2 mb-2">
                                    @if($lowongan->deadline < now()->toDateString())
                                        <span class="text-danger fw-semibold"><i class="bi bi-x-circle me-1"></i>Lowongan Ditutup</span>
                                    @else
                                        <i class="bi bi-clock me-1 text-mk"></i>Deadline {{ $lowongan->deadline->translatedFormat('d F Y') }}
                                    @endif
                                </div>
                                @auth
                                    <button class="btn btn-outline-mk btn-sm" id="bookmarkBtn" data-save-url="{{ route('pelamar.saved.toggle', $lowongan) }}">
                                        <i class="bi {{ $disimpan ? 'bi-bookmark-fill' : 'bi-bookmark' }} me-1"></i><span>{{ $disimpan ? 'Tersimpan' : 'Simpan' }}</span>
                                    </button>
                                @endauth
                            </div>
                        </div>

                        <hr>
                        <div class="row text-center g-2 small">
                            <div class="col-6 col-md-3">
                                <div class="text-muted-2">Kuota</div>
                                <div class="fw-bold">{{ $lowongan->kuota }} orang</div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="text-muted-2">Pelamar</div>
                                <div class="fw-bold">{{ $lowongan->lamarans_count ?? $lowongan->lamarans()->count() }}</div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="text-muted-2">Dilihat</div>
                                <div class="fw-bold">{{ number_format($lowongan->views) }}</div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="text-muted-2">Dibuat</div>
                                <div class="fw-bold">{{ $lowongan->created_at->translatedFormat('d M Y') }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Deskripsi --}}
                    <div class="card card-hover p-4 mb-4" data-aos="fade-up">
                        <h5 class="fw-bold mb-3"><i class="bi bi-file-text me-2 text-mk"></i>Deskripsi Pekerjaan</h5>
                        <div class="text-muted-2 content-html">{!! nl2br(e($lowongan->deskripsi)) !!}</div>

                        @if($lowongan->persyaratan)
                            <h5 class="fw-bold mt-4 mb-3"><i class="bi bi-list-check me-2 text-mk"></i>Persyaratan</h5>
                            <div class="text-muted-2 content-html">{!! nl2br(e($lowongan->persyaratan)) !!}</div>
                        @endif

                        @if($lowongan->skills->count())
                            <h5 class="fw-bold mt-4 mb-3"><i class="bi bi-lightning-charge me-2 text-mk"></i>Skill yang Dibutuhkan</h5>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($lowongan->skills as $skill)
                                    <span class="chip" style="cursor:default;">{{ $skill->nama }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Tombol lamar --}}
                    @if($lowongan->status === 'aktif' && $lowongan->deadline >= now()->toDateString())
                        <div class="card p-4 text-center bg-mk-gradient-dark border-0 text-white" data-aos="fade-up">
                            <h5 class="fw-bold mb-2">Tertarik dengan posisi ini?</h5>
                            <p class="mb-3" style="color:rgba(255,255,255,.9);">Jangan lewatkan kesempatanmu! Lamar sekarang sebelum deadline.</p>
                            @auth
                                @if(auth()->user()->isPelamar())
                                    @if($sudahDilamar)
                                        <div>
                                            <span class="badge rounded-pill text-bg-light fs-6 py-2 px-4"><i class="bi bi-check-circle-fill text-success me-1"></i>Anda sudah melamar lowongan ini</span>
                                            <a href="{{ route('pelamar.lamaran.index') }}" class="btn btn-light fw-bold mt-2 d-block mx-auto" style="max-width:280px;">Tracking Lamaran</a>
                                        </div>
                                    @else
                                        <button class="btn btn-light btn-lg fw-bold px-5 mx-auto" style="max-width:320px;" data-bs-toggle="modal" data-bs-target="#applyModal">
                                            <i class="bi bi-send-fill me-2"></i>Lamar Sekarang
                                        </button>
                                    @endif
                                @else
                                    <a href="{{ route('register') }}" class="btn btn-light btn-lg fw-bold px-5">Daftar sebagai Pelamar</a>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="btn btn-light btn-lg fw-bold px-5 mx-auto" style="max-width:320px;">Login untuk Melamar</a>
                            @endauth
                        </div>
                    @else
                        <div class="card p-4 text-center border-0 bg-soft">
                            <i class="bi bi-x-circle fs-1 text-danger mb-2"></i>
                            <h5 class="fw-bold text-danger">Lowongan Telah Ditutup</h5>
                            <a href="{{ route('jobs.index') }}" class="btn btn-mk mx-auto px-4 mt-2">Cari Lowongan Lain</a>
                        </div>
                    @endif
                </div>

                {{-- Sidebar --}}
                <div class="col-lg-4">
                    @if($lowongan->perusahaan->status_verifikasi === 'verified')
                        <div class="card p-4 mb-4 text-center border-0 bg-soft" data-aos="fade-left">
                            <i class="bi bi-patch-check-fill fs-1 text-mk"></i>
                            <h6 class="fw-bold mt-2 mb-0">Perusahaan Terverifikasi</h6>
                            <p class="small text-muted-2 mb-0">Lowongan ini dipastikan aman dan terpercaya.</p>
                        </div>
                    @endif

                    <div class="card card-hover p-4 mb-4" data-aos="fade-left">
                        <h6 class="fw-bold mb-3"><i class="bi bi-buildings me-2 text-mk"></i>Tentang Perusahaan</h6>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="company-logo-lg" style="width:56px;height:56px;font-size:1.3rem;border-radius:1rem;background:{{ $colors[$lowongan->perusahaan_id % count($colors)] }};">
                                @if($lowongan->perusahaan->logo_url)
                                    <img src="{{ $lowongan->perusahaan->logo_url }}" width="56" height="56" style="object-fit:cover;border-radius:1rem;">
                                @else
                                    {{ $initial }}
                                @endif
                            </div>
                            <div>
                                <div class="fw-bold">{{ $lowongan->perusahaan->nama_perusahaan }}</div>
                                <div class="small text-muted-2">{{ $lowongan->perusahaan->industri ?: '-' }} • {{ $lowongan->perusahaan->lokasi ?: '-' }}</div>
                            </div>
                        </div>
                        <p class="small text-muted-2">{{ Str::limit($lowongan->perusahaan->deskripsi ?: 'Perusahaan terpercaya di Indonesia.', 130) }}</p>
                        <a href="{{ route('companies.show', $lowongan->perusahaan) }}" class="btn btn-soft btn-sm w-100">Lihat Profil Perusahaan</a>
                    </div>

                    <div class="card card-hover p-4" data-aos="fade-left">
                        <h6 class="fw-bold mb-3"><i class="bi bi-info-circle me-2 text-mk"></i>Ringkasan Lowongan</h6>
                        <ul class="list-unstyled small d-grid gap-2 mb-0 text-muted-2">
                            <li><i class="bi bi-tag me-2 text-mk"></i>{{ $lowongan->category?->nama ?? 'Umum' }}</li>
                            <li><i class="bi bi-briefcase me-2 text-mk"></i>{{ $lowongan->jenis_label }}</li>
                            <li><i class="bi bi-geo-alt me-2 text-mk"></i>{{ $lowongan->lokasi ?: 'Remote' }}</li>
                            <li><i class="bi bi-cash-stack me-2 text-mk"></i>{{ $lowongan->gaji_label }}</li>
                            <li><i class="bi bi-clock me-2 text-mk"></i>Deadline {{ $lowongan->deadline->translatedFormat('d M Y') }}</li>
                            <li><i class="bi bi-people me-2 text-mk"></i>Kuota {{ $lowongan->kuota }} orang</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Lowongan serupa --}}
            @if($related->count())
                <div class="mt-5">
                    <h5 class="fw-bold mb-4" data-aos="fade-up">Lowongan Serupa</h5>
                    <div class="row g-4">
                        @foreach($related as $job)
                            <div class="col-md-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 80 }}">
                                <x-job-card :job="$job"/>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>

    {{-- Modal Lamar --}}
    @auth
        @if(auth()->user()->isPelamar() && !$sudahDilamar)
            <div class="modal fade" id="applyModal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content p-2">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title fw-bold"><i class="bi bi-send-fill text-mk me-2"></i>Lamar Posisi</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form id="applyForm" action="{{ route('pelamar.lamaran.apply', $lowongan) }}" class="modal-body pt-2">
                            @csrf
                            <div class="alert alert-info border-0 rounded-4 small mb-3">
                                <i class="bi bi-info-circle-fill me-1"></i>
                                CV Anda (<span class="fw-semibold">{{ auth()->user()->pelamarProfile->cv_path ? 'Terlampir ✓' : 'BELUM DIUNGGAH' }}</span>) akan otomatis dikirim.
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Catatan untuk Perusahaan <span class="text-muted-2 fw-normal">(opsional)</span></label>
                                <textarea name="catatan" rows="3" class="form-control" placeholder="Ceritakan mengapa Anda cocok untuk posisi ini..."></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">File Pendukung <span class="text-muted-2 fw-normal">(opsional, PDF/Word/ZIP max 5MB)</span></label>
                                <input type="file" name="file_pendukung" class="form-control" accept=".pdf,.doc,.docx,.zip">
                            </div>
                            <button type="submit" class="btn btn-mk w-100 btn-lg" id="applyBtn">
                                <i class="bi bi-send me-1"></i>Kirim Lamaran
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endauth
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Bookmark
        document.getElementById('bookmarkBtn')?.addEventListener('click', async (e) => {
            const btn = e.currentTarget;
            try {
                const data = await mkFetch(btn.dataset.saveUrl, { method: 'POST' });
                btn.querySelector('i').className = data.saved ? 'bi bi-bookmark-fill me-1' : 'bi bi-bookmark me-1';
                btn.querySelector('span').textContent = data.saved ? 'Tersimpan' : 'Simpan';
                btn.classList.toggle('btn-mk', data.saved);
                btn.classList.toggle('btn-outline-mk', !data.saved);
                mkToast(data.saved ? 'Lowongan disimpan' : 'Dihapus dari simpanan', data.saved ? 'success' : 'info');
            } catch (err) {
                mkToast(err.message, 'error');
            }
        });

        // Apply
        document.getElementById('applyForm')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const form = e.currentTarget;
            const btn = document.getElementById('applyBtn');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengirim...';
            try {
                const data = await mkFetch(form.action, { method: 'POST', body: new FormData(form) });
                mkToast(data.message, 'success');
                const modal = bootstrap.Modal.getInstance(document.getElementById('applyModal'));
                modal?.hide();
                setTimeout(() => window.location.href = data.url, 1200);
            } catch (err) {
                mkToast(err.message, 'error');
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-send me-1"></i>Kirim Lamaran';
            }
        });
    });
</script>
@endpush
