@extends('layouts.app')

@section('title', 'Detail Pelamar - MagangKu')
@section('page-title', 'Detail Pelamar')

@section('content')
    <nav aria-label="breadcrumb" class="mb-3" data-aos="fade-up">
        <ol class="breadcrumb small mb-0">
            <li class="breadcrumb-item"><a href="{{ route('perusahaan.pelamar.index') }}" class="text-mk text-decoration-none">Kelola Pelamar</a></li>
            <li class="breadcrumb-item active text-muted-2">{{ $lamaran->pelamar->nama }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card card-hover p-4 text-center mb-4" data-aos="fade-up">
                @if($lamaran->pelamar->foto_url)
                    <img src="{{ $lamaran->pelamar->foto_url }}" class="rounded-circle mx-auto mb-3" width="100" height="100" style="object-fit:cover;border:4px solid var(--mk-secondary);">
                @else
                    <div class="avatar avatar-lg mx-auto mb-3" style="background:var(--mk-gradient);width:100px;height:100px;font-size:2.2rem;">{{ strtoupper(substr($lamaran->pelamar->nama ?? '?', 0, 1)) }}</div>
                @endif
                <h6 class="fw-bold">{{ $lamaran->pelamar->nama }}</h6>
                <div class="small text-muted-2">
                    <i class="bi bi-mortarboard me-1 text-mk"></i>{{ $lamaran->pelamar->universitas ?: '-' }} @if($lamaran->pelamar->jurusan) • {{ $lamaran->pelamar->jurusan }} @endif
                </div>
                <div class="small text-muted-2"><i class="bi bi-geo-alt me-1 text-mk"></i>{{ $lamaran->pelamar->lokasi ?: '-' }}</div>
                <div class="small text-muted-2"><i class="bi bi-envelope me-1 text-mk"></i>{{ $lamaran->pelamar->user->email }}</div>

                <div class="d-grid gap-2 mt-3">
                    @if($lamaran->pelamar->cv_path)
                        <a href="{{ route('perusahaan.pelamar.cv', $lamaran) }}" class="btn btn-mk"><i class="bi bi-download me-1"></i>Download CV</a>
                    @endif
                    @if($lamaran->pelamar->is_portfolio_public)
                        <a href="{{ route('portfolio.public', $lamaran->pelamar->user_id) }}" target="_blank" class="btn btn-soft"><i class="bi bi-person-workspace me-1"></i>Lihat Portofolio</a>
                        <a href="{{ route('perusahaan.chat.show', $lamaran) }}" class="btn btn-outline-mk"><i class="bi bi-chat-dots me-1"></i>Chat dengan Pelamar</a>
                    @endif
                    @if($lamaran->status === 'diterima')
                        @if($lamaran->offer_letter_path)
                            <a href="{{ route('offer-letter.download', $lamaran) }}" class="btn btn-success"><i class="bi bi-file-earmark-pdf me-1"></i>Unduh Surat Penerimaan</a>
                        @else
                            <form method="POST" action="{{ route('perusahaan.pelamar.offerLetter', $lamaran) }}" class="d-grid">
                                @csrf
                                <button type="submit" class="btn btn-success"><i class="bi bi-file-earmark-arrow-up me-1"></i>Generate Surat Penerimaan</button>
                            </form>
                        @endif
                    @endif
                </div>

                <hr style="border-color:var(--mk-border);">
                <div class="text-start small d-grid gap-1 text-muted-2">
                    <div><i class="bi bi-briefcase me-2 text-mk"></i>Posisi: <strong class="text-accent">{{ $lamaran->lowongan->posisi }}</strong></div>
                    <div><i class="bi bi-calendar3 me-2 text-mk"></i>Dilamar: {{ $lamaran->created_at->translatedFormat('d M Y') }}</div>
                    <div><i class="bi bi-tag me-2 text-mk"></i>Status: <span class="badge rounded-pill text-bg-{{ $lamaran->status_color }}">{{ $lamaran->status_label }}</span></div>
                </div>
            </div>

            {{-- Ubah status --}}
            <div class="card card-hover p-4 mb-4" data-aos="fade-up">
                <h6 class="fw-bold mb-3"><i class="bi bi-arrow-repeat me-2 text-mk"></i>Ubah Status Lamaran</h6>
                <div class="d-grid gap-2">
                    @foreach($statusList as $key => [$label, $color])
                        <button class="btn btn-sm btn-outline-{{ $color }} status-btn {{ $lamaran->status === $key ? 'active' : '' }}"
                            data-url="{{ route('perusahaan.pelamar.status', $lamaran) }}" data-status="{{ $key }}"
                            {{ $lamaran->status === $key ? 'disabled' : '' }}>
                            <i class="bi {{ match($key) { 'menunggu' => 'bi-hourglass-split', 'diproses' => 'bi-search', 'psikotes' => 'bi-journal-check', 'interview' => 'bi-camera-video', 'diterima' => 'bi-trophy', 'ditolak' => 'bi-x-circle' } }} me-1"></i>
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            {{-- Catatan --}}
            <div class="card card-hover p-4 mb-4" data-aos="fade-up">
                <h6 class="fw-bold mb-3"><i class="bi bi-chat-left-quote me-2 text-mk"></i>Catatan Lamaran</h6>
                @if($lamaran->catatan)
                    <p class="text-muted-2 mb-0">{{ $lamaran->catatan }}</p>
                @else
                    <p class="text-muted-2 small mb-0">Tidak ada catatan.</p>
                @endif
                @if($lamaran->file_pendukung)
                    <div class="mt-3">
                        <i class="bi bi-paperclip me-1 text-mk"></i>
                        <a href="{{ asset('storage/' . $lamaran->file_pendukung) }}" target="_blank" class="text-mk fw-semibold small">File Pendukung</a>
                    </div>
                @endif
            </div>

            {{-- Data pelamar --}}
            <div class="card card-hover p-4 mb-4" data-aos="fade-up">
                <h6 class="fw-bold mb-3"><i class="bi bi-person-lines-fill me-2 text-mk"></i>Profil Pelamar</h6>
                <div class="row g-3 small">
                    <div class="col-md-4"><div class="text-muted-2">Tanggal Lahir</div><div class="fw-semibold">{{ $lamaran->pelamar->tanggal_lahir?->translatedFormat('d M Y') ?: '-' }}</div></div>
                    <div class="col-md-4"><div class="text-muted-2">Jenis Kelamin</div><div class="fw-semibold">{{ $lamaran->pelamar->jenis_kelamin === 'L' ? 'Laki-laki' : ($lamaran->pelamar->jenis_kelamin === 'P' ? 'Perempuan' : '-') }}</div></div>
                    <div class="col-md-4"><div class="text-muted-2">Pendidikan</div><div class="fw-semibold">{{ $lamaran->pelamar->pendidikan_terakhir ?: '-' }}</div></div>
                    <div class="col-md-4"><div class="text-muted-2">Tahun Lulus</div><div class="fw-semibold">{{ $lamaran->pelamar->tahun_lulus ?: '-' }}</div></div>
                    <div class="col-md-8"><div class="text-muted-2">Keahlian</div><div class="fw-semibold">{{ $lamaran->pelamar->keahlian ?: '-' }}</div></div>
                </div>
                @if($lamaran->pelamar->tentang)
                    <hr style="border-color:var(--mk-border);">
                    <div class="small text-muted-2" style="white-space:pre-line;">{{ $lamaran->pelamar->tentang }}</div>
                @endif
            </div>

            {{-- Feedback untuk pelamar --}}
            <div class="card card-hover p-4 mb-4" data-aos="fade-up">
                <h6 class="fw-bold mb-3"><i class="bi bi-chat-heart me-2 text-mk"></i>Feedback untuk Pelamar</h6>

                @if($lamaran->feedback)
                    <div class="alert alert-success border-0 rounded-4 small">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="fw-semibold">Feedback terkirim</span>
                            <span style="color:#F59E0B;font-size:.9rem;">{!! str_repeat('★', $lamaran->feedback->rating) !!}{!! str_repeat('☆', 5 - $lamaran->feedback->rating) !!}</span>
                            <span class="text-muted-2" style="font-size:.72rem;">• {{ $lamaran->feedback->updated_at->diffForHumans() }}</span>
                        </div>
                        @if($lamaran->feedback->feedback)
                            <p class="mb-0" style="font-style:italic;">"{{ $lamaran->feedback->feedback }}"</p>
                        @endif
                    </div>
                @endif

                <form id="feedbackForm" action="{{ route('perusahaan.pelamar.feedback', $lamaran) }}" class="row g-3">
                    @csrf
                    <div class="col-md-5">
                        <label class="form-label small">Penilaian</label>
                        <div class="d-flex gap-1 fs-4" id="ratingStars" style="cursor:pointer;">
                            @for($i = 1; $i <= 5; $i++)
                                <span class="star {{ $lamaran->feedback && $lamaran->feedback->rating >= $i ? 'text-warning' : 'text-muted-2' }}" data-value="{{ $i }}">★</span>
                            @endfor
                        </div>
                        <input type="hidden" name="rating" id="ratingValue" value="{{ $lamaran->feedback->rating ?? 5 }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label small">Pesan Feedback</label>
                        <textarea name="feedback" rows="3" class="form-control form-control-sm" placeholder="Contoh: Kemampuan komunikasi Anda sangat baik, pertahankan!" maxlength="2000">{{ $lamaran->feedback->feedback ?? '' }}</textarea>
                    </div>
                    <div class="col-12">
                        <button class="btn btn-mk btn-sm" id="feedbackBtn"><i class="bi bi-send me-1"></i>{{ $lamaran->feedback ? 'Perbarui Feedback' : 'Kirim Feedback' }}</button>
                    </div>
                </form>
            </div>

            {{-- Interview --}}
            <div class="card card-hover p-4 mb-4" data-aos="fade-up">
                <h6 class="fw-bold mb-3"><i class="bi bi-camera-video me-2 text-mk"></i>Jadwalkan Interview</h6>

                @if($lamaran->interview)
                    <div class="alert alert-info border-0 rounded-4 small">
                        <div class="fw-semibold mb-1"><i class="bi bi-calendar-check me-1"></i>Interview Terjadwal</div>
                        {{ $lamaran->interview->tanggal->translatedFormat('l, d F Y') }} • {{ $lamaran->interview->waktu }} WIB
                        ({{ $lamaran->interview->tipe === 'online' ? 'Online' : 'Offline' }})
                        @if($lamaran->interview->link) • <a href="{{ $lamaran->interview->link }}" target="_blank" class="fw-semibold">Link</a> @endif
                        @if($lamaran->interview->lokasi) • {{ $lamaran->interview->lokasi }} @endif
                        @if($lamaran->interview->kode)
                            <div class="d-flex align-items-center gap-3 mt-2 pt-2 border-top" style="border-color:rgba(2,136,209,.15) !important;">
                                <div class="rounded-2 p-1 bg-white">{!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(76)->generate($lamaran->interview->publicUrl()) !!}</div>
                                <div>
                                    <div class="fw-semibold">QR Code Interview</div>
                                    <div class="text-muted-2" style="font-size:.75rem;">Pelamar dapat scan QR untuk melihat detail interview.</div>
                                    <a href="{{ $lamaran->interview->publicUrl() }}" target="_blank" class="fw-semibold small">Buka halaman undangan →</a>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                <form id="interviewForm" action="{{ route('perusahaan.pelamar.interview', $lamaran) }}" class="row g-3">
                    @csrf
                    <div class="col-md-4">
                        <label class="form-label small">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small">Waktu</label>
                        <input type="time" name="waktu" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label small">Tipe</label>
                        <select name="tipe" class="form-select form-select-sm" id="tipeInterview">
                            <option value="online">Online</option>
                            <option value="offline">Offline</option>
                        </select>
                    </div>
                    <div class="col-md-6 d-none" id="linkField">
                        <label class="form-label small">Link Meeting (Zoom/Meet)</label>
                        <input type="url" name="link" class="form-control form-control-sm" placeholder="https://zoom.us/j/...">
                    </div>
                    <div class="col-md-6 d-none" id="lokasiField">
                        <label class="form-label small">Lokasi Offline</label>
                        <input type="text" name="lokasi" class="form-control form-control-sm" placeholder="Alamat kantor">
                    </div>
                    <div class="col-12">
                        <label class="form-label small">Catatan untuk Pelamar</label>
                        <textarea name="catatan" rows="2" class="form-control form-control-sm" placeholder="Persiapkan portofolio dan dokumen..."></textarea>
                    </div>
                    <div class="col-12">
                        <button class="btn btn-mk btn-sm" id="interviewBtn"><i class="bi bi-calendar-plus me-1"></i>Jadwalkan Interview</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal konfirmasi ubah status --}}
    <div class="modal fade" id="statusConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-body text-center p-4 pt-5 pb-4">
                    <div class="feature-icon mx-auto mb-3" style="width:60px;height:60px;font-size:1.4rem;border-radius:50%;background:#e0f2fe;color:#0284c7;"><i class="bi bi-arrow-repeat"></i></div>
                    <h5 class="fw-bold mb-2">Ubah Status Lamaran?</h5>
                    <p class="text-muted-2 small mb-4">Status lamaran <strong id="statusConfirmLabel"></strong> akan diubah. Pelamar akan menerima notifikasi perubahan ini.</p>
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-soft px-4" data-bs-dismiss="modal">Tidak</button>
                        <button type="button" class="btn btn-mk px-4" id="statusConfirmBtn"><i class="bi bi-check-lg me-1"></i>Ya, Ubah</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Status (konfirmasi via modal)
        const statusModalEl = document.getElementById('statusConfirmModal');
        const statusModal = statusModalEl ? bootstrap.Modal.getOrCreateInstance(statusModalEl) : null;
        const statusBtn = document.getElementById('statusConfirmBtn');
        const statusLabel = document.getElementById('statusConfirmLabel');
        document.querySelectorAll('.status-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                if (btn.disabled) return;
                statusBtn.dataset.url = btn.dataset.url;
                statusBtn.dataset.status = btn.dataset.status;
                statusLabel.textContent = '"' + btn.textContent.trim() + '"';
                statusModal.show();
            });
        });
        statusBtn?.addEventListener('click', async () => {
            try {
                await mkFetch(statusBtn.dataset.url, { method: 'POST', body: JSON.stringify({ status: statusBtn.dataset.status }) });
                statusModal.hide();
                mkToast('Status lamaran diperbarui', 'success');
                setTimeout(() => location.reload(), 800);
            } catch (err) {
                statusModal.hide();
                mkToast(err.message, 'error');
            }
        });

        // Tipe interview
        const tipe = document.getElementById('tipeInterview');
        const toggleTipe = () => {
            document.getElementById('linkField').classList.toggle('d-none', tipe.value !== 'online');
            document.getElementById('lokasiField').classList.toggle('d-none', tipe.value !== 'offline');
        };
        tipe?.addEventListener('change', toggleTipe);
        toggleTipe();

        // Submit interview
        document.getElementById('interviewForm')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const form = e.currentTarget;
            const btn = document.getElementById('interviewBtn');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
            try {
                await mkFetch(form.action, { method: 'POST', body: new FormData(form) });
                mkToast('Interview berhasil dijadwalkan', 'success');
                setTimeout(() => location.reload(), 800);
            } catch (err) {
                mkToast(err.message, 'error');
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-calendar-plus me-1"></i>Jadwalkan Interview';
            }
        });

        // Rating stars
        const stars = document.querySelectorAll('#ratingStars .star');
        const ratingValue = document.getElementById('ratingValue');
        const paintStars = (v) => stars.forEach(s => s.classList.toggle('text-warning', s.dataset.value <= v));
        stars.forEach(s => {
            s.addEventListener('mouseenter', () => paintStars(parseInt(s.dataset.value)));
            s.addEventListener('click', () => { ratingValue.value = s.dataset.value; paintStars(parseInt(s.dataset.value)); });
        });
        document.getElementById('ratingStars')?.addEventListener('mouseleave', () => paintStars(parseInt(ratingValue.value)));
        // Paint bintang sesuai nilai awal (agar tampilan konsisten dengan ratingValue)
        paintStars(parseInt(ratingValue.value));

        // Submit feedback
        document.getElementById('feedbackForm')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const form = e.currentTarget;
            const btn = document.getElementById('feedbackBtn');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengirim...';
            try {
                await mkFetch(form.action, { method: 'POST', body: new FormData(form) });
                mkToast('Feedback berhasil dikirim ke pelamar', 'success');
                setTimeout(() => location.reload(), 800);
            } catch (err) {
                mkToast(err.message, 'error');
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-send me-1"></i>Kirim Feedback';
            }
        });
    });
</script>
@endpush
