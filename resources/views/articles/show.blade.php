@extends('layouts.guest')

@section('title', $article->judul . ' - MagangKu')
@section('meta_description', $article->ringkasan)
@section('og_title', $article->judul)
@section('og_description', $article->ringkasan)
@section('og_type', 'article')
@section('canonical', route('articles.show', $article))

@push('head')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Article',
    'headline' => $article->judul,
    'description' => $article->ringkasan,
    'author' => ['@type' => 'Person', 'name' => $article->penulis],
    'publisher' => ['@type' => 'Organization', 'name' => 'MagangKu', 'logo' => ['@type' => 'ImageObject', 'url' => url('/img/pwa/icon-512.png')]],
    'datePublished' => $article->published_at?->toIso8601String(),
    'image' => url('/img/pwa/icon-512.png'),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush

@section('content')
    <section class="section" style="padding-top:3rem;">
        <div class="container" style="max-width:860px;">
            <nav aria-label="breadcrumb" class="mb-4" data-aos="fade-up">
                <ol class="breadcrumb small mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-mk text-decoration-none">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('articles.index') }}" class="text-mk text-decoration-none">Artikel</a></li>
                    <li class="breadcrumb-item active text-muted-2">{{ Str::limit($article->judul, 40) }}</li>
                </ol>
            </nav>

            <div data-aos="fade-up">
                <span class="badge-mk">{{ $article->kategori_label }}</span>
                <h1 class="fw-bold mt-3 mb-3">{{ $article->judul }}</h1>
                <div class="d-flex align-items-center gap-3 text-muted-2 small mb-4">
                    <span><i class="bi bi-person-circle me-1 text-mk"></i>{{ $article->penulis ?: 'Tim MagangKu' }}</span>
                    <span><i class="bi bi-calendar3 me-1 text-mk"></i>{{ $article->published_at?->translatedFormat('d F Y') }}</span>
                    <span><i class="bi bi-eye me-1 text-mk"></i>{{ number_format($article->views) }}x dibaca</span>
                </div>
            </div>

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

            <div class="card card-hover overflow-hidden mb-4" data-aos="fade-up">
                <img src="{{ $cover }}" alt="{{ $article->judul }}" class="w-100" style="height:320px;object-fit:cover;">
            </div>

            <div class="card card-hover p-4 p-md-5" data-aos="fade-up">
                <div class="article-content text-muted-2" style="line-height:1.9;white-space:pre-line;">{{ $article->konten }}</div>
            </div>

            @if($related->count())
                <h5 class="fw-bold mt-5 mb-4" data-aos="fade-up">Artikel Terkait</h5>
                <div class="row g-4">
                    @foreach($related as $relatedArticle)
                        <div class="col-md-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 80 }}">
                            <div class="card card-hover h-100 overflow-hidden">
                                <img src="{{ asset($covers[$relatedArticle->kategori] ?? 'img/articles/fresh-graduate.svg') }}" class="article-img w-100" style="height:140px;" alt="">
                                <div class="card-body p-3">
                                    <h6 class="fw-bold small">
                                        <a href="{{ route('articles.show', $relatedArticle) }}" class="text-decoration-none text-accent">{{ Str::limit($relatedArticle->judul, 60) }}</a>
                                    </h6>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection
