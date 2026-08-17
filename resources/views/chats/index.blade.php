@extends('layouts.app')

@section('title', $title . ' - MagangKu')
@section('page-title', $title)

@section('content')
    <div class="card card-hover p-4" data-aos="fade-up">
        @forelse($conversations as $l)
            @php
                $isCompany = auth()->user()->isPerusahaan();
                $title = $isCompany ? $l->pelamar->nama : $l->lowongan->posisi;
                $sub = $isCompany
                    ? $l->lowongan->posisi . ' • ' . $l->lowongan->perusahaan->nama_perusahaan
                    : $l->lowongan->perusahaan->nama_perusahaan;
                $link = $isCompany ? route('perusahaan.chat.show', $l) : route('pelamar.chat.show', $l);
                $last = $l->messages->first();
            @endphp
            <a href="{{ $link }}" class="text-decoration-none">
                <div class="d-flex align-items-center gap-3 py-3 {{ !$loop->last ? 'border-bottom' : '' }}" style="border-color:var(--mk-border) !important;">
                    <div class="position-relative">
                        @if($isCompany && $l->pelamar->user->avatar_url)
                            <img src="{{ $l->pelamar->user->avatar_url }}" class="rounded-circle" width="48" height="48" style="object-fit:cover;">
                        @elseif(!$isCompany && $l->lowongan->perusahaan->logo_url)
                            <img src="{{ $l->lowongan->perusahaan->logo_url }}" class="rounded-circle bg-white" width="48" height="48" style="object-fit:cover;">
                        @else
                            <div class="avatar" style="width:48px;height:48px;font-size:1rem;">{{ strtoupper(substr($title, 0, 1)) }}</div>
                        @endif
                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <div class="d-flex justify-content-between align-items-center gap-2">
                            <div class="fw-semibold small text-truncate text-accent">{{ $title }}</div>
                            <div class="small text-muted-2 text-nowrap" style="font-size:.7rem;">{{ $last ? $last->created_at->translatedFormat('d M, H:i') : '' }}</div>
                        </div>
                        <div class="small text-muted-2 text-truncate">{{ $sub }}</div>
                        <div class="small text-truncate" style="font-size:.8rem;{{ $l->unread_count ? 'color:var(--mk-text);font-weight:600;' : 'color:var(--mk-muted);' }}">
                            @if($last && $last->sender_id === auth()->id())<i class="bi bi-arrow-return-right me-1"></i>@endif
                            {{ $last?->pesan ?? 'Belum ada pesan' }}
                        </div>
                    </div>
                    @if($l->unread_count > 0)
                        <span class="badge rounded-pill text-bg-danger">{{ $l->unread_count }}</span>
                    @endif
                </div>
            </a>
        @empty
            <div class="text-center py-5">
                <i class="bi bi-chat-dots fs-1 text-muted-2"></i>
                <p class="text-muted-2 small mt-2 mb-0">{{ $emptyText }}</p>
            </div>
        @endforelse
    </div>
@endsection
