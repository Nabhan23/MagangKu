@extends('layouts.app')

@section('title', 'Notifikasi - MagangKu')
@section('page-title', 'Notifikasi')

@section('content')
    <div class="card card-hover p-4" data-aos="fade-up">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold mb-0"><i class="bi bi-bell me-2 text-mk"></i>Semua Notifikasi</h6>
            <button class="btn btn-soft btn-sm" id="readAllBtn"><i class="bi bi-check2-all me-1"></i>Tandai Semua Dibaca</button>
        </div>

        @forelse($notifications as $notif)
            <div class="d-flex align-items-start gap-3 py-3 {{ !$loop->last ? 'border-bottom' : '' }} {{ $notif->is_read ? 'opacity-75' : '' }}" style="border-color:var(--mk-border) !important;">
                <div class="stat-icon" style="width:44px;height:44px;font-size:1.1rem;background:{{ $notif->is_read ? 'var(--mk-bg-soft)' : 'rgba(79,195,247,.15)' }};color:{{ $notif->is_read ? 'var(--mk-muted)' : 'var(--mk-primary)' }};border-radius:.9rem;flex-shrink:0;">
                    <i class="bi {{ match($notif->type) { 'lamaran' => 'bi-briefcase-fill', 'interview' => 'bi-camera-video-fill', 'event' => 'bi-calendar-event-fill', 'verifikasi' => 'bi-patch-check-fill', 'broadcast' => 'bi-megaphone-fill', default => 'bi-bell-fill' } }}"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                    <div class="fw-semibold small {{ $notif->is_read ? 'text-muted-2' : 'text-accent' }}">{{ $notif->judul }}</div>
                    <p class="small text-muted-2 mb-1">{{ $notif->pesan }}</p>
                    <div class="small text-muted-2" style="font-size:.72rem;">{{ $notif->created_at->diffForHumans() }}</div>
                </div>
                <div class="d-flex flex-column gap-1 align-items-end">
                    @if(!$notif->is_read)
                        <button class="btn btn-soft btn-sm read-btn" data-url="{{ route('pelamar.notifications.read', $notif) }}"><i class="bi bi-check-lg"></i></button>
                    @endif
                    @if($notif->link)
                        <a href="{{ $notif->link }}" class="btn btn-outline-mk btn-sm"><i class="bi bi-box-arrow-up-right"></i></a>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-5">
                <i class="bi bi-bell-slash fs-1 text-muted-2"></i>
                <p class="text-muted-2 small mt-2 mb-0">Tidak ada notifikasi.</p>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center mt-4">{{ $notifications->links() }}</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.read-btn').forEach(btn => {
            btn.addEventListener('click', async () => {
                await mkFetch(btn.dataset.url, { method: 'POST' });
                btn.closest('.d-flex').classList.remove('opacity-75');
                btn.closest('div.d-flex.flex-column').querySelector('.read-btn')?.remove();
                btn.remove();
            });
        });
        document.getElementById('readAllBtn')?.addEventListener('click', async () => {
            await mkFetch('{{ route('pelamar.notifications.readAll') }}', { method: 'POST' });
            location.reload();
        });
    });
</script>
@endpush
