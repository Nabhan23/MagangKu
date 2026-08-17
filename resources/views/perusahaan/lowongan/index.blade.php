@extends('layouts.app')

@section('title', 'Kelola Lowongan - MagangKu')
@section('page-title', 'Kelola Lowongan')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4" data-aos="fade-up">
        <div class="small text-muted-2">{{ $lowongans->total() }} lowongan</div>
        <a href="{{ route('perusahaan.lowongan.create') }}" class="btn btn-mk"><i class="bi bi-plus-circle me-1"></i>Posting Lowongan</a>
    </div>

    <div class="card card-hover p-4" data-aos="fade-up">
        @forelse($lowongans as $lowongan)
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 py-3 {{ !$loop->last ? 'border-bottom' : '' }}" style="border-color:var(--mk-border) !important;">
                <div class="min-w-0">
                    <div class="fw-bold text-accent">{{ $lowongan->posisi }}</div>
                    <div class="small text-muted-2 d-flex flex-wrap gap-2">
                        <span>{{ $lowongan->jenis_label }}</span>
                        <span><i class="bi bi-geo-alt me-1"></i>{{ $lowongan->lokasi ?: 'Remote' }}</span>
                        <span><i class="bi bi-cash-stack me-1"></i>{{ $lowongan->gaji_label }}</span>
                        <span class="badge rounded-pill text-bg-{{ $lowongan->status_badge[0] }}">{{ $lowongan->status_badge[1] }}</span>
                    </div>
                    <div class="small text-muted-2 mt-1">
                        <span class="me-3"><i class="bi bi-people me-1"></i>{{ $lowongan->lamarans_count }} pelamar</span>
                        <span class="me-3"><i class="bi bi-eye me-1"></i>{{ number_format($lowongan->views) }}x dilihat</span>
                        <span><i class="bi bi-clock me-1"></i>Deadline {{ $lowongan->deadline->translatedFormat('d M Y') }}</span>
                    </div>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('perusahaan.pelamar.index', ['lowongan' => $lowongan->id]) }}" class="btn btn-soft btn-sm"><i class="bi bi-people me-1"></i>Pelamar</a>
                    <a href="{{ route('jobs.show', $lowongan) }}" class="btn btn-soft btn-sm" target="_blank"><i class="bi bi-eye me-1"></i>Lihat</a>
                    <a href="{{ route('perusahaan.lowongan.edit', $lowongan) }}" class="btn btn-outline-mk btn-sm"><i class="bi bi-pencil me-1"></i>Edit</a>
                    <form method="POST" action="{{ route('perusahaan.lowongan.destroy', $lowongan) }}" onsubmit="return confirm('Hapus lowongan ini?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
                    </form>
                </div>
            </div>
        @empty
            <div class="text-center py-5">
                <i class="bi bi-briefcase fs-1 text-muted-2"></i>
                <h6 class="fw-bold mt-3">Belum Ada Lowongan</h6>
                <p class="text-muted-2 small">Posting lowongan pertamamu dan temukan talenta terbaik!</p>
                <a href="{{ route('perusahaan.lowongan.create') }}" class="btn btn-mk mt-2"><i class="bi bi-plus-circle me-1"></i>Posting Lowongan</a>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center mt-4">{{ $lowongans->links() }}</div>
@endsection
