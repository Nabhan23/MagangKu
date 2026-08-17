<section class="section bg-soft" id="lowongan">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="badge-mk"><i class="bi bi-lightning-charge-fill me-1"></i>Terbaru</span>
            <h2 class="section-title mt-3">Lowongan & Magang Terbaru</h2>
            <p class="section-sub mx-auto">Temukan peluang karier impianmu dari perusahaan-perusahaan terpercaya.</p>
        </div>

        {{-- Filter cepat --}}
        <div class="d-flex flex-wrap justify-content-center gap-2 mb-4" data-aos="fade-up">
            <a href="{{ route('jobs.index') }}" class="chip">Semua</a>
            @foreach(['magang' => 'Magang', 'full_time' => 'Full Time', 'part_time' => 'Part Time', 'freelance' => 'Freelance', 'remote' => 'Remote'] as $key => $label)
                <a href="{{ route('jobs.index', ['jenis' => $key]) }}" class="chip">{{ $label }}</a>
            @endforeach
        </div>

        <div class="row g-4">
            @forelse($jobs as $job)
                <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 80 }}">
                    <x-job-card :job="$job"/>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <i class="bi bi-inbox fs-1 text-muted-2"></i>
                    <p class="text-muted-2 mt-2">Belum ada lowongan tersedia.</p>
                </div>
            @endforelse
        </div>

        <div class="text-center mt-5" data-aos="fade-up">
            <a href="{{ route('jobs.index') }}" class="btn btn-outline-mk btn-lg px-5">Lihat Semua Lowongan <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
    </div>
</section>
