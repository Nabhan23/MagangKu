<section class="section" id="perusahaan">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="badge-mk"><i class="bi bi-patch-check-fill me-1"></i>Terverifikasi</span>
            <h2 class="section-title mt-3">Perusahaan Terpercaya</h2>
            <p class="section-sub mx-auto">Ribuan perusahaan terverifikasi telah mempercayakan rekrutmen mereka kepada MagangKu.</p>
        </div>

        <div class="row g-4" data-aos="fade-up" data-aos-delay="100">
            @foreach($companies as $company)
                <div class="col-6 col-md-4 col-lg-3 col-xl">
                    <a href="{{ route('companies.show', $company) }}" class="text-decoration-none">
                        <div class="card card-hover text-center p-3 h-100">
                            @php
                                $colors = ['#4FC3F7', '#26A69A', '#5C6BC0', '#FFA726', '#EC407A', '#66BB6A', '#AB47BC', '#8D6E63'];
                                $initial = strtoupper(substr($company->nama_perusahaan, 0, 1));
                            @endphp
                            <div class="mx-auto mb-2" style="width:56px;height:56px;border-radius:1rem;background:{{ $colors[$company->id % count($colors)] }};display:flex;align-items:center;justify-content:center;overflow:hidden;">
                                @if($company->logo_url)
                                    <img src="{{ $company->logo_url }}" alt="{{ $company->nama_perusahaan }}" width="56" height="56" style="object-fit:cover;">
                                @else
                                    <span class="fw-bold text-white fs-5">{{ $initial }}</span>
                                @endif
                            </div>
                            <div class="fw-semibold small text-accent text-truncate">{{ $company->nama_perusahaan }}</div>
                            <div class="text-muted-2 small text-truncate">{{ $company->industri ?: 'Perusahaan' }}</div>
                            <div class="rating small">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi {{ $company->rating >= $i ? 'bi-star-fill' : 'bi-star' }}"></i>
                                @endfor
                            </div>
                            @if($company->status_verifikasi === 'verified')
                                <span class="text-mk small"><i class="bi bi-patch-check-fill"></i> Terverifikasi</span>
                            @endif
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        <div class="text-center mt-5" data-aos="fade-up">
            <a href="{{ route('companies.index') }}" class="btn btn-outline-mk btn-lg px-5">Lihat Semua Perusahaan <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
    </div>
</section>
