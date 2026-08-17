@extends('layouts.guest')

@section('title', 'FAQ - MagangKu')

@section('content')
    <section class="hero-section" style="padding:5rem 0 4rem;">
        <div class="container position-relative" style="z-index:2;">
            <div class="text-center" data-aos="fade-up">
                <span class="badge-mk"><i class="bi bi-question-circle me-1"></i>FAQ</span>
                <h1 class="hero-title mt-3">Pertanyaan yang <span class="grad">Sering Diajukan</span></h1>
                <p class="text-muted-2 mx-auto" style="max-width:600px;">Temukan jawaban atas pertanyaan seputar MagangKu.</p>
            </div>
        </div>
        <div class="wave-wrap">
            <svg class="wave-anim" viewBox="0 0 1440 90" preserveAspectRatio="none">
                <path fill="var(--mk-bg)" d="M0,60 C240,90 480,20 720,40 C960,60 1200,90 1440,50 L1440,90 L0,90 Z"></path>
            </svg>
        </div>
    </section>

    <section class="section pt-4" style="background:var(--mk-bg);">
        <div class="container" style="max-width:800px;">
            @php $grouped = $faqs->groupBy('kategori'); @endphp
            @forelse($grouped as $kategori => $items)
                <h5 class="fw-bold mb-3 mt-4" data-aos="fade-up">
                    <i class="bi bi-tag-fill text-mk me-2"></i>{{ ucfirst($kategori) }}
                </h5>
                <div class="accordion mb-4" id="faq-{{ Str::slug($kategori) }}">
                    @foreach($items as $faq)
                        <div class="accordion-item border-0 mb-2 rounded-4 overflow-hidden" style="box-shadow:var(--mk-shadow-sm);" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq-item-{{ $faq->id }}" style="background:var(--mk-card-bg);color:var(--mk-text);">
                                    {{ $faq->pertanyaan }}
                                </button>
                            </h2>
                            <div id="faq-item-{{ $faq->id }}" class="accordion-collapse collapse" data-bs-parent="#faq-{{ Str::slug($kategori) }}">
                                <div class="accordion-body text-muted-2" style="background:var(--mk-bg-soft);">{{ $faq->jawaban }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="bi bi-question-circle fs-1 text-muted-2"></i>
                    <p class="text-muted-2 mt-2">Belum ada FAQ.</p>
                </div>
            @endforelse

            <div class="card p-4 text-center border-0 bg-mk-gradient-dark text-white mt-4" data-aos="fade-up">
                <h5 class="fw-bold mb-2">Masih punya pertanyaan?</h5>
                <p class="mb-3" style="color:rgba(255,255,255,.9);">Tim kami siap membantu kamu 24/7.</p>
                <div class="d-flex justify-content-center gap-2">
                    <a href="{{ route('contact') }}" class="btn btn-light fw-bold px-4"><i class="bi bi-chat-dots me-1"></i>Hubungi Kami</a>
                </div>
            </div>
        </div>
    </section>
@endsection
