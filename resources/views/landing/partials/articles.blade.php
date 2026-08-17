<section class="section bg-soft" id="artikel">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="badge-mk"><i class="bi bi-journal-richtext me-1"></i>Artikel Karier</span>
            <h2 class="section-title mt-3">Tips & Wawasan Karier</h2>
            <p class="section-sub mx-auto">Kembangkan diri dengan tips CV ATS, interview, dan dunia kerja dari para ahli.</p>
        </div>

        <div class="row g-4">
            @forelse($articles as $article)
                <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                    <div class="card card-hover h-100 overflow-hidden">
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
                        <img src="{{ $cover }}" alt="{{ $article->judul }}" class="article-img w-100">
                        <div class="card-body p-4">
                            <span class="badge-mk small mb-2 d-inline-block">{{ $article->kategori_label }}</span>
                            <h6 class="fw-bold">
                                <a href="{{ route('articles.show', $article) }}" class="text-decoration-none text-accent">{{ $article->judul }}</a>
                            </h6>
                            <p class="small text-muted-2 mb-3">{{ Str::limit($article->ringkasan ?: strip_tags($article->konten), 90) }}</p>
                            <div class="d-flex align-items-center justify-content-between small text-muted-2">
                                <span><i class="bi bi-person me-1 text-mk"></i>{{ $article->penulis ?: 'Tim MagangKu' }}</span>
                                <span>{{ $article->published_at?->translatedFormat('d M Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <i class="bi bi-journal-x fs-1 text-muted-2"></i>
                    <p class="text-muted-2 mt-2">Belum ada artikel.</p>
                </div>
            @endforelse
        </div>

        <div class="text-center mt-5" data-aos="fade-up">
            <a href="{{ route('articles.index') }}" class="btn btn-outline-mk btn-lg px-5">Baca Semua Artikel <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
    </div>
</section>
