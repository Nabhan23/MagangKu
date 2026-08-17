<section class="hero-section" id="beranda">
    {{-- Floating Shapes --}}
    <div class="floating-shape" style="width:90px;height:90px;background:rgba(79,195,247,.3);top:20%;left:6%;"></div>
    <div class="floating-shape" style="width:60px;height:60px;background:rgba(129,212,250,.4);top:60%;left:12%;animation-delay:1.4s;"></div>
    <div class="floating-shape" style="width:120px;height:120px;background:rgba(79,195,247,.22);top:15%;right:8%;animation-delay:.8s;"></div>
    <div class="floating-shape" style="width:50px;height:50px;background:rgba(129,212,250,.5);bottom:20%;right:18%;animation-delay:2s;"></div>

    <div class="container position-relative" style="z-index:2;">
        <div class="row align-items-center g-5">
            <div class="col-lg-6" data-aos="fade-up">
                <span class="badge-mk d-inline-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-stars"></i> Platform Karier #1 di Indonesia
                </span>

                <h1 class="hero-title mb-3">
                    Temukan <span class="grad">Magang</span> dan <span class="grad">Pekerjaan</span> Impianmu
                </h1>

                <p class="fs-5 text-muted-2 mb-4">
                    Ribuan peluang magang dan pekerjaan dari perusahaan terpercaya di seluruh Indonesia.
                </p>

                {{-- Typing Animation --}}
                <div class="fs-4 fw-bold text-accent mb-4 d-flex align-items-center">
                    <i class="bi bi-briefcase-fill text-mk me-2"></i>
                    <span id="typingText"></span><span class="typing-cursor ms-1" style="height:1.4em;">&nbsp;</span>
                </div>
                <script>
                    document.getElementById('typingText')?.setAttribute('data-words', JSON.stringify([
                        'Frontend Developer', 'UI/UX Designer', 'Data Analyst', 'Content Writer', 'Digital Marketing'
                    ]));
                </script>

                <div class="d-flex flex-wrap gap-2 mb-4">
                    <a href="{{ route('jobs.index') }}" class="btn btn-mk btn-lg px-4"><i class="bi bi-search me-2"></i>Cari Lowongan</a>
                    <a href="{{ route('jobs.index', ['jenis' => 'magang']) }}" class="btn btn-outline-mk btn-lg px-4"><i class="bi bi-mortarboard me-2"></i>Cari Magang</a>
                    <a href="{{ route('register') }}" class="btn btn-soft btn-lg px-4"><i class="bi bi-person-plus me-2"></i>Daftar Gratis</a>
                </div>

                <div class="d-flex align-items-center gap-4 flex-wrap">
                    <div class="d-flex align-items-center">
                        <div class="d-flex">
                            @foreach(['#4FC3F7','#81D4FA','#0288D1','#1E3A5F','#B3E5FC'] as $c)
                                <div class="rounded-circle border border-2 border-white d-flex align-items-center justify-content-center text-white fw-bold" style="width:38px;height:38px;background:{{ $c }};margin-left:-8px;font-size:.75rem;">
                                    {{ ['R','A','S','Y','D'][$loop->index] }}
                                </div>
                            @endforeach
                        </div>
                        <div class="ms-3">
                            <div class="fw-bold small">{{ number_format($stats['pelamar'], 0, ',', '.') }}+ <span class="text-mk">Pelamar</span></div>
                            <div class="text-muted-2 small">sudah bergabung</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="rating fs-5"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></div>
                        <div class="ms-2 small text-muted-2">{{ $stats['rating'] }}/5 dari {{ number_format($stats['ulasan'], 0, ',', '.') }}+ ulasan</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 text-center position-relative" data-aos="fade-left" data-aos-delay="200">
                <img src="{{ asset('img/hero-illustration.svg') }}" alt="Ilustrasi Karier MagangKu" class="img-fluid" style="max-height:480px;">

                {{-- Floating career icons --}}
                <div class="float-icon p-3" style="top:8%;left:2%;animation:floatIcon 4.5s ease-in-out infinite;">
                    <i class="bi bi-briefcase-fill fs-4"></i>
                </div>
                <div class="float-icon p-3" style="top:20%;right:0;animation:floatIcon 5.5s ease-in-out infinite .8s;">
                    <i class="bi bi-mortarboard-fill fs-4"></i>
                </div>
                <div class="float-icon p-3" style="bottom:12%;left:0;animation:floatIcon 5s ease-in-out infinite 1.4s;">
                    <i class="bi bi-graph-up-arrow fs-4"></i>
                </div>
                <div class="float-icon p-3" style="bottom:6%;right:8%;animation:floatIcon 6s ease-in-out infinite .4s;">
                    <i class="bi bi-chat-dots-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Animated Wave --}}
    <div class="wave-wrap">
        <svg class="wave-anim" viewBox="0 0 1440 90" preserveAspectRatio="none">
            <path fill="var(--mk-bg)" d="M0,60 C240,90 480,20 720,40 C960,60 1200,90 1440,50 L1440,90 L0,90 Z"></path>
        </svg>
    </div>
</section>
