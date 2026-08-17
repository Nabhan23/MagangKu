<section class="section" id="testimoni">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="badge-mk"><i class="bi bi-chat-heart me-1"></i>Testimoni</span>
            <h2 class="section-title mt-3">Kata Mereka Tentang MagangKu</h2>
            <p class="section-sub mx-auto">Ribuan pengguna telah memulai karier impian mereka bersama kami.</p>
        </div>

        <div id="testimoniCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="4500" data-aos="fade-up">
            <div class="carousel-inner">
                @php $chunks = $testimonials->chunk(3); @endphp
                @forelse($chunks as $chunk)
                    <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                        <div class="row g-4 justify-content-center">
                            @foreach($chunk as $testimoni)
                                <div class="col-md-6 col-lg-4">
                                    <div class="card card-hover testimonial-card h-100 p-4">
                                        <div class="rating mb-3">
                                            @for($i = 1; $i <= 5; $i++)<i class="bi bi-star-fill"></i>@endfor
                                        </div>
                                        <p class="text-muted-2 small mb-4 fst-italic">"{{ $testimoni->komentar }}"</p>
                                        <div class="d-flex align-items-center gap-3 mt-auto">
                                            @php
                                                $colors = ['#4FC3F7', '#26A69A', '#5C6BC0', '#FFA726', '#EC407A'];
                                                $foto = $testimoni->foto ? asset('storage/' . $testimoni->foto) : null;
                                            @endphp
                                            @if($foto)
                                                <img src="{{ $foto }}" class="rounded-circle" width="48" height="48" style="object-fit:cover;">
                                            @else
                                                <div class="avatar" style="background:{{ $colors[$loop->parent->index % count($colors)] }};">{{ strtoupper(substr($testimoni->nama, 0, 1)) }}</div>
                                            @endif
                                            <div>
                                                <div class="fw-bold small">{{ $testimoni->nama }}</div>
                                                <div class="text-muted-2 small">{{ $testimoni->universitas }}@if($testimoni->role) • {{ $testimoni->role }}@endif</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted-2">Belum ada testimoni.</div>
                @endforelse
            </div>

            @if($chunks->count() > 1)
                <div class="d-flex justify-content-center gap-2 mt-4">
                    <button class="btn btn-soft btn-sm" type="button" data-bs-target="#testimoniCarousel" data-bs-slide="prev"><i class="bi bi-chevron-left"></i></button>
                    <button class="btn btn-soft btn-sm" type="button" data-bs-target="#testimoniCarousel" data-bs-slide="next"><i class="bi bi-chevron-right"></i></button>
                </div>
            @endif
        </div>
    </div>
</section>
