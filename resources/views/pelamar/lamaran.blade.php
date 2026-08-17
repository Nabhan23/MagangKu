@extends('layouts.app')

@section('title', 'Tracking Lamaran - MagangKu')
@section('page-title', 'Tracking Lamaran')

@section('content')
    <div class="row g-4">
        {{-- Status legend --}}
        <div class="col-12" data-aos="fade-up">
            <div class="card card-hover p-3">
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <span class="small text-muted-2 me-2 fw-semibold">Status:</span>
                    @foreach(\App\Models\Lamaran::STATUS as $key => [$label, $color])
                        <span class="badge rounded-pill text-bg-{{ $color }}">{{ $label }}</span>
                    @endforeach
                </div>
            </div>
        </div>

        @forelse($lamarans as $lamaran)
            <div class="col-12" data-aos="fade-up" data-aos-delay="{{ $loop->index * 60 }}">
                <div class="card card-hover p-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-3 min-w-0">
                            @php
                                $colors = ['#4FC3F7', '#26A69A', '#5C6BC0', '#FFA726', '#EC407A'];
                                $initial = strtoupper(substr($lamaran->lowongan->perusahaan->nama_perusahaan, 0, 1));
                            @endphp
                            <div class="avatar" style="width:52px;height:52px;border-radius:1rem;background:{{ $colors[$lamaran->lowongan_id % count($colors)] }};">
                                @if($lamaran->lowongan->perusahaan->logo_url)
                                    <img src="{{ $lamaran->lowongan->perusahaan->logo_url }}" width="52" height="52" style="object-fit:cover;border-radius:1rem;">
                                @else
                                    {{ $initial }}
                                @endif
                            </div>
                            <div class="min-w-0">
                                <div class="fw-bold text-accent">{{ $lamaran->lowongan->posisi }}</div>
                                <div class="small text-muted-2">
                                    {{ $lamaran->lowongan->perusahaan->nama_perusahaan }} • Dilamar {{ $lamaran->created_at->translatedFormat('d M Y') }}
                                </div>
                                @if($lamaran->interview)
                                    <div class="small text-mk fw-semibold mt-1">
                                        <i class="bi bi-camera-video me-1"></i>
                                        Interview {{ $lamaran->interview->tipe === 'online' ? 'Online' : 'Offline' }}: {{ $lamaran->interview->tanggal->translatedFormat('d M Y') }} {{ $lamaran->interview->waktu }} WIB
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="text-end">
                            <span class="badge rounded-pill text-bg-{{ $lamaran->status_color }} mb-2">{{ $lamaran->status_label }}</span>
                            <div>
                                <a href="{{ route('pelamar.lamaran.show', $lamaran) }}" class="btn btn-soft btn-sm">Tracking Detail</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5" data-aos="fade-up">
                <i class="bi bi-briefcase fs-1 text-muted-2"></i>
                <h6 class="fw-bold mt-3">Belum Ada Lamaran</h6>
                <p class="text-muted-2 small">Mulai cari lowongan impianmu sekarang!</p>
                <a href="{{ route('jobs.index') }}" class="btn btn-mk mt-2"><i class="bi bi-search me-1"></i>Cari Lowongan</a>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $lamarans->links() }}
    </div>
@endsection
