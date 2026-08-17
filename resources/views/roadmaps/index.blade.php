@extends('layouts.guest')

@section('title', 'Career Roadmap - MagangKu')
@section('meta_description', 'Temukan jalur karier impianmu. Pilih bidang dan lihat tahapan lengkap menuju karier yang kamu inginkan.')

@section('content')
    <section class="section">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <span class="badge-mk"><i class="bi bi-signpost-split-fill me-1"></i>Career Roadmap</span>
                <h1 class="section-title mt-3">Pilih Jalur Karier Impianmu</h1>
                <p class="section-sub mx-auto">Lihat tahapan lengkap, skill yang dibutuhkan, estimasi durasi, hingga potensi gaji untuk setiap jalur karier.</p>
            </div>

            @php $grouped = $roadmaps->groupBy('bidang'); @endphp
            @forelse($grouped as $bidang => $items)
                <h5 class="fw-bold mb-3 mt-4" data-aos="fade-up">
                    <i class="bi bi-folder2-open me-2 text-mk"></i>{{ $bidangList[$bidang] ?? $bidang }}
                    <span class="badge rounded-pill text-bg-light text-muted-2 ms-2">{{ $items->count() }}</span>
                </h5>
                <div class="row g-4 mb-2">
                    @foreach($items as $roadmap)
                        <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 70 }}">
                            <div class="card card-hover h-100 p-4 d-flex flex-column">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="feature-icon"><i class="bi {{ $roadmap->ikon }}"></i></div>
                                    <div>
                                        <h5 class="fw-bold mb-0">{{ $roadmap->judul }}</h5>
                                        <div class="small text-muted-2">{{ $roadmap->total_tahapan }} tahapan • ± {{ $roadmap->estimasi_durasi }}</div>
                                    </div>
                                </div>
                                <p class="small text-muted-2 mb-3">{{ Str::limit($roadmap->deskripsi, 110) }}</p>
                                <div class="small text-muted-2 mb-3">
                                    <i class="bi bi-cash-stack me-1 text-mk"></i>{{ $roadmap->gaji_range }}
                                </div>
                                <div class="mt-auto">
                                    <a href="{{ route('roadmaps.show', $roadmap) }}" class="btn btn-mk w-100">
                                        <i class="bi bi-map me-1"></i>Lihat Jalur Karier
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="bi bi-map fs-1 text-muted-2"></i>
                    <p class="text-muted-2 mt-3">Jalur karier segera hadir!</p>
                </div>
            @endforelse
        </div>
    </section>
@endsection
