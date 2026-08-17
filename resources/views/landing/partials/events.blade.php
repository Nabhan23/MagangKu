<section class="section" id="event">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="badge-mk"><i class="bi bi-calendar-event me-1"></i>Event Center</span>
            <h2 class="section-title mt-3">Event Karier Terdekat</h2>
            <p class="section-sub mx-auto">Webinar, workshop, job fair, dan campus hiring dari berbagai perusahaan ternama.</p>
        </div>

        <div class="row g-4">
            @forelse($events as $event)
                <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                    <div class="card card-hover h-100 overflow-hidden">
                        @php
                            $covers = [
                                'webinar' => 'img/events/webinar-karir.svg',
                                'job_fair' => 'img/events/jobfair-nasional.svg',
                                'workshop' => 'img/events/workshop-cv.svg',
                                'campus_hiring' => 'img/events/campus-hiring.svg',
                            ];
                            $cover = $event->gambar ? asset('storage/' . $event->gambar) : asset($covers[$event->kategori] ?? $covers['webinar']);
                            $kategoriBadge = match($event->kategori) {
                                'webinar' => 'primary', 'job_fair' => 'success', 'workshop' => 'warning', 'campus_hiring' => 'info', default => 'secondary'
                            };
                        @endphp
                        <div class="position-relative">
                            <img src="{{ $cover }}" alt="{{ $event->judul }}" class="w-100" style="height:170px;object-fit:cover;">
                            <span class="position-absolute top-0 start-0 m-3 badge rounded-pill text-bg-{{ $kategoriBadge }}">{{ $event->kategori_label }}</span>
                            @if($event->tanggal_mulai->isFuture())
                                <span class="position-absolute bottom-0 start-0 m-3 badge rounded-pill text-bg-light">
                                    <i class="bi bi-clock me-1"></i>{{ $event->tanggal_mulai->diffForHumans() }}
                                </span>
                            @endif
                        </div>
                        <div class="card-body p-4">
                            <h6 class="fw-bold">
                                <a href="{{ route('events.show', $event) }}" class="text-decoration-none text-accent">{{ $event->judul }}</a>
                            </h6>
                            <div class="small text-muted-2 d-grid gap-1 mb-3">
                                <span><i class="bi bi-calendar3 me-2 text-mk"></i>{{ $event->tanggal_mulai->translatedFormat('d F Y') }} @if($event->tanggal_selesai) - {{ $event->tanggal_selesai->translatedFormat('d F Y') }} @endif</span>
                                <span><i class="bi bi-geo-alt me-2 text-mk"></i>{{ $event->is_online ? 'Online' : ($event->lokasi ?: 'Lokasi menyusul') }}</span>
                                <span><i class="bi bi-people me-2 text-mk"></i>{{ $event->terdaftar }}/{{ $event->kapasitas }} peserta</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between">
                                @if($event->tanggal_mulai->isFuture())
                                    <div class="d-flex gap-1" data-countdown="{{ $event->tanggal_mulai->toIso8601String() }}">
                                        <div class="countdown-box"><span class="num cd-d">00</span><span class="lbl">Hari</span></div>
                                        <div class="countdown-box"><span class="num cd-h">00</span><span class="lbl">Jam</span></div>
                                        <div class="countdown-box"><span class="num cd-m">00</span><span class="lbl">Mnt</span></div>
                                    </div>
                                @else
                                    <span class="text-muted-2 small fw-semibold">Event telah berlangsung</span>
                                @endif
                                <a href="{{ route('events.show', $event) }}" class="btn btn-mk btn-sm">Daftar</a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <i class="bi bi-calendar-x fs-1 text-muted-2"></i>
                    <p class="text-muted-2 mt-2">Belum ada event mendatang.</p>
                </div>
            @endforelse
        </div>

        <div class="text-center mt-5" data-aos="fade-up">
            <a href="{{ route('events.index') }}" class="btn btn-outline-mk btn-lg px-5">Lihat Semua Event <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
    </div>
</section>
