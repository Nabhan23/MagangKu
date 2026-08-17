@extends('layouts.app')

@section('title', 'Kalender Interview - MagangKu')
@section('page-title', 'Kalender Interview')

@section('content')
    <div class="card card-hover p-4 mb-4" data-aos="fade-up">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h6 class="fw-bold mb-0">{{ $month->translatedFormat('F Y') }}</h6>
                <div class="small text-muted-2">{{ $total }} interview dijadwalkan bulan ini</div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('perusahaan.interview.calendar', ['bulan' => $month->copy()->subMonth()->format('Y-m')]) }}" class="btn btn-soft btn-sm"><i class="bi bi-chevron-left"></i> Sebelumnya</a>
                <a href="{{ route('perusahaan.interview.calendar') }}" class="btn btn-outline-mk btn-sm">Bulan Ini</a>
                <a href="{{ route('perusahaan.interview.calendar', ['bulan' => $month->copy()->addMonth()->format('Y-m')]) }}" class="btn btn-soft btn-sm">Berikutnya <i class="bi bi-chevron-right"></i></a>
            </div>
        </div>
    </div>

    <div class="card card-hover p-4" data-aos="fade-up">
        <div class="row g-1 text-center small fw-semibold mb-2">
            @foreach(['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'] as $d)
                <div class="col text-muted-2">{{ $d }}</div>
            @endforeach
        </div>
        <div class="row g-1">
            @foreach($days as $day)
                <div class="col-1" style="flex:0 0 14.2857%;max-width:14.2857%;">
                    <div class="rounded-3 p-2 mb-1 {{ $day['inMonth'] ? '' : 'opacity-25' }}" style="min-height:90px;background:var(--mk-soft);">
                        <div class="small fw-semibold mb-1">{{ $day['date']->day }}</div>
                        @foreach($day['interviews'] as $interview)
                            <a href="{{ route('perusahaan.pelamar.show', $interview->lamaran_id) }}" class="d-block text-decoration-none small rounded-2 px-1 py-1 mb-1" style="background:var(--mk-gradient);color:#fff;font-size:.65rem;line-height:1.2;" title="{{ $interview->lamaran->pelamar->nama }} - {{ $interview->waktu }}">
                                <i class="bi bi-camera-video me-1"></i>{{ $interview->waktu }}<br>{{ $interview->lamaran->pelamar->nama }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
