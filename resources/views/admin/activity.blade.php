@extends('layouts.app')

@section('title', 'Aktivitas Log - MagangKu')
@section('page-title', 'Aktivitas Log')

@section('content')
    <div class="card card-hover p-4 mb-4" data-aos="fade-up">
        <form method="GET" action="{{ route('admin.activity.index') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small">Jenis Aksi</label>
                <select name="aksi" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    @foreach($aksiList as $aksi)
                        <option value="{{ $aksi }}" @selected($filters['aksi'] ?? null === $aksi)>{{ $aksi }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small">Pengguna</label>
                <select name="user" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    @foreach($users as $id => $name)
                        <option value="{{ $id }}" @selected(($filters['user'] ?? null) == $id)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small">Tanggal</label>
                <input type="date" name="tanggal" value="{{ $filters['tanggal'] ?? '' }}" class="form-control form-control-sm">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-mk btn-sm"><i class="bi bi-funnel me-1"></i>Filter</button>
                <a href="{{ route('admin.activity.index') }}" class="btn btn-soft btn-sm">Reset</a>
            </div>
        </form>
    </div>

    <div class="card card-hover p-4" data-aos="fade-up">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Pengguna</th>
                        <th>Aksi</th>
                        <th>Detail</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td class="small text-nowrap">{{ $log->created_at->translatedFormat('d M Y H:i:s') }}</td>
                            <td class="small">{{ $log->user?->name ?? 'Sistem' }}</td>
                            <td><span class="badge rounded-pill text-bg-light border">{{ $log->aksi }}</span></td>
                            <td class="small text-muted-2">{{ $log->detail }}</td>
                            <td class="small text-muted-2 text-nowrap">{{ $log->ip_address }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-5 text-muted-2">Belum ada aktivitas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center mt-3">{{ $logs->links() }}</div>
    </div>
@endsection
