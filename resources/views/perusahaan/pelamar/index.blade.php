@extends('layouts.app')

@section('title', 'Kelola Pelamar - MagangKu')
@section('page-title', 'Kelola Pelamar')

@section('content')
    <div class="card card-hover p-3 mb-4" data-aos="fade-up">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label small mb-1">Filter Lowongan</label>
                <select name="lowongan" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Semua Lowongan</option>
                    @foreach($lowongans as $lowongan)
                        <option value="{{ $lowongan->id }}" @selected(request('lowongan') == $lowongan->id)>{{ $lowongan->posisi }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label small mb-1">Filter Status</label>
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    @foreach($statusList as $key => [$label, $color])
                        <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-grid">
                <button class="btn btn-mk btn-sm"><i class="bi bi-funnel me-1"></i>Filter</button>
            </div>
        </form>
    </div>

    @if($lowongans->isNotEmpty())
        <div class="card card-hover p-3 mb-4" data-aos="fade-up">
            <div class="d-flex flex-wrap align-items-end gap-2">
                <div class="flex-grow-1" style="min-width:220px;">
                    <label class="form-label small mb-1">Export Data Pelamar per Lowongan</label>
                    <select id="exportLowongan" class="form-select form-select-sm">
                        @foreach($lowongans as $l)
                            <option value="{{ $l->id }}" @selected(request('lowongan') == $l->id)>{{ $l->posisi }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="button" class="btn btn-outline-mk btn-sm" data-format="csv"><i class="bi bi-filetype-csv me-1"></i>CSV</button>
                <button type="button" class="btn btn-outline-mk btn-sm" data-format="excel"><i class="bi bi-file-earmark-excel me-1"></i>Excel</button>
                <button type="button" class="btn btn-mk btn-sm" data-format="pdf"><i class="bi bi-file-earmark-pdf me-1"></i>PDF</button>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('perusahaan.pelamar.bulkStatus') }}" class="card card-hover p-4" data-aos="fade-up" id="bulkForm">
        @csrf
        <div class="d-flex flex-wrap align-items-center gap-2 mb-3 pb-3 border-bottom" style="border-color:var(--mk-border) !important;">
            <div class="form-check mb-0">
                <input class="form-check-input" type="checkbox" id="selectAll">
                <label class="form-check-label small" for="selectAll">Pilih Semua</label>
            </div>
            <div class="d-flex align-items-center gap-2 ms-auto flex-wrap">
                <span class="small text-muted-2" id="selectedCount">0 dipilih</span>
                <select name="status" class="form-select form-select-sm" style="width:auto;" id="bulkStatusSelect" required>
                    <option value="">Ubah status menjadi...</option>
                    @foreach($statusList as $key => [$label, $color])
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
                <button type="button" class="btn btn-mk btn-sm" id="bulkApply" disabled><i class="bi bi-check2-square me-1"></i>Terapkan</button>
            </div>
        </div>
        @forelse($lamarans as $lamaran)
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 py-3 {{ !$loop->last ? 'border-bottom' : '' }}" style="border-color:var(--mk-border) !important;">
                <div class="d-flex align-items-center gap-3 min-w-0">
                    <div class="form-check mb-0">
                        <input class="form-check-input bulk-check" type="checkbox" name="lamaran_ids[]" value="{{ $lamaran->id }}">
                    </div>
                    <div class="d-flex align-items-center gap-3 min-w-0">
                    @if($lamaran->pelamar->foto_url)
                        <img src="{{ $lamaran->pelamar->foto_url }}" class="rounded-circle" width="48" height="48" style="object-fit:cover;">
                    @else
                        <div class="avatar" style="background:var(--mk-gradient);">{{ strtoupper(substr($lamaran->pelamar->nama ?? '?', 0, 1)) }}</div>
                    @endif
                    <div class="min-w-0">
                        <div class="fw-semibold">{{ $lamaran->pelamar->nama }}</div>
                        <div class="small text-muted-2 text-truncate">
                            {{ $lamaran->lowongan->posisi }}
                            @if($lamaran->pelamar->universitas) • {{ $lamaran->pelamar->universitas }} @endif
                        </div>
                        <div class="small text-muted-2">Dilamar {{ $lamaran->created_at->translatedFormat('d M Y') }}</div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge rounded-pill text-bg-{{ $lamaran->status_color }}">{{ $lamaran->status_label }}</span>
                    <a href="{{ route('perusahaan.pelamar.show', $lamaran) }}" class="btn btn-soft btn-sm"><i class="bi bi-eye me-1"></i>Detail</a>
                    @if($lamaran->pelamar->cv_path)
                        <a href="{{ route('perusahaan.pelamar.cv', $lamaran) }}" class="btn btn-outline-mk btn-sm"><i class="bi bi-download me-1"></i>CV</a>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-5">
                <i class="bi bi-inbox fs-1 text-muted-2"></i>
                <h6 class="fw-bold mt-3">Belum Ada Lamaran</h6>
                <p class="text-muted-2 small">Lamaran yang masuk akan muncul di sini.</p>
            </div>
        @endforelse
    </form>

    <div class="d-flex justify-content-center mt-4">{{ $lamarans->links() }}</div>

    {{-- Modal konfirmasi ubah status massal --}}
    <div class="modal fade" id="bulkConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-body text-center p-4 pt-5 pb-4">
                    <div class="feature-icon mx-auto mb-3" style="width:60px;height:60px;font-size:1.4rem;border-radius:50%;background:#e0f2fe;color:#0284c7;"><i class="bi bi-people"></i></div>
                    <h5 class="fw-bold mb-2">Ubah Status Massal?</h5>
                    <p class="text-muted-2 small mb-4"><strong id="bulkConfirmText"></strong> Semua pelamar terpilih akan menerima notifikasi perubahan ini.</p>
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-soft px-4" data-bs-dismiss="modal">Tidak</button>
                        <button type="submit" form="bulkForm" class="btn btn-mk px-4"><i class="bi bi-check-lg me-1"></i>Ya, Terapkan</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const select = document.getElementById('exportLowongan');
        if (select) {
            const base = @js(route('perusahaan.pelamar.export', ['lowongan' => '__ID__']));
            document.querySelectorAll('[data-format]').forEach(btn => {
                btn.addEventListener('click', () => {
                    window.location.href = base.replace('__ID__', select.value) + '?format=' + btn.dataset.format;
                });
            });
        }

        // Bulk status
        const all = document.getElementById('selectAll');
        const checks = [...document.querySelectorAll('.bulk-check')];
        const count = document.getElementById('selectedCount');
        const apply = document.getElementById('bulkApply');
        const update = () => {
            const n = checks.filter(c => c.checked).length;
            count.textContent = n + ' dipilih';
            apply.disabled = n === 0;
            if (all) all.checked = n === checks.length && n > 0;
        };
        all?.addEventListener('change', () => {
            checks.forEach(c => { c.checked = all.checked; });
            update();
        });
        checks.forEach(c => c.addEventListener('change', update));
        // Klik Terapkan -> konfirmasi modal (hanya jika valid)
        apply?.addEventListener('click', () => {
            const n = checks.filter(c => c.checked).length;
            if (n === 0) return;
            const sel = document.getElementById('bulkStatusSelect');
            if (!sel || !sel.value) {
                mkToast('Pilih status tujuan terlebih dahulu', 'error');
                return;
            }
            const label = sel.options[sel.selectedIndex].text;
            document.getElementById('bulkConfirmText').textContent =
                n + ' pelamar akan diubah menjadi "' + label + '".';
            bootstrap.Modal.getOrCreateInstance(document.getElementById('bulkConfirmModal')).show();
        });
    });
</script>
@endpush
