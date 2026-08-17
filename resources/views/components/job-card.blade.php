@props(['job'])

@php
    $colors = ['#4FC3F7', '#26A69A', '#5C6BC0', '#FFA726', '#EC407A', '#66BB6A'];
    $logoUrl = $job->perusahaan?->logo_url;
    $initial = strtoupper(substr($job->perusahaan?->nama_perusahaan ?? '?', 0, 1));
    $color = $colors[$job->perusahaan?->id % count($colors)] ?? $colors[0];
    $jenisBadge = match($job->jenis) {
        'magang' => ['info', 'Magang'],
        'full_time' => ['success', 'Full Time'],
        'part_time' => ['warning', 'Part Time'],
        'freelance' => ['secondary', 'Freelance'],
        'remote' => ['primary', 'Remote'],
        default => ['secondary', $job->jenis_label],
    };
    $saved = auth()->check() && $job->savedBy->contains('id', auth()->id());
@endphp

<div class="card card-hover job-card h-100">
    <div class="card-body p-4">
        @auth
            <button class="bookmark-btn {{ $saved ? 'saved' : '' }}" data-save-url="{{ route('pelamar.saved.toggle', $job) }}" title="Simpan lowongan">
                <i class="bi {{ $saved ? 'bi-bookmark-fill' : 'bi-bookmark' }}"></i>
            </button>
        @endauth

        <div class="d-flex align-items-center gap-3 mb-3">
            <div class="job-logo" style="background:{{ $color }};">
                @if($logoUrl)
                    <img src="{{ $logoUrl }}" alt="{{ $job->perusahaan->nama_perusahaan }}" width="58" height="58" style="object-fit:cover;border-radius:1rem;">
                @else
                    {{ $initial }}
                @endif
            </div>
            <div class="min-w-0">
                <div class="text-muted-2 small text-truncate">{{ $job->perusahaan?->nama_perusahaan }}</div>
                <span class="job-type" style="background:rgba(79,195,247,.12);color:var(--mk-primary);">{{ $jenisBadge[1] }}</span>
            </div>
        </div>

        <h6 class="fw-bold mb-2">
            <a href="{{ route('jobs.show', $job) }}" class="text-decoration-none text-accent stretched-link">{{ $job->posisi }}</a>
        </h6>

        <div class="d-flex flex-wrap gap-2 small text-muted-2 mb-3">
            <span><i class="bi bi-geo-alt me-1 text-mk"></i>{{ $job->lokasi ?: 'Remote' }}</span>
            <span><i class="bi bi-cash-stack me-1 text-mk"></i>{{ $job->gaji_label }}</span>
        </div>

        <div class="d-flex align-items-center justify-content-between small">
            <span class="text-muted-2">
                <i class="bi bi-clock me-1 text-mk"></i>
                @if($job->deadline < now()->toDateString())
                    <span class="text-danger">Tutup</span>
                @else
                    Deadline {{ $job->deadline->translatedFormat('d M Y') }}
                @endif
            </span>
            <a href="{{ route('jobs.show', $job) }}" class="btn btn-soft btn-sm">Detail <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
    </div>
</div>
