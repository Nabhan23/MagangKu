@extends('layouts.app')

@section('title', 'Detail Lamaran - MagangKu')
@section('page-title', 'Tracking Lamaran')

@section('content')
    <nav aria-label="breadcrumb" class="mb-3" data-aos="fade-up">
        <ol class="breadcrumb small mb-0">
            <li class="breadcrumb-item"><a href="{{ route('pelamar.lamaran.index') }}" class="text-mk text-decoration-none">Lamaran Saya</a></li>
            <li class="breadcrumb-item active text-muted-2">{{ $lamaran->lowongan->posisi }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        <div class="col-lg-8">
            {{-- Info lamaran --}}
            <div class="card card-hover p-4 mb-4" data-aos="fade-up">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                    <div>
                        <h5 class="fw-bold mb-1">{{ $lamaran->lowongan->posisi }}</h5>
                        <div class="text-muted-2 small">{{ $lamaran->lowongan->perusahaan->nama_perusahaan }}</div>
                    </div>
                    <span class="badge rounded-pill text-bg-{{ $lamaran->status_color }} fs-6">{{ $lamaran->status_label }}</span>
                </div>
                <hr style="border-color:var(--mk-border);">
                <div class="row text-center g-2 small">
                    <div class="col-6 col-md-3">
                        <div class="text-muted-2">Dilamar</div>
                        <div class="fw-bold">{{ $lamaran->created_at->translatedFormat('d M Y') }}</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-muted-2">Jenis</div>
                        <div class="fw-bold">{{ $lamaran->lowongan->jenis_label }}</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-muted-2">Lokasi</div>
                        <div class="fw-bold">{{ $lamaran->lowongan->lokasi ?: 'Remote' }}</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-muted-2">Gaji</div>
                        <div class="fw-bold">{{ $lamaran->lowongan->gaji_label }}</div>
                    </div>
                </div>
                @if($lamaran->catatan)
                    <hr style="border-color:var(--mk-border);">
                    <div class="small">
                        <div class="fw-semibold mb-1"><i class="bi bi-chat-left-quote me-1 text-mk"></i>Catatan Lamaran</div>
                        <p class="text-muted-2 mb-0">{{ $lamaran->catatan }}</p>
                    </div>
                @endif
                @if($lamaran->file_pendukung)
                    <hr style="border-color:var(--mk-border);">
                    <div class="small">
                        <i class="bi bi-paperclip me-1 text-mk"></i>
                        <a href="{{ asset('storage/' . $lamaran->file_pendukung) }}" target="_blank" class="text-mk fw-semibold">File Pendukung</a>
                    </div>
                @endif
            </div>

            {{-- Timeline tracking --}}
            <div class="card card-hover p-4" data-aos="fade-up">
                <h6 class="fw-bold mb-4"><i class="bi bi-signpost-split me-2 text-mk"></i>Status Lamaran</h6>
                @php
                    $steps = ['menunggu' => ['Menunggu', 'Lamaran diterima perusahaan', 'bi-hourglass-split'],
                              'diproses' => ['Diproses', 'Lamaran sedang ditinjau HRD', 'bi-search'],
                              'psikotes' => ['Psikotes', 'Menjalani tes kemampuan', 'bi-journal-check'],
                              'interview' => ['Interview', 'Sesi wawancara dengan tim', 'bi-camera-video'],
                              'diterima' => ['Diterima', 'Selamat! Anda diterima 🎉', 'bi-trophy']];
                    $currentIdx = \App\Models\Lamaran::URUTAN[$lamaran->status];
                    $isRejected = $lamaran->status === 'ditolak';
                @endphp

                @if($isRejected)
                    <div class="alert alert-danger border-0 rounded-4">
                        <i class="bi bi-x-circle-fill me-1"></i>
                        <strong>Lamaran Anda ditolak.</strong> Jangan menyerah, masih banyak peluang lainnya!
                    </div>
                @else
                    <div class="timeline">
                        @foreach($steps as $key => [$label, $desc, $icon])
                            @php
                                $stepIdx = \App\Models\Lamaran::URUTAN[$key];
                                $done = $currentIdx > $stepIdx || ($lamaran->status === $key);
                                $current = $lamaran->status === $key;
                            @endphp
                            <div class="timeline-item {{ $done ? 'done' : '' }} {{ $current ? 'current' : '' }}">
                                <span class="timeline-dot"></span>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi {{ $icon }} text-mk"></i>
                                    <span class="fw-bold {{ $current ? 'text-mk' : ($done ? '' : 'text-muted-2') }}">{{ $label }}</span>
                                    @if($current)
                                        <span class="badge rounded-pill text-bg-primary" style="font-size:.65rem;">SEDANG BERLANGSUNG</span>
                                    @endif
                                </div>
                                <div class="small text-muted-2">{{ $desc }}</div>
                            </div>
                        @endforeach
                    </div>

                    @if($lamaran->interview)
                        <div class="alert alert-info border-0 rounded-4 mt-3">
                            <div class="fw-semibold mb-1"><i class="bi bi-camera-video-fill me-1"></i>Jadwal Interview</div>
                            <div class="small">
                                {{ $lamaran->interview->tanggal->translatedFormat('l, d F Y') }} pukul {{ $lamaran->interview->waktu }} WIB
                                ({{ $lamaran->interview->tipe === 'online' ? 'Online' : 'Offline' }})
                            </div>
                            @if($lamaran->interview->link)
                                <div class="small mt-1">Link: <a href="{{ $lamaran->interview->link }}" target="_blank" class="fw-semibold">{{ $lamaran->interview->link }}</a></div>
                            @endif
                            @if($lamaran->interview->lokasi)
                                <div class="small mt-1">Lokasi: {{ $lamaran->interview->lokasi }}</div>
                            @endif
                            @if($lamaran->interview->catatan)
                                <div class="small mt-1">Catatan: {{ $lamaran->interview->catatan }}</div>
                            @endif
                            @if($lamaran->interview->kode)
                                <div class="d-flex align-items-center gap-3 mt-2 pt-2 border-top" style="border-color:rgba(2,136,209,.15) !important;">
                                    <div class="rounded-2 p-1 bg-white">{!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(70)->generate($lamaran->interview->publicUrl()) !!}</div>
                                    <div class="small">
                                        <div class="fw-semibold">QR Code Interview</div>
                                        <div class="text-muted-2" style="font-size:.75rem;">Tunjukkan QR ini saat interview berlangsung.</div>
                                        <a href="{{ $lamaran->interview->publicUrl() }}" target="_blank" class="fw-semibold small">Lihat undangan →</a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                @endif
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card card-hover p-4 mb-4" data-aos="fade-up">
                <h6 class="fw-bold mb-3"><i class="bi bi-briefcase me-2 text-mk"></i>Info Lowongan</h6>
                <ul class="list-unstyled small d-grid gap-2 mb-3 text-muted-2">
                    <li><i class="bi bi-tag me-2 text-mk"></i>{{ $lamaran->lowongan->category?->nama ?? 'Umum' }}</li>
                    <li><i class="bi bi-briefcase me-2 text-mk"></i>{{ $lamaran->lowongan->jenis_label }}</li>
                    <li><i class="bi bi-geo-alt me-2 text-mk"></i>{{ $lamaran->lowongan->lokasi ?: 'Remote' }}</li>
                    <li><i class="bi bi-cash-stack me-2 text-mk"></i>{{ $lamaran->lowongan->gaji_label }}</li>
                    <li><i class="bi bi-clock me-2 text-mk"></i>Deadline {{ $lamaran->lowongan->deadline->translatedFormat('d M Y') }}</li>
                </ul>
                <a href="{{ route('jobs.show', $lamaran->lowongan) }}" class="btn btn-soft btn-sm w-100">Lihat Detail Lowongan</a>
                <a href="{{ route('pelamar.chat.show', $lamaran) }}" class="btn btn-outline-mk btn-sm w-100"><i class="bi bi-chat-dots me-1"></i>Chat dengan Perusahaan</a>
            </div>

            @if($lamaran->status === 'diterima' && $lamaran->offer_letter_path)
                <div class="card card-hover p-4 mb-4 border-success" style="border-color:#22C55E !important;" data-aos="fade-up">
                    <h6 class="fw-bold mb-2"><i class="bi bi-trophy me-2 text-mk"></i>Selamat! Anda Diterima 🎉</h6>
                    <p class="small text-muted-2 mb-3">Surat penerimaan resmi dari perusahaan sudah tersedia. Unduh untuk keperluan administrasi Anda.</p>
                    <a href="{{ route('offer-letter.download', $lamaran) }}" class="btn btn-success w-100"><i class="bi bi-file-earmark-pdf me-1"></i>Unduh Surat Penerimaan</a>
                </div>
            @endif

            @if($lamaran->feedback)
                <div class="card card-hover p-4 mb-4" data-aos="fade-up">
                    <h6 class="fw-bold mb-3"><i class="bi bi-chat-heart me-2 text-mk"></i>Feedback dari Perusahaan</h6>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="fw-bold fs-5" style="color:#F59E0B;">{!! str_repeat('★', $lamaran->feedback->rating) !!}</span>
                        <span class="text-muted-2 small">{{ $lamaran->feedback->rating }}/5 • {{ $lamaran->feedback->updated_at->translatedFormat('d M Y') }}</span>
                    </div>
                    @if($lamaran->feedback->feedback)
                        <p class="small text-muted-2 mb-0" style="font-style:italic;">"{{ $lamaran->feedback->feedback }}"</p>
                    @else
                        <p class="small text-muted-2 mb-0">Perusahaan telah memberikan penilaian atas lamaran Anda.</p>
                    @endif
                </div>
            @endif

            <div class="card card-hover p-4" data-aos="fade-up">
                <h6 class="fw-bold mb-3"><i class="bi bi-lightbulb me-2 text-mk"></i>Tips</h6>
                <p class="small text-muted-2 mb-2">
                    <i class="bi bi-check2-circle text-mk me-1"></i>Pantau email dan notifikasi secara rutin.
                </p>
                <p class="small text-muted-2 mb-2">
                    <i class="bi bi-check2-circle text-mk me-1"></i>Siapkan dokumen dan portofolio sebelum interview.
                </p>
                <p class="small text-muted-2 mb-0">
                    <i class="bi bi-check2-circle text-mk me-1"></i>Pelajari profil perusahaan sebelum interview.
                </p>
            </div>
        </div>
    </div>
@endsection
