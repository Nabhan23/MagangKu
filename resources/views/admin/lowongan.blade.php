@extends('layouts.app')

@section('title', 'Kelola Lowongan - MagangKu')
@section('page-title', 'Kelola Lowongan')

@section('content')
    <form method="GET" class="card card-hover p-3 mb-4" data-aos="fade-up">
        <div class="row g-2 align-items-end">
            <div class="col-md-6">
                <label class="form-label small mb-1">Cari</label>
                <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="Posisi lowongan...">
            </div>
            <div class="col-md-4">
                <label class="form-label small mb-1">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
                    <option value="ditutup" @selected(request('status') === 'ditutup')>Ditutup</option>
                    <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                </select>
            </div>
            <div class="col-md-2 d-grid">
                <button class="btn btn-mk btn-sm"><i class="bi bi-search me-1"></i>Cari</button>
            </div>
        </div>
    </form>

    <div class="card card-hover p-4" data-aos="fade-up">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Lowongan</th>
                        <th>Perusahaan</th>
                        <th>Jenis</th>
                        <th>Pelamar</th>
                        <th>Deadline</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lowongans as $lowongan)
                        <tr>
                            <td>
                                <div class="fw-semibold small">{{ $lowongan->posisi }}</div>
                                <div class="small text-muted-2">{{ $lowongan->views }}x dilihat</div>
                            </td>
                            <td class="small">{{ $lowongan->perusahaan->nama_perusahaan }}</td>
                            <td><span class="badge rounded-pill text-bg-info">{{ $lowongan->jenis_label }}</span></td>
                            <td class="small"><i class="bi bi-people me-1 text-mk"></i>{{ $lowongan->lamarans_count }}</td>
                            <td class="small text-muted-2">{{ $lowongan->deadline->translatedFormat('d M Y') }}</td>
                            <td><span class="badge rounded-pill text-bg-{{ $lowongan->status_badge[0] }}">{{ $lowongan->status_badge[1] }}</span></td>
                            <td class="text-end">
                                <a href="{{ route('jobs.show', $lowongan) }}" target="_blank" class="btn btn-soft btn-sm"><i class="bi bi-eye"></i></a>
                                <form method="POST" action="{{ route('admin.lowongan.toggle', $lowongan) }}" class="d-inline">
                                    @csrf
                                    <button class="btn btn-{{ $lowongan->status === 'aktif' ? 'warning' : 'success' }} btn-sm">
                                        <i class="bi {{ $lowongan->status === 'aktif' ? 'bi-pause-circle' : 'bi-play-circle' }}"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.lowongan.destroy', $lowongan) }}" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-outline-danger btn-sm" data-confirm="Hapus lowongan &quot;{{ $lowongan->posisi }}&quot;? Aksi ini tidak bisa dibatalkan."><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-5 text-muted-2">Tidak ada lowongan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-4">{{ $lowongans->links() }}</div>

    @include('partials.confirm-modal')
@endsection
