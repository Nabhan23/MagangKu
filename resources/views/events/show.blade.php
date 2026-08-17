@extends('layouts.guest')

@section('title', $event->judul . ' - MagangKu')
@section('meta_description', Str::limit(strip_tags($event->deskripsi), 160))
@section('og_title', $event->judul)
@section('og_description', Str::limit(strip_tags($event->deskripsi), 160))
@section('canonical', route('events.show', $event))

@push('head')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Event',
    'name' => $event->judul,
    'description' => Str::limit(strip_tags($event->deskripsi), 500),
    'startDate' => $event->tanggal_mulai->toIso8601String(),
    'endDate' => $event->tanggal_selesai?->toIso8601String(),
    'eventAttendanceMode' => $event->is_online ? 'https://schema.org/OnlineEventAttendanceMode' : 'https://schema.org/OfflineEventAttendanceMode',
    'location' => [
        '@type' => 'Place',
        'name' => $event->is_online ? 'Online (Zoom / Streaming)' : $event->lokasi,
        'address' => $event->is_online ? null : ['@type' => 'PostalAddress', 'addressLocality' => $event->lokasi],
    ],
    'organizer' => ['@type' => 'Organization', 'name' => $event->penyelenggara ?: 'MagangKu'],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush

@section('content')
    <section class="section" style="padding-top:3rem;">
        <div class="container">
            <nav aria-label="breadcrumb" class="mb-4" data-aos="fade-up">
                <ol class="breadcrumb small mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-mk text-decoration-none">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('events.index') }}" class="text-mk text-decoration-none">Event Karier</a></li>
                    <li class="breadcrumb-item active text-muted-2">{{ Str::limit($event->judul, 40) }}</li>
                </ol>
            </nav>

            @php
                $covers = [
                    'webinar' => 'img/events/webinar-karir.svg',
                    'job_fair' => 'img/events/jobfair-nasional.svg',
                    'workshop' => 'img/events/workshop-cv.svg',
                    'campus_hiring' => 'img/events/campus-hiring.svg',
                ];
                $cover = $event->gambar ? asset('storage/' . $event->gambar) : asset($covers[$event->kategori] ?? $covers['webinar']);
            @endphp

            <div class="card card-hover overflow-hidden mb-4" data-aos="fade-up">
                <img src="{{ $cover }}" alt="{{ $event->judul }}" class="w-100" style="height:280px;object-fit:cover;">
            </div>

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card card-hover p-4 mb-4" data-aos="fade-up">
                        <div class="d-flex align-items-center gap-2 flex-wrap mb-3">
                            <span class="badge rounded-pill text-bg-primary">{{ $event->kategori_label }}</span>
                            @if($event->tanggal_mulai->isFuture())
                                <span class="badge rounded-pill text-bg-success"><i class="bi bi-clock me-1"></i>{{ $event->tanggal_mulai->diffForHumans() }}</span>
                            @else
                                <span class="badge rounded-pill text-bg-secondary">Event telah berlangsung</span>
                            @endif
                        </div>
                        <h3 class="fw-bold mb-3">{{ $event->judul }}</h3>
                        <div class="content-html text-muted-2" style="white-space:pre-line;">{{ $event->deskripsi }}</div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card card-hover p-4 mb-4" data-aos="fade-left">
                        <h6 class="fw-bold mb-3"><i class="bi bi-info-circle me-2 text-mk"></i>Detail Event</h6>
                        <ul class="list-unstyled small d-grid gap-2 mb-3 text-muted-2">
                            <li><i class="bi bi-calendar3 me-2 text-mk"></i>{{ $event->tanggal_mulai->translatedFormat('l, d F Y') }}@if($event->waktu) • {{ $event->waktu }} WIB @endif</li>
                            @if($event->tanggal_selesai)
                                <li><i class="bi bi-calendar-check me-2 text-mk"></i>Selesai: {{ $event->tanggal_selesai->translatedFormat('d F Y') }}</li>
                            @endif
                            <li><i class="bi bi-geo-alt me-2 text-mk"></i>{{ $event->is_online ? 'Online (Zoom / Streaming)' : ($event->lokasi ?: '-') }}</li>
                            <li><i class="bi bi-people me-2 text-mk"></i>{{ $event->registrations_count }}/{{ $event->kapasitas }} peserta terdaftar</li>
                            <li><i class="bi bi-building me-2 text-mk"></i>Diselenggarakan oleh {{ $event->penyelenggara ?: 'MagangKu' }}</li>
                            @if($event->is_online && $event->link_event)
                                <li><i class="bi bi-link-45deg me-2 text-mk"></i><a href="{{ $event->link_event }}" target="_blank" class="text-mk">Link Event</a></li>
                            @endif
                        </ul>

                        @if(!$event->is_online && $event->lokasi)
                            <div class="rounded-3 overflow-hidden mb-3" style="border:1px solid var(--mk-border);">
                                <iframe
                                    src="https://maps.google.com/maps?q={{ urlencode($event->judul . ' ' . $event->lokasi) }}&t=&z=13&ie=UTF8&iwloc=&output=embed"
                                    width="100%" height="200" style="border:0;" loading="lazy"
                                    referrerpolicy="no-referrer-when-downgrade"
                                    title="Peta lokasi event"></iframe>
                            </div>
                        @endif

                        {{-- Countdown --}}
                        @if($event->tanggal_mulai->isFuture())
                            <div class="d-flex gap-2 justify-content-center mb-3" data-countdown="{{ $event->tanggal_mulai->toIso8601String() }}">
                                <div class="countdown-box"><span class="num cd-d">00</span><span class="lbl">Hari</span></div>
                                <div class="countdown-box"><span class="num cd-h">00</span><span class="lbl">Jam</span></div>
                                <div class="countdown-box"><span class="num cd-m">00</span><span class="lbl">Mnt</span></div>
                                <div class="countdown-box"><span class="num cd-s">00</span><span class="lbl">Dtk</span></div>
                            </div>
                        @endif

                        {{-- Registrasi --}}
                        @auth
                            @if($registered)
                                <div class="alert alert-success border-0 rounded-4 small text-center mb-0">
                                    <i class="bi bi-check-circle-fill me-1"></i>Anda sudah terdaftar di event ini!
                                </div>
                            @elseif($event->tanggal_mulai->isFuture() && $event->registrations_count < $event->kapasitas)
                                <form method="POST" action="{{ route('events.register', $event) }}" class="d-grid gap-2">
                                    @csrf
                                    <input type="text" name="nama" value="{{ auth()->user()->name }}" class="form-control form-control-sm" placeholder="Nama lengkap">
                                    <input type="text" name="telepon" value="{{ auth()->user()->phone }}" class="form-control form-control-sm" placeholder="No. HP">
                                    <button class="btn btn-mk w-100"><i class="bi bi-check-circle me-1"></i>Daftar Event Gratis</button>
                                </form>
                            @elseif($event->registrations_count >= $event->kapasitas)
                                <div class="alert alert-warning border-0 rounded-4 small text-center mb-0">Kuota event sudah penuh. 😢</div>
                            @else
                                <div class="alert alert-secondary border-0 rounded-4 small text-center mb-0">Pendaftaran telah ditutup.</div>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="btn btn-mk w-100"><i class="bi bi-person me-1"></i>Login untuk Daftar</a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
