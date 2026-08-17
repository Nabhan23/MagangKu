@extends('layouts.guest')

@section('title', $roadmap->judul . ' - Career Roadmap MagangKu')
@section('meta_description', 'Jalur karier ' . $roadmap->judul . ': tahapan, skill, dan prospek karier.')

@section('content')
    @php $isFollowed = $profile && $profile->career_roadmap_id === $roadmap->id; @endphp

    <section class="section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    {{-- Header --}}
                    <div class="card border-0 bg-mk-gradient-dark text-white p-4 mb-4" data-aos="fade-up">
                        <div class="d-flex flex-wrap align-items-center gap-3">
                            <div class="feature-icon" style="background:rgba(255,255,255,.2);color:#fff;width:64px;height:64px;font-size:1.5rem;">
                                <i class="bi {{ $roadmap->ikon }}"></i>
                            </div>
                            <div class="flex-grow-1">
                                <span class="badge rounded-pill text-bg-light text-accent mb-1">{{ $roadmap->bidang_label }}</span>
                                <h1 class="fw-bold fs-3 mb-1">{{ $roadmap->judul }}</h1>
                                <div class="small" style="color:rgba(255,255,255,.9);">
                                    <i class="bi bi-signpost-2 me-1"></i>{{ $roadmap->total_tahapan }} tahapan •
                                    <i class="bi bi-clock ms-1 me-1"></i>± {{ $roadmap->estimasi_durasi }} •
                                    <i class="bi bi-cash-stack ms-1 me-1"></i>{{ $roadmap->gaji_range }}
                                </div>
                            </div>
                        </div>
                        @if($roadmap->deskripsi)
                            <p class="mb-0 mt-3 small" style="color:rgba(255,255,255,.92);">{{ $roadmap->deskripsi }}</p>
                        @endif
                    </div>

                    {{-- Progress (untuk pelamar) --}}
                    @auth
                        @if(auth()->user()->isPelamar())
                            <div class="card card-hover p-4 mb-4" data-aos="fade-up">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="fw-bold mb-0"><i class="bi bi-graph-up-arrow me-2 text-mk"></i>Progress Kamu</h6>
                                    @if($isFollowed)
                                        <span class="badge rounded-pill text-bg-success" id="progressPct">@if($progress){{ (int) round((count($progress) / max($roadmap->total_tahapan, 1)) * 100) }}% selesai @else 0% selesai @endif</span>
                                    @endif
                                </div>
                                @if($isFollowed)
                                    <div class="progress" style="height:8px;">
                                        <div class="progress-bar bg-mk" id="progressBar" style="width:{{ $progress ? round((count($progress) / max($roadmap->total_tahapan, 1)) * 100) : 0 }}%"></div>
                                    </div>
                                    <div class="small text-muted-2 mt-2">
                                        <i class="bi bi-lightbulb me-1"></i>Centang setiap tahapan yang sudah kamu selesaikan.
                                        @if(count($progress) === $roadmap->total_tahapan)
                                            <b class="text-success d-block mt-1"><i class="bi bi-trophy-fill me-1"></i>Selamat! Kamu telah menyelesaikan seluruh jalur karier ini 🎉</b>
                                        @endif
                                    </div>
                                @else
                                    <p class="small text-muted-2 mb-3">Mulai jalur karier ini untuk melacak progresmu dan tampilkan di dashboard.</p>
                                    <form method="POST" action="{{ route('roadmaps.start', $roadmap) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-mk"><i class="bi bi-play-fill me-1"></i>Mulai Jalur Ini</button>
                                    </form>
                                @endif
                            </div>
                        @endif
                    @endauth

                    {{-- Timeline tahapan --}}
                    <div class="card card-hover p-4 mb-4" data-aos="fade-up">
                        <h6 class="fw-bold mb-4"><i class="bi bi-flag-fill me-2 text-mk"></i>Tahapan Menuju Karier</h6>
                        <div class="roadmap-timeline">
                            @foreach($roadmap->tahapan as $i => $tahap)
                                <div class="roadmap-step {{ $isFollowed && in_array($i, $progress, true) ? 'done' : '' }}" data-step="{{ $i }}">
                                    <div class="roadmap-node">
                                        @if($isFollowed && in_array($i, $progress, true))
                                            <i class="bi bi-check-lg"></i>
                                        @else
                                            {{ $i + 1 }}
                                        @endif
                                    </div>
                                    <div class="roadmap-body">
                                        <div class="d-flex justify-content-between align-items-start gap-2">
                                            <div>
                                                <h6 class="fw-bold mb-1">{{ $tahap['judul'] ?? 'Tahap ' . ($i + 1) }}</h6>
                                                <div class="small text-muted-2 mb-2">
                                                    <i class="bi bi-clock me-1"></i>{{ $tahap['durasi'] ?? '?' }} bulan
                                                </div>
                                            </div>
                                            @if($isFollowed)
                                                <button type="button" class="btn btn-sm btn-soft roadmap-toggle" data-step="{{ $i }}">
                                                    {{ in_array($i, $progress, true) ? 'Selesai ✓' : 'Tandai' }}
                                                </button>
                                            @endif
                                        </div>
                                        <p class="small text-muted-2 mb-2">{{ $tahap['deskripsi'] ?? '' }}</p>
                                        @if(!empty($tahap['skills']))
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach($tahap['skills'] as $skill)
                                                    <span class="badge rounded-pill text-bg-light text-mk border">{{ $skill }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Prospek --}}
                    @if($roadmap->prospek)
                        <div class="card border-0 bg-soft p-4 mb-4" data-aos="fade-up">
                            <h6 class="fw-bold mb-2"><i class="bi bi-rocket-takeoff me-2 text-mk"></i>Prospek Karier</h6>
                            <p class="small text-muted-2 mb-0">{{ $roadmap->prospek }}</p>
                        </div>
                    @endif

                    {{-- Lowongan terkait --}}
                    @if($lowongan->isNotEmpty())
                        <div class="card card-hover p-4 mb-4" data-aos="fade-up">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold mb-0"><i class="bi bi-briefcase me-2 text-mk"></i>Lowongan Terkait</h6>
                                <a href="{{ route('jobs.index') }}" class="small text-mk fw-semibold text-decoration-none">Lihat Semua</a>
                            </div>
                            <div class="row g-3">
                                @foreach($lowongan as $job)
                                    <div class="col-md-6">
                                        <a href="{{ route('jobs.show', $job) }}" class="text-decoration-none">
                                            <div class="d-flex align-items-center gap-3 p-3 rounded-4 card-hover" style="background:var(--mk-bg);">
                                                <img src="{{ asset($job->perusahaan->logo) }}" class="rounded-3" width="44" height="44" style="object-fit:contain;background:#fff;" alt="{{ $job->perusahaan->nama_perusahaan }}">
                                                <div class="min-w-0">
                                                    <div class="fw-semibold small text-accent text-truncate">{{ $job->posisi }}</div>
                                                    <div class="small text-muted-2 text-truncate">{{ $job->perusahaan->nama_perusahaan }} • {{ $job->lokasi }}</div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="text-center">
                        <a href="{{ route('roadmaps.index') }}" class="btn btn-soft"><i class="bi bi-arrow-left me-1"></i>Semua Jalur Karier</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    @auth
        @if(auth()->user()->isPelamar() && $isFollowed)
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const total = {{ $roadmap->total_tahapan }};
                    const bar = document.getElementById('progressBar');
                    const pct = document.getElementById('progressPct');

                    document.querySelectorAll('.roadmap-toggle').forEach((btn) => {
                        btn.addEventListener('click', async () => {
                            const step = btn.dataset.step;
                            try {
                                const data = await mkFetch('{{ route('roadmaps.step', [$roadmap, '__STEP__']) }}'.replace('__STEP__', step), {
                                    method: 'POST',
                                });
                                const el = document.querySelector(`.roadmap-step[data-step="${step}"]`);
                                const node = el?.querySelector('.roadmap-node');
                                const done = data.progress.includes(parseInt(step, 10));
                                el?.classList.toggle('done', done);
                                if (node) node.innerHTML = done ? '<i class="bi bi-check-lg"></i>' : (parseInt(step, 10) + 1);
                                btn.textContent = done ? 'Selesai ✓' : 'Tandai';
                                if (bar) bar.style.width = data.persen + '%';
                                if (pct) pct.textContent = data.persen + '% selesai';
                                if (data.selesai) {
                                    mkToast('Selamat! Seluruh jalur karier selesai 🎉', 'success');
                                }
                            } catch (e) {
                                mkToast(e.message, 'error');
                            }
                        });
                    });
                });
            </script>
        @endif
    @endauth
@endpush
