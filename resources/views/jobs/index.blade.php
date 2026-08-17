@extends('layouts.guest')

@section('title', 'Lowongan Kerja & Magang - MagangKu')
@section('meta_description', 'Cari lowongan kerja dan magang terbaru dari perusahaan terpercaya di Indonesia.')

@section('content')
    {{-- Header --}}
    <section class="hero-section" style="padding:5rem 0 4rem;">
        <div class="container position-relative" style="z-index:2;">
            <div class="text-center" data-aos="fade-up">
                <span class="badge-mk"><i class="bi bi-briefcase me-1"></i>{{ number_format($jobs->total()) }} Lowongan Tersedia</span>
                <h1 class="hero-title mt-3">Temukan <span class="grad">Lowongan</span> & <span class="grad">Magang</span> Impianmu</h1>
                <p class="text-muted-2 mx-auto" style="max-width:600px;">Filter berdasarkan jenis pekerjaan, lokasi, dan kategori untuk menemukan peluang terbaikmu.</p>
            </div>

            {{-- Search bar --}}
            <form id="filterForm" method="GET" action="{{ route('jobs.index') }}" class="glass rounded-4 p-3 mt-4" data-aos="fade-up" data-aos-delay="100">
                <div class="row g-2 align-items-end">
                    <div class="col-lg-5">
                        <label class="form-label small mb-1"><i class="bi bi-search me-1 text-mk"></i>Posisi / Perusahaan</label>
                        <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" class="form-control" placeholder="Frontend Developer, PT Maju...">
                    </div>
                    <div class="col-lg-3">
                        <label class="form-label small mb-1"><i class="bi bi-geo-alt me-1 text-mk"></i>Lokasi</label>
                        <input type="text" name="lokasi" value="{{ $filters['lokasi'] ?? '' }}" class="form-control" placeholder="Jakarta, Bandung...">
                    </div>
                    <div class="col-lg-2">
                        <label class="form-label small mb-1"><i class="bi bi-funnel me-1 text-mk"></i>Urutkan</label>
                        <select name="sort" class="form-select">
                            <option value="terbaru" @selected(($filters['sort'] ?? 'terbaru') === 'terbaru')>Terbaru</option>
                            <option value="gaji" @selected(($filters['sort'] ?? '') === 'gaji')>Gaji Tertinggi</option>
                            <option value="deadline" @selected(($filters['sort'] ?? '') === 'deadline')>Deadline Terdekat</option>
                        </select>
                    </div>
                    <div class="col-lg-2 d-grid">
                        <button type="submit" class="btn btn-mk"><i class="bi bi-search me-1"></i>Cari</button>
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
                {{-- Sidebar filter --}}
                <div class="col-lg-3">
                    <div class="card filter-card p-4 position-sticky" style="top:90px;" data-aos="fade-right">
                        <h6 class="fw-bold mb-3"><i class="bi bi-funnel me-1 text-mk"></i>Filter</h6>

                        <label class="form-label small">Jenis Pekerjaan</label>
                        <div class="d-grid gap-2 mb-3">
                            @foreach($jenisList as $key => $label)
                                <div class="form-check">
                                    <input class="form-check-input filter-jenis" type="checkbox" name="jenis" value="{{ $key }}" id="jenis-{{ $key }}"
                                        @checked(in_array($key, (array)($filters['jenis'] ?? [])))>
                                    <label class="form-check-label small" for="jenis-{{ $key }}">{{ $label }}</label>
                                </div>
                            @endforeach
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input filter-jenis" type="checkbox" name="remote" value="1" id="remote"
                                @checked(!empty($filters['remote']))>
                            <label class="form-check-label small fw-semibold" for="remote"><i class="bi bi-wifi me-1 text-mk"></i>Remote / WFH</label>
                        </div>

                        <label class="form-label small">Kategori</label>
                        <select class="form-select mb-3" name="kategori" id="filterKategori">
                            <option value="">Semua Kategori</option>
                            @foreach($kategoriList as $kategori)
                                <option value="{{ $kategori->id }}" @selected(($filters['kategori'] ?? '') == $kategori->id)>{{ $kategori->nama }}</option>
                            @endforeach
                        </select>

                        <button type="button" class="btn btn-soft w-100 btn-sm" id="resetFilter"><i class="bi bi-arrow-counterclockwise me-1"></i>Reset Filter</button>
                    </div>
                </div>

                {{-- Hasil --}}
                <div class="col-lg-9">
                    @if($rekomendasi->isNotEmpty())
                        <div class="mb-4" data-aos="fade-up">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <span class="badge rounded-circle text-bg-mk d-inline-flex align-items-center justify-content-center" style="width:32px;height:32px;"><i class="bi bi-stars"></i></span>
                                <div>
                                    <h6 class="fw-bold mb-0">Rekomendasi Untukmu</h6>
                                    <div class="small text-muted-2">Cocok dengan skill di profilmu</div>
                                </div>
                            </div>
                            <div class="row g-4">
                                @foreach($rekomendasi as $job)
                                    <div class="col-md-6 col-xl-4">
                                        <x-job-card :job="$job"/>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="small text-muted-2">Menampilkan <span class="fw-bold text-accent" id="jobCount">{{ $jobs->total() }}</span> lowongan</div>
                        <a href="{{ route('jobs.index', ['jenis' => 'magang']) }}" class="btn btn-soft btn-sm">Lihat Magang Saja</a>
                    </div>

                    <div id="jobResults">
                        @include('jobs.partials.list', ['jobs' => $jobs])
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('filterForm');
        const results = document.getElementById('jobResults');
        const count = document.getElementById('jobCount');
        const url = new URL(window.location.href);

        async function loadResults() {
            const params = new URLSearchParams(new FormData(form));
            // checkbox yang tidak dicentang tidak ikut di FormData -> tambahkan remote manual
            if (document.getElementById('remote')?.checked) params.set('remote', '1');
            const res = await fetch(`{{ route('jobs.index') }}?${params.toString()}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            const data = await res.json();
            results.innerHTML = data.html;
            count.textContent = data.count;
            history.replaceState(null, '', `?${params.toString()}`);
            // Re-init AOS pada konten baru
            if (window.AOS) AOS.refresh();
            window.scrollTo({ top: results.offsetTop - 100, behavior: 'smooth' });
        }

        // Checkbox jenis & remote
        document.querySelectorAll('.filter-jenis').forEach(el => {
            el.addEventListener('change', loadResults);
        });
        // Kategori select
        document.getElementById('filterKategori')?.addEventListener('change', loadResults);

        // Pagination (event delegation)
        results.addEventListener('click', async (e) => {
            const link = e.target.closest('a[data-page-link]');
            if (!link) return;
            e.preventDefault();
            const params = new URLSearchParams(new FormData(form));
            params.set('page', link.dataset.pageLink);
            if (document.getElementById('remote')?.checked) params.set('remote', '1');
            const res = await fetch(`{{ route('jobs.index') }}?${params.toString()}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            const data = await res.json();
            results.innerHTML = data.html;
            history.replaceState(null, '', `?${params.toString()}`);
            window.scrollTo({ top: results.offsetTop - 100, behavior: 'smooth' });
        });

        // Reset
        document.getElementById('resetFilter')?.addEventListener('click', () => {
            form.querySelectorAll('input[type="checkbox"]').forEach(c => c.checked = false);
            form.querySelectorAll('input[type="text"]').forEach(i => i.value = '');
            form.querySelector('select[name="sort"]').value = 'terbaru';
            document.getElementById('filterKategori').value = '';
            loadResults();
        });

        // Bookmark
        results.addEventListener('click', async (e) => {
            const btn = e.target.closest('[data-save-url]');
            if (!btn) return;
            e.preventDefault();
            e.stopPropagation();
            if (!{{ auth()->check() ? 'true' : 'false' }}) {
                window.location.href = '{{ route('login') }}';
                return;
            }
            try {
                const data = await mkFetch(btn.dataset.saveUrl, { method: 'POST' });
                btn.classList.toggle('saved', data.saved);
                btn.querySelector('i').className = data.saved ? 'bi bi-bookmark-fill' : 'bi bi-bookmark';
                mkToast(data.saved ? 'Lowongan disimpan' : 'Lowongan dihapus dari simpanan', data.saved ? 'success' : 'info');
            } catch (err) {
                mkToast(err.message, 'error');
            }
        });
    });
</script>
@endpush
