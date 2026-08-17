<section class="section" id="fitur-pembeda">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="badge-mk"><i class="bi bi-stars me-1"></i>Fitur Pembeda MagangKu</span>
            <h2 class="section-title mt-3">Lebih dari Sekadar Cari Kerja</h2>
            <p class="section-sub mx-auto">Dua fitur unik yang tidak semua platform punya — untuk membantumu tumbuh dan menonjol.</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-6" data-aos="fade-right">
                <div class="card card-hover h-100 p-4 p-md-5 diff-card" style="background:linear-gradient(135deg, rgba(79,195,247,.10), transparent);">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="feature-icon"><i class="bi bi-lightning-charge-fill"></i></div>
                        <div>
                            <h5 class="fw-bold mb-0">Skill Assessment</h5>
                            <div class="small text-muted-2">Tes kemampuan dasar gratis</div>
                        </div>
                    </div>
                    <p class="text-muted-2 small mb-4">Ukur kemampuanmu lewat tes interaktif, dapatkan skor langsung, dan raih badge <b>Top Talent</b> yang membuat profilmu menonjol di mata recruiter.</p>
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <span class="badge rounded-pill text-bg-light border"><i class="bi bi-stopwatch me-1 text-mk"></i>10–15 menit</span>
                        <span class="badge rounded-pill text-bg-light border"><i class="bi bi-trophy me-1 text-mk"></i>Badge Top Talent</span>
                        <span class="badge rounded-pill text-bg-light border"><i class="bi bi-patch-check me-1 text-mk"></i>Hasil di profil</span>
                    </div>
                    <a href="{{ route('assessment.index') }}" class="btn btn-mk"><i class="bi bi-play-fill me-1"></i>Ikuti Skill Assessment</a>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <div class="card card-hover h-100 p-4 p-md-5 diff-card" style="background:linear-gradient(135deg, rgba(30,58,95,.08), transparent);">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="feature-icon" style="background:var(--mk-gradient-dark);"><i class="bi bi-signpost-split-fill"></i></div>
                        <div>
                            <h5 class="fw-bold mb-0">Career Roadmap</h5>
                            <div class="small text-muted-2">Peta jalan kariermu</div>
                        </div>
                    </div>
                    <p class="text-muted-2 small mb-4">Pilih bidang yang kamu minati dan lihat tahapan lengkapnya — dari skill yang harus dikuasai, estimasi durasi, hingga potensi gaji. Lacak progresmu step by step.</p>
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <span class="badge rounded-pill text-bg-light border"><i class="bi bi-signpost-2 me-1 text-mk"></i>Tahapan terstruktur</span>
                        <span class="badge rounded-pill text-bg-light border"><i class="bi bi-cash-stack me-1 text-mk"></i>Info gaji</span>
                        <span class="badge rounded-pill text-bg-light border"><i class="bi bi-graph-up-arrow me-1 text-mk"></i>Tracker progres</span>
                    </div>
                    <a href="{{ route('roadmaps.index') }}" class="btn btn-mk"><i class="bi bi-map me-1"></i>Jelajahi Career Roadmap</a>
                </div>
            </div>
        </div>
    </div>
</section>
