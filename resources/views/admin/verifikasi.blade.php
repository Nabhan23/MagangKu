@extends('layouts.app')

@section('title', 'Verifikasi Perusahaan - MagangKu')
@section('page-title', 'Verifikasi Perusahaan')

@section('content')
    <ul class="nav nav-pills mb-4 gap-2" data-aos="fade-up">
        @foreach(['menunggu' => 'Menunggu', 'verified' => 'Terverifikasi', 'rejected' => 'Ditolak', 'semua' => 'Semua'] as $key => $label)
            <li class="nav-item">
                <a class="nav-link rounded-pill px-3 {{ $activeStatus === $key ? 'active bg-mk-gradient text-white' : 'bg-soft text-accent' }}" href="{{ route('admin.verifikasi.index', ['status' => $key]) }}">
                    {{ $label }}
                    @if(isset($counts[$key]))
                        <span class="badge rounded-pill text-bg-light ms-1">{{ $counts[$key] }}</span>
                    @endif
                </a>
            </li>
        @endforeach
    </ul>

    <div class="row g-4">
        @forelse($perusahaan as $item)
            <div class="col-md-6 col-xl-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                <div class="card card-hover h-100 p-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        @php $initial = strtoupper(substr($item->nama_perusahaan, 0, 1)); @endphp
                        @if($item->logo_url)
                            <img src="{{ $item->logo_url }}" class="rounded-4" width="56" height="56" style="object-fit:cover;">
                        @else
                            <div class="avatar" style="width:56px;height:56px;border-radius:1rem;background:var(--mk-gradient);font-size:1.3rem;">{{ $initial }}</div>
                        @endif
                        <div class="min-w-0">
                            <div class="fw-bold text-truncate">{{ $item->nama_perusahaan }}</div>
                            <div class="small text-muted-2">{{ $item->industri ?: '-' }} • {{ $item->lokasi ?: '-' }}</div>
                        </div>
                    </div>

                    <div class="small text-muted-2 d-grid gap-1 mb-3">
                        <div><i class="bi bi-person me-2 text-mk"></i>{{ $item->user->email }}</div>
                        <div><i class="bi bi-telephone me-2 text-mk"></i>{{ $item->telepon ?: '-' }}</div>
                        <div><i class="bi bi-calendar me-2 text-mk"></i>Daftar {{ $item->created_at->translatedFormat('d M Y') }}</div>
                        <div class="mt-1">
                            <span class="badge rounded-pill text-bg-{{ $item->status_verifikasi_badge[0] }}">{{ $item->status_verifikasi_badge[1] }}</span>
                        </div>
                    </div>

                    @if($item->dokumen_verifikasi)
                        <a href="{{ route('admin.verifikasi.dokumen', $item) }}" target="_blank" class="btn btn-soft btn-sm w-100 mb-3">
                            <i class="bi bi-file-earmark-check me-1"></i>Lihat Dokumen Verifikasi
                        </a>
                    @else
                        <div class="alert alert-warning border-0 rounded-4 small py-2 mb-3">Belum mengunggah dokumen.</div>
                    @endif

                    @if($item->pesan_verifikasi)
                        <div class="alert alert-info border-0 rounded-4 small py-2 mb-3">
                            <i class="bi bi-chat-left-text me-1"></i>{{ $item->pesan_verifikasi }}
                        </div>
                    @endif

                    @if($item->status_verifikasi === 'menunggu')
                        <form method="POST" action="{{ route('admin.verifikasi.update', $item) }}" class="d-grid gap-2">
                            @csrf
                            <input type="hidden" name="status" value="verified">
                            <button class="btn btn-success btn-sm"><i class="bi bi-check-lg me-1"></i>Verifikasi</button>
                        </form>
                        <form method="POST" action="{{ route('admin.verifikasi.update', $item) }}" class="d-grid gap-2 mt-2">
                            @csrf
                            <input type="hidden" name="status" value="rejected">
                            <div class="input-group input-group-sm">
                                <input type="text" name="pesan" class="form-control" placeholder="Alasan penolakan (opsional)">
                                <button class="btn btn-outline-danger"><i class="bi bi-x-lg"></i></button>
                            </div>
                        </form>
                    @elseif($item->status_verifikasi === 'rejected')
                        <form method="POST" action="{{ route('admin.verifikasi.update', $item) }}">
                            @csrf
                            <input type="hidden" name="status" value="verified">
                            <button class="btn btn-success btn-sm w-100"><i class="bi bi-arrow-counterclockwise me-1"></i>Verifikasi Ulang</button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="bi bi-patch-check fs-1 text-muted-2"></i>
                <h6 class="fw-bold mt-3">Tidak Ada Data</h6>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center mt-4">{{ $perusahaan->links() }}</div>
@endsection
