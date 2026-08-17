@extends('layouts.guest')

@section('title', 'Event Karier - MagangKu')

@section('content')
    <section class="hero-section" style="padding:5rem 0 4rem;">
        <div class="container position-relative" style="z-index:2;">
            <div class="text-center" data-aos="fade-up">
                <span class="badge-mk"><i class="bi bi-calendar-event me-1"></i>Event Center</span>
                <h1 class="hero-title mt-3">Event <span class="grad">Karier</span> Terdekat</h1>
                <p class="text-muted-2 mx-auto" style="max-width:600px;">Webinar, workshop, job fair, dan campus hiring untuk mengembangkan kariermu.</p>
            </div>

            <div class="d-flex flex-wrap justify-content-center gap-2 mt-4" data-aos="fade-up" data-aos-delay="100">
                <a href="{{ route('events.index') }}" class="chip {{ !$activeKategori ? 'active' : '' }}">Semua</a>
                @foreach($kategoriList as $key => $label)
                    <a href="{{ route('events.index', ['kategori' => $key]) }}" class="chip {{ $activeKategori === $key ? 'active' : '' }}">{{ $label }}</a>
                @endforeach
            </div>
        </div>
        <div class="wave-wrap">
            <svg class="wave-anim" viewBox="0 0 1440 90" preserveAspectRatio="none">
                <path fill="var(--mk-bg)" d="M0,60 C240,90 480,20 720,40 C960,60 1200,90 1440,50 L1440,90 L0,90 Z"></path>
            </svg>
        </div>
    </section>

    <section class="section pt-4" style="background:var(--mk-bg);">
        <div class="container">
            <div class="row g-4">
                @forelse($events as $event)
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
                    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 80 }}">
                        <div class="card card-hover h-100 overflow-hidden">
                            <div class="position-relative">
                                <img src="{{ $cover }}" alt="{{ $event->judul }}" class="w-100" style="height:180px;object-fit:cover;">
                                <span class="position-absolute top-0 start-0 m-3 badge rounded-pill text-bg-{{ $kategoriBadge }}">{{ $event->kategori_label }}</span>
                                @if($event->tanggal_mulai->isFuture())
                                    <span class="position-absolute bottom-0 start-0 m-3 badge rounded-pill text-bg-light"><i class="bi bi-clock me-1"></i>{{ $event->tanggal_mulai->diffForHumans() }}</span>
                                @endif
                            </div>
                            <div class="card-body p-4 d-flex flex-column">
                                <h6 class="fw-bold"><a href="{{ route('events.show', $event) }}" class="text-decoration-none text-accent">{{ $event->judul }}</a></h6>
                                <div class="small text-muted-2 d-grid gap-1 mb-3">
                                    <span><i class="bi bi-calendar3 me-2 text-mk"></i>{{ $event->tanggal_mulai->translatedFormat('d F Y') }}@if($event->waktu) • {{ $event->waktu }} WIB @endif</span>
                                    <span><i class="bi bi-geo-alt me-2 text-mk"></i>{{ $event->is_online ? 'Online' : ($event->lokasi ?: '-') }}</span>
                                    <span><i class="bi bi-people me-2 text-mk"></i>{{ $event->registrations_count }}/{{ $event->kapasitas }} peserta</span>
                                </div>
                                <div class="mt-auto d-flex align-items-center justify-content-between">
                                    <span class="small text-muted-2"><i class="bi bi-building me-1 text-mk"></i>{{ $event->penyelenggara ?: 'MagangKu' }}</span>
                                    <a href="{{ route('events.show', $event) }}" class="btn btn-mk btn-sm">Detail Event</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <i class="bi bi-calendar-x fs-1 text-muted-2"></i>
                        <h6 class="fw-bold mt-3">Belum Ada Event</h6>
                    </div>
                @endforelse
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $events->links() }}
            </div>
        </div>
    </section>
@endsection
