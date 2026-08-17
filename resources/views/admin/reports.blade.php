@extends('layouts.app')

@section('title', 'Laporan Sistem - MagangKu')
@section('page-title', 'Laporan Sistem')

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div class="text-muted-2 small"><i class="bi bi-info-circle me-1 text-mk"></i>Laporan ringkasan sistem MagangKu. Unduh dalam format PDF, Excel, atau CSV.</div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.reports.export', ['format' => 'pdf']) }}" class="btn btn-outline-mk btn-sm"><i class="bi bi-file-earmark-pdf me-1"></i>PDF</a>
            <a href="{{ route('admin.reports.export', ['format' => 'excel']) }}" class="btn btn-outline-mk btn-sm"><i class="bi bi-file-earmark-excel me-1"></i>Excel</a>
            <a href="{{ route('admin.reports.export', ['format' => 'csv']) }}" class="btn btn-mk btn-sm"><i class="bi bi-download me-1"></i>CSV</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            {{-- Ringkasan --}}
            <div class="card card-hover p-4 mb-4" data-aos="fade-up">
                <h6 class="fw-bold mb-3"><i class="bi bi-clipboard-data me-2 text-mk"></i>Ringkasan Sistem</h6>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <tbody>
                            @foreach([
                                ['Total Pengguna', number_format($data['totalUsers'])],
                                ['Pelamar', number_format($data['totalPelamar'])],
                                ['Perusahaan', number_format($data['totalPerusahaan'])],
                                ['Total Lowongan', number_format($data['totalLowongan'])],
                                ['Total Lamaran', number_format($data['totalLamaran'])],
                            ] as [$label, $value])
                                <tr>
                                    <td class="text-muted-2">{{ $label }}</td>
                                    <td class="fw-bold text-end">{{ $value }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Status lamaran --}}
            <div class="card card-hover p-4 mb-4" data-aos="fade-up">
                <h6 class="fw-bold mb-3"><i class="bi bi-pie-chart me-2 text-mk"></i>Distribusi Status Lamaran</h6>
                @php
                    $total = $data['lamaranPerStatus']->sum();
                    $colors = ['secondary', 'info', 'warning', 'primary', 'success', 'danger'];
                @endphp
                <div class="d-flex gap-2 mb-4">
                    @foreach($data['lamaranPerStatus'] as $key => $count)
                        @if($total > 0)
                            <div class="rounded-3" style="flex:{{ $count }};height:14px;background:var(--bs-{{ $colors[array_search($key, array_keys($data['lamaranPerStatus']->all()))] }});"></div>
                        @endif
                    @endforeach
                </div>
                <div class="row g-2">
                    @foreach($data['lamaranPerStatus'] as $key => $count)
                        <div class="col-6 col-md-4 d-flex align-items-center gap-2 small">
                            <span class="d-inline-block rounded-3" style="width:12px;height:12px;background:var(--bs-{{ $colors[$loop->index] }});"></span>
                            {{ \App\Models\Lamaran::STATUS[$key][0] }}: <strong class="ms-1">{{ $count }}</strong>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Lamaran per bulan --}}
            <div class="card card-hover p-4" data-aos="fade-up">
                <h6 class="fw-bold mb-4"><i class="bi bi-bar-chart me-2 text-mk"></i>Lamaran per Bulan (12 Bulan)</h6>
                <div class="d-flex align-items-end gap-1" style="height:180px;">
                    @php $max = max($data['lamaranPerBulan']->max('jumlah'), 1); @endphp
                    @foreach($data['lamaranPerBulan'] as $b)
                        <div class="flex-grow-1 d-flex flex-column align-items-center gap-1">
                            <div class="small text-muted-2" style="font-size:.65rem;">{{ $b['jumlah'] }}</div>
                            <div class="w-100 rounded-2" style="height:{{ max(5, round(($b['jumlah'] / $max) * 100)) }}%;background:var(--mk-gradient);min-height:5px;" title="{{ $b['date']->translatedFormat('M Y') }}"></div>
                            <div class="small text-muted-2" style="font-size:.6rem;">{{ $b['date']->translatedFormat('M') }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            {{-- Top perusahaan --}}
            <div class="card card-hover p-4 mb-4" data-aos="fade-up">
                <h6 class="fw-bold mb-3"><i class="bi bi-trophy me-2 text-mk"></i>Perusahaan Paling Aktif</h6>
                @forelse($data['topPerusahaan'] as $i => $perusahaan)
                    <div class="d-flex align-items-center gap-3 py-2 {{ !$loop->last ? 'border-bottom' : '' }}" style="border-color:var(--mk-border) !important;">
                        <div class="fw-bold fs-5 {{ $i === 0 ? 'text-warning' : 'text-muted-2' }}">#{{ $i + 1 }}</div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="fw-semibold small text-truncate">{{ $perusahaan->nama_perusahaan }}</div>
                            <div class="small text-muted-2">{{ $perusahaan->industri }}</div>
                        </div>
                        <span class="badge rounded-pill text-bg-info">{{ $perusahaan->lowongan_aktif_count }} lowongan</span>
                    </div>
                @empty
                    <p class="text-muted-2 small text-center py-3 mb-0">Belum ada data.</p>
                @endforelse
            </div>

            {{-- Log aktivitas --}}
            <div class="card card-hover p-4" data-aos="fade-up">
                <h6 class="fw-bold mb-3"><i class="bi bi-activity me-2 text-mk"></i>Log Aktivitas Terbaru</h6>
                <div style="max-height:400px;overflow-y:auto;">
                    @forelse($data['aktivitas'] as $log)
                        <div class="small py-2 {{ !$loop->last ? 'border-bottom' : '' }}" style="border-color:var(--mk-border) !important;">
                            <div class="fw-semibold small">{{ $log->aksi }}</div>
                            <div class="text-muted-2 text-truncate" style="font-size:.72rem;">
                                {{ $log->user?->name ?? 'Sistem' }} • {{ $log->created_at->translatedFormat('d M Y H:i') }} • {{ $log->ip_address }}
                            </div>
                        </div>
                    @empty
                        <p class="text-muted-2 small text-center py-3 mb-0">Belum ada aktivitas.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
