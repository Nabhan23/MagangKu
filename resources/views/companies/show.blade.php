@extends('layouts.guest')

@section('title', $perusahaan->nama_perusahaan . ' - MagangKu')
@section('meta_description', Str::limit(strip_tags($perusahaan->deskripsi), 160))
@section('og_title', $perusahaan->nama_perusahaan)
@section('og_description', $perusahaan->industri . ' • ' . $perusahaan->lokasi . ' — ' . Str::limit(strip_tags($perusahaan->deskripsi), 140))
@section('og_image', asset($perusahaan->logo))
@section('canonical', route('companies.show', $perusahaan))
@section('meta_description', Str::limit($perusahaan->deskripsi ?: 'Profil perusahaan di MagangKu.', 160))

@push('head')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Organization',
    'name' => $perusahaan->nama_perusahaan,
    'description' => Str::limit(strip_tags($perusahaan->deskripsi ?? ''), 300),
    'url' => route('companies.show', $perusahaan),
    'logo' => $perusahaan->logo_url ?: url('/img/pwa/icon-512.png'),
    'address' => [
        '@type' => 'PostalAddress',
        'addressLocality' => $perusahaan->lokasi,
        'streetAddress' => $perusahaan->alamat_lengkap,
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
                    <li class="breadcrumb-item"><a href="{{ route('companies.index') }}" class="text-mk text-decoration-none">Perusahaan</a></li>
                    <li class="breadcrumb-item active text-muted-2">{{ $perusahaan->nama_perusahaan }}</li>
                </ol>
            </nav>

            {{-- Header --}}
            <div class="card card-hover p-4 mb-4" data-aos="fade-up">
                @php
                    $colors = ['#4FC3F7', '#26A69A', '#5C6BC0', '#FFA726', '#EC407A', '#66BB6A', '#AB47BC', '#8D6E63'];
                    $initial = strtoupper(substr($perusahaan->nama_perusahaan, 0, 1));
                @endphp
                <div class="d-flex flex-wrap align-items-center gap-4">
                    <div class="company-logo-lg" style="background:{{ $colors[$perusahaan->id % count($colors)] }};">
                        @if($perusahaan->logo_url)
                            <img src="{{ $perusahaan->logo_url }}" width="88" height="88" style="object-fit:cover;border-radius:1.25rem;">
                        @else
                            {{ $initial }}
                        @endif
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <h3 class="fw-bold mb-0">{{ $perusahaan->nama_perusahaan }}</h3>
                            <span class="badge rounded-pill text-bg-success"><i class="bi bi-patch-check-fill me-1"></i>Terverifikasi</span>
                        </div>
                        <div class="text-muted-2 small mt-1 d-flex flex-wrap gap-3">
                            <span><i class="bi bi-tag me-1 text-mk"></i>{{ $perusahaan->industri ?: '-' }}</span>
                            <span><i class="bi bi-geo-alt me-1 text-mk"></i>{{ $perusahaan->lokasi ?: '-' }}</span>
                            <span><i class="bi bi-people me-1 text-mk"></i>{{ $perusahaan->ukuran_perusahaan ?: '-' }}</span>
                            <span><i class="bi bi-calendar me-1 text-mk"></i>Berdiri {{ $perusahaan->tahun_berdiri ?: '-' }}</span>
                        </div>
                        <div class="rating mt-2">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi {{ $perusahaan->rating >= $i ? 'bi-star-fill' : 'bi-star' }}"></i>
                            @endfor
                            <span class="text-muted-2 small ms-1">{{ $perusahaan->rating }} ({{ $perusahaan->reviews->count() }} ulasan)</span>
                        </div>
                    </div>
                    <div class="d-flex flex-column gap-2">
                        <button type="button" class="btn {{ $perusahaan->is_followed ? 'btn-mk' : 'btn-soft' }} btn-sm" id="followBtn" data-url="{{ route('companies.follow', $perusahaan) }}">
                            <i class="bi {{ $perusahaan->is_followed ? 'bi-star-fill' : 'bi-star' }} me-1"></i>
                            <span id="followText">{{ $perusahaan->is_followed ? 'Mengikuti' : 'Ikuti Perusahaan' }}</span>
                            <span class="badge rounded-pill text-bg-light ms-1" id="followCount">{{ $perusahaan->followers_count }}</span>
                        </button>
                        @if($perusahaan->website)
                            <a href="{{ $perusahaan->website }}" target="_blank" class="btn btn-outline-mk btn-sm"><i class="bi bi-globe me-1"></i>Website</a>
                        @endif
                        <a href="{{ route('jobs.index', ['q' => $perusahaan->nama_perusahaan]) }}" class="btn btn-mk btn-sm">
                            <i class="bi bi-briefcase me-1"></i>{{ $perusahaan->lowongans->count() }} Lowongan
                        </a>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-8">
                    {{-- Deskripsi --}}
                    <div class="card card-hover p-4 mb-4" data-aos="fade-up">
                        <h5 class="fw-bold mb-3"><i class="bi bi-buildings me-2 text-mk"></i>Tentang Perusahaan</h5>
                        <p class="text-muted-2 mb-0" style="white-space:pre-line;">{{ $perusahaan->deskripsi ?: 'Belum ada deskripsi.' }}</p>
                    </div>

                    {{-- Lowongan --}}
                    <div class="card card-hover p-4 mb-4" data-aos="fade-up">
                        <h5 class="fw-bold mb-3"><i class="bi bi-briefcase me-2 text-mk"></i>Lowongan Terbuka ({{ $perusahaan->lowongans->count() }})</h5>
                        @forelse($perusahaan->lowongans as $job)
                            <div class="d-flex align-items-center justify-content-between gap-3 py-3 {{ !$loop->last ? 'border-bottom' : '' }}" style="border-color:var(--mk-border) !important;">
                                <div>
                                    <a href="{{ route('jobs.show', $job) }}" class="fw-semibold text-accent text-decoration-none">{{ $job->posisi }}</a>
                                    <div class="small text-muted-2">
                                        <span class="me-2">{{ $job->jenis_label }}</span>
                                        <span class="me-2"><i class="bi bi-geo-alt me-1"></i>{{ $job->lokasi ?: 'Remote' }}</span>
                                        <span><i class="bi bi-cash-stack me-1"></i>{{ $job->gaji_label }}</span>
                                    </div>
                                </div>
                                <a href="{{ route('jobs.show', $job) }}" class="btn btn-soft btn-sm text-nowrap">Detail</a>
                            </div>
                        @empty
                            <p class="text-muted-2 small mb-0">Belum ada lowongan terbuka saat ini.</p>
                        @endforelse
                    </div>
                </div>

                <div class="col-lg-4">
                    {{-- Info --}}
                    <div class="card card-hover p-4 mb-4" data-aos="fade-left">
                        <h6 class="fw-bold mb-3"><i class="bi bi-info-circle me-2 text-mk"></i>Informasi Kontak</h6>
                        <ul class="list-unstyled small d-grid gap-2 mb-0 text-muted-2">
                            @if($perusahaan->email_kontak)
                                <li><i class="bi bi-envelope me-2 text-mk"></i>{{ $perusahaan->email_kontak }}</li>
                            @endif
                            @if($perusahaan->telepon)
                                <li><i class="bi bi-telephone me-2 text-mk"></i>{{ $perusahaan->telepon }}</li>
                            @endif
                            @if($perusahaan->website)
                                <li><i class="bi bi-globe me-2 text-mk"></i>{{ str_replace(['https://','http://'], '', $perusahaan->website) }}</li>
                            @endif
                            <li>
                                <i class="bi bi-geo-alt me-2 text-mk"></i>
                                @if($perusahaan->alamat_lengkap || $perusahaan->lokasi)
                                    <a href="{{ $perusahaan->maps_url }}" target="_blank" rel="noopener" class="text-decoration-none" title="Buka di Google Maps">
                                        {{ $perusahaan->alamat_lengkap ?: $perusahaan->lokasi }}
                                        <i class="bi bi-box-arrow-up-right small text-mk"></i>
                                    </a>
                                @else
                                    -
                                @endif
                            </li>
                        </ul>
                    </div>

                    {{-- Peta Lokasi --}}
                    @if($perusahaan->alamat_lengkap || $perusahaan->lokasi)
                        <div class="card card-hover p-4 mb-4" data-aos="fade-left">
                            <h6 class="fw-bold mb-3"><i class="bi bi-map me-2 text-mk"></i>Lokasi di Peta</h6>
                            <div class="rounded-3 overflow-hidden" style="border:1px solid var(--mk-border);">
                                <iframe
                                    src="https://maps.google.com/maps?q={{ urlencode($perusahaan->nama_perusahaan . ' ' . ($perusahaan->alamat_lengkap ?: $perusahaan->lokasi)) }}&t=&z=14&ie=UTF8&iwloc=&output=embed"
                                    width="100%" height="220" style="border:0;" loading="lazy"
                                    referrerpolicy="no-referrer-when-downgrade"
                                    title="Peta lokasi {{ $perusahaan->nama_perusahaan }}"></iframe>
                            </div>
                            <a href="{{ $perusahaan->maps_url }}" target="_blank" rel="noopener" class="btn btn-outline-mk btn-sm w-100 mt-2"><i class="bi bi-sign-turn-right me-1"></i>Buka di Google Maps</a>
                        </div>
                    @endif

                    {{-- Form Ulasan --}}
                    <div class="card card-hover p-4 mb-4" data-aos="fade-left">
                        <h6 class="fw-bold mb-3"><i class="bi bi-star me-2 text-mk"></i>Beri Ulasan</h6>

                        @auth
                            @if(auth()->user()->isPelamar())
                                @if($myReview)
                                    <div class="alert alert-success border-0 rounded-4 small">
                                        <div class="fw-semibold mb-1"><i class="bi bi-check-circle-fill me-1"></i>Anda sudah memberi ulasan</div>
                                        <div class="rating small mb-1">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="bi {{ $myReview->rating >= $i ? 'bi-star-fill' : 'bi-star' }}"></i>
                                            @endfor
                                        </div>
                                        <p class="mb-0">"{{ $myReview->komentar }}"</p>
                                    </div>
                                @elseif($reviewEligible)
                                    <p class="small text-muted-2 mb-3">
                                        Bagikan pengalaman Anda magang/bekerja di perusahaan ini untuk membantu pelamar lain. ⭐
                                    </p>
                                    <form id="reviewForm" action="{{ route('companies.review', $perusahaan) }}" class="d-grid gap-3">
                                        @csrf
                                        <div>
                                            <label class="form-label small">Rating Anda</label>
                                            <div class="d-flex gap-1 review-stars" id="reviewStars">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="bi bi-star fs-4 review-star" data-value="{{ $i }}" style="cursor:pointer;color:var(--mk-muted);transition:transform .15s ease;"></i>
                                                @endfor
                                            </div>
                                            <input type="hidden" name="rating" id="ratingInput" value="">
                                        </div>
                                        <div>
                                            <label class="form-label small">Komentar</label>
                                            <textarea name="komentar" rows="3" class="form-control" placeholder="Ceritakan pengalaman Anda di sini..." maxlength="1000" required></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-mk btn-sm" id="reviewBtn"><i class="bi bi-send me-1"></i>Kirim Ulasan</button>
                                    </form>
                                @else
                                    <div class="alert alert-info border-0 rounded-4 small mb-0">
                                        <i class="bi bi-info-circle-fill me-1"></i>
                                        Ulasan hanya dapat diberikan setelah lamaran Anda <strong>lolos ke tahap interview</strong> atau <strong>diterima</strong> di perusahaan ini.
                                    </div>
                                @endif
                            @else
                                <div class="alert alert-secondary border-0 rounded-4 small mb-0">
                                    <i class="bi bi-person-lock me-1"></i>
                                    Hanya akun <strong>Pelamar</strong> yang dapat memberikan ulasan.
                                </div>
                            @endif
                        @else
                            <div class="alert alert-secondary border-0 rounded-4 small mb-0">
                                <i class="bi bi-box-arrow-in-right me-1"></i>
                                <a href="{{ route('login') }}" class="text-mk fw-semibold">Login</a> sebagai pelamar untuk memberikan ulasan.
                            </div>
                        @endauth
                    </div>

                    {{-- Daftar Ulasan --}}
                    <div class="card card-hover p-4" data-aos="fade-left">
                        <h6 class="fw-bold mb-3"><i class="bi bi-chat-square-quote me-2 text-mk"></i>Ulasan ({{ $perusahaan->reviews->count() }})</h6>
                        @forelse($perusahaan->reviews->take(5) as $review)
                            <div class="mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}" style="border-color:var(--mk-border) !important;">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <div class="avatar avatar-sm" style="background:{{ $colors[$review->pelamar_id % count($colors)] }};">
                                        {{ strtoupper(substr($review->pelamar->nama ?? '?', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold small">{{ $review->pelamar->nama ?? 'Pelamar' }}</div>
                                        <div class="rating small">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="bi {{ $review->rating >= $i ? 'bi-star-fill' : 'bi-star' }}"></i>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                                @if($review->pelamar?->universitas)
                                    <div class="small text-muted-2 mb-1" style="font-size:.72rem;"><i class="bi bi-mortarboard me-1"></i>{{ $review->pelamar->universitas }}</div>
                                @endif
                                <p class="small text-muted-2 mb-0">"{{ $review->komentar }}"</p>
                            </div>
                        @empty
                            <p class="text-muted-2 small mb-0">Belum ada ulasan. Jadilah yang pertama! 🎉</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Follow perusahaan
        const followBtn = document.getElementById('followBtn');
        followBtn?.addEventListener('click', async (e) => {
            if (!{{ auth()->check() ? 'true' : 'false' }}) {
                window.location.href = '{{ route('login') }}';
                return;
            }
            const btn = e.currentTarget;
            try {
                const data = await mkFetch(btn.dataset.url, { method: 'POST' });
                const following = data.following;
                btn.classList.toggle('btn-mk', following);
                btn.classList.toggle('btn-soft', !following);
                btn.querySelector('i').className = following ? 'bi bi-star-fill me-1' : 'bi bi-star me-1';
                document.getElementById('followText').textContent = following ? 'Mengikuti' : 'Ikuti Perusahaan';
                document.getElementById('followCount').textContent = data.count;
                mkToast(following ? 'Anda mengikuti {{ $perusahaan->nama_perusahaan }}' : 'Berhenti mengikuti perusahaan', following ? 'success' : 'info');
            } catch (err) {
                mkToast(err.message, 'error');
            }
        });

        const stars = document.querySelectorAll('.review-star');
        const ratingInput = document.getElementById('ratingInput');
        if (!stars.length || !ratingInput) return;

        const setStars = (value) => {
            stars.forEach(star => {
                const active = parseInt(star.dataset.value, 10) <= value;
                star.className = active ? 'bi bi-star-fill fs-4 review-star' : 'bi bi-star fs-4 review-star';
                star.style.color = active ? '#FBBF24' : 'var(--mk-muted)';
            });
        };

        stars.forEach(star => {
            star.addEventListener('mouseenter', () => setStars(parseInt(star.dataset.value, 10)));
            star.addEventListener('click', () => {
                ratingInput.value = star.dataset.value;
                setStars(parseInt(star.dataset.value, 10));
            });
        });

        document.getElementById('reviewStars')?.addEventListener('mouseleave', () => {
            setStars(parseInt(ratingInput.value, 10) || 0);
        });

        document.getElementById('reviewForm')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const form = e.currentTarget;
            const btn = document.getElementById('reviewBtn');

            if (!ratingInput.value) {
                mkToast('Pilih rating bintang terlebih dahulu.', 'error');
                return;
            }

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengirim...';

            try {
                const data = await mkFetch(form.action, { method: 'POST', body: new FormData(form) });
                mkToast(data.message, 'success');
                setTimeout(() => window.location.reload(), 1200);
            } catch (err) {
                mkToast(err.message, 'error');
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-send me-1"></i>Kirim Ulasan';
            }
        });
    });
</script>
@endpush
