<section class="section bg-soft" id="fitur">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="badge-mk"><i class="bi bi-gem me-1"></i>Fitur Unggulan</span>
            <h2 class="section-title mt-3">Kenapa Memilih MagangKu?</h2>
            <p class="section-sub mx-auto">Semua yang kamu butuhkan untuk memulai dan mengembangkan karier dalam satu platform.</p>
        </div>

        @php
            $features = [
                ['icon' => 'bi-clipboard2-check-fill', 'title' => 'Tracking Lamaran', 'desc' => 'Pantau status lamaranmu secara realtime dari menunggu hingga diterima.'],
                ['icon' => 'bi-chat-square-quote-fill', 'title' => 'Review Tempat Magang', 'desc' => 'Lihat pengalaman peserta magang sebelumnya sebelum memutuskan.'],
                ['icon' => 'bi-file-earmark-text-fill', 'title' => 'CV Digital', 'desc' => 'Kelola CV online dalam satu platform dan lamar dengan sekali klik.'],
                ['icon' => 'bi-person-workspace', 'title' => 'Portofolio Online', 'desc' => 'Tampilkan karya terbaikmu kepada recruiter lewat halaman portofolio publik.'],
                ['icon' => 'bi-calendar-event-fill', 'title' => 'Event Karier', 'desc' => 'Ikuti webinar, workshop, dan job fair nasional dengan sertifikat digital.'],
                ['icon' => 'bi-shield-check-fill', 'title' => 'Perusahaan Terverifikasi', 'desc' => 'Semua lowongan aman dan terpercaya karena perusahaan telah diverifikasi.'],
            ];
        @endphp

        <div class="row g-4">
            @foreach($features as $i => $feature)
                <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="{{ $i * 80 }}">
                    <div class="card card-hover feature-card h-100">
                        <div class="feature-icon"><i class="bi {{ $feature['icon'] }}"></i></div>
                        <h5 class="fw-bold mb-2">{{ $feature['title'] }}</h5>
                        <p class="text-muted-2 mb-0 small">{{ $feature['desc'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
