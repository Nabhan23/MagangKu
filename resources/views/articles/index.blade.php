@extends('layouts.guest')

@section('title', 'Artikel Karier - MagangKu')

@section('content')
    <section class="hero-section" style="padding:5rem 0 4rem;">
        <div class="container position-relative" style="z-index:2;">
            <div class="text-center" data-aos="fade-up">
                <span class="badge-mk"><i class="bi bi-journal-richtext me-1"></i>Wawasan Karier</span>
                <h1 class="hero-title mt-3">Artikel & <span class="grad">Tips Karier</span></h1>
                <p class="text-muted-2 mx-auto" style="max-width:600px;">Tips CV ATS, interview, dunia kerja, dan pengembangan skill dari para ahli.</p>
            </div>

            <div class="d-flex flex-wrap justify-content-center gap-2 mt-4" data-aos="fade-up" data-aos-delay="100">
                <a href="{{ route('articles.index') }}" class="chip {{ !$activeKategori ? 'active' : '' }}">Semua</a>
                @foreach($kategoriList as $key => $label)
                    <a href="{{ route('articles.index', ['kategori' => $key]) }}" class="chip {{ $activeKategori === $key ? 'active' : '' }}">{{ $label }}</a>
                @endforeach
            </div>
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
                @forelse($articles as $article)
                    @php
                        $covers = [
                            'tips_cv_ats' => 'img/articles/tips-cv-ats.svg',
                            'tips_interview' => 'img/articles/tips-interview.svg',
                            'dunia_kerja' => 'img/articles/dunia-kerja.svg',
                            'magang' => 'img/articles/tips-magang.svg',
                            'pengembangan_skill' => 'img/articles/pengembangan-skill.svg',
                        ];
                        $cover = $article->gambar ? asset('storage/' . $article->gambar) : asset($covers[$article->kategori] ?? 'img/articles/fresh-graduate.svg');
                    @endphp
                    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 80 }}">
                        <div class="card card-hover h-100 overflow-hidden">
                            <div class="position-relative">
                                <img src="{{ $cover }}" alt="{{ $article->judul }}" class="article-img w-100">
                                <span class="position-absolute top-0 start-0 m-3 badge rounded-pill text-bg-primary">{{ $article->kategori_label }}</span>
                            </div>
                            <div class="card-body p-4 d-flex flex-column">
                                <h6 class="fw-bold"><a href="{{ route('articles.show', $article) }}" class="text-decoration-none text-accent">{{ $article->judul }}</a></h6>
                                <p class="small text-muted-2 mb-3">{{ Str::limit($article->ringkasan ?: strip_tags($article->konten), 110) }}</p>
                                <div class="d-flex align-items-center justify-content-between small text-muted-2 mt-auto">
                                    <span><i class="bi bi-person me-1 text-mk"></i>{{ $article->penulis ?: 'Tim MagangKu' }}</span>
                                    <span>{{ $article->published_at?->translatedFormat('d M Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <i class="bi bi-journal-x fs-1 text-muted-2"></i>
                        <h6 class="fw-bold mt-3">Belum Ada Artikel</h6>
                    </div>
                @endforelse
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $articles->links() }}
            </div>
        </div>
    </section>
@endsection
