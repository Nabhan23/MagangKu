@extends('layouts.app')

@section('title', 'Broadcast Notifikasi - MagangKu')
@section('page-title', 'Broadcast Notifikasi')

@section('content')
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card card-hover p-4" data-aos="fade-up">
                <h6 class="fw-bold mb-3"><i class="bi bi-megaphone me-2 text-mk"></i>Kirim Notifikasi Massal</h6>
                <form method="POST" action="{{ route('admin.notifications.broadcast') }}" class="d-grid gap-3">
                    @csrf
                    <div>
                        <label class="form-label">Target Pengguna</label>
                        <select name="target" class="form-select">
                            <option value="semua">Semua Pengguna</option>
                            <option value="pelamar">Hanya Pelamar</option>
                            <option value="perusahaan">Hanya Perusahaan</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Judul</label>
                        <input type="text" name="judul" class="form-control" placeholder="Pengumuman Penting!" required>
                    </div>
                    <div>
                        <label class="form-label">Pesan</label>
                        <textarea name="pesan" rows="4" class="form-control" placeholder="Tulis pesan notifikasi..." required></textarea>
                    </div>
                    <button class="btn btn-mk btn-lg"><i class="bi bi-send me-1"></i>Kirim Notifikasi</button>
                </form>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card card-hover p-4" data-aos="fade-up">
                <h6 class="fw-bold mb-3"><i class="bi bi-clock-history me-2 text-mk"></i>Notifikasi Terakhir</h6>
                @forelse($recent as $notif)
                    <div class="d-flex align-items-start gap-2 py-2 {{ !$loop->last ? 'border-bottom' : '' }}" style="border-color:var(--mk-border) !important;">
                        <i class="bi bi-bell-fill text-mk mt-1"></i>
                        <div class="min-w-0">
                            <div class="fw-semibold small">{{ $notif->judul }}</div>
                            <div class="small text-muted-2 text-truncate">{{ $notif->pesan }}</div>
                            <div class="small text-muted-2" style="font-size:.7rem;">
                                → {{ $notif->user?->name ?? 'Hapus' }} • {{ $notif->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted-2 small text-center py-5 mb-0">Belum ada notifikasi terkirim.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
