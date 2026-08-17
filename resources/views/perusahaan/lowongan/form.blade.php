@extends('layouts.app')

@section('title', ($lowongan->exists ? 'Edit' : 'Posting') . ' Lowongan - MagangKu')
@section('page-title', ($lowongan->exists ? 'Edit' : 'Posting') . ' Lowongan')

@section('content')
    <form method="POST" action="{{ $lowongan->exists ? route('perusahaan.lowongan.update', $lowongan) : route('perusahaan.lowongan.store') }}" class="d-grid gap-4">
        @csrf
        @if($lowongan->exists)
            @method('PUT')
        @endif

        <div class="card card-hover p-4" data-aos="fade-up">
            <h6 class="fw-bold mb-3"><i class="bi bi-briefcase me-2 text-mk"></i>Informasi Posisi</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Posisi / Judul Lowongan</label>
                    <input type="text" name="posisi" value="{{ old('posisi', $lowongan->posisi) }}" class="form-control @error('posisi') is-invalid @enderror" placeholder="Frontend Developer" required>
                    @error('posisi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Jenis Pekerjaan</label>
                    <select name="jenis" class="form-select">
                        @foreach($jenisList as $key => $label)
                            <option value="{{ $key }}" @selected(old('jenis', $lowongan->jenis) === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kategori</label>
                    <select name="category_id" class="form-select">
                        <option value="">Tanpa Kategori</option>
                        @foreach($kategoriList as $kategori)
                            <option value="{{ $kategori->id }}" @selected(old('category_id', $lowongan->category_id) == $kategori->id)>{{ $kategori->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Lokasi</label>
                    <input type="text" name="lokasi" value="{{ old('lokasi', $lowongan->lokasi) }}" class="form-control" placeholder="Jakarta">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Gaji Min (Rp)</label>
                    <input type="number" name="gaji_min" value="{{ old('gaji_min', $lowongan->gaji_min) }}" class="form-control" placeholder="5000000" min="0">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Gaji Max (Rp)</label>
                    <input type="number" name="gaji_max" value="{{ old('gaji_max', $lowongan->gaji_max) }}" class="form-control" placeholder="8000000" min="0">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Kuota</label>
                    <input type="number" name="kuota" value="{{ old('kuota', $lowongan->kuota ?? 1) }}" class="form-control" min="1" max="999">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Deadline</label>
                    <input type="date" name="deadline" value="{{ old('deadline', $lowongan->deadline?->format('Y-m-d')) }}" class="form-control" required>
                </div>
                <div class="col-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_remote" id="isRemote" value="1" @checked(old('is_remote', $lowongan->is_remote))>
                        <label class="form-check-label" for="isRemote"><i class="bi bi-wifi me-1 text-mk"></i>Posisi ini Remote / WFH</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-hover p-4" data-aos="fade-up">
            <h6 class="fw-bold mb-3"><i class="bi bi-file-text me-2 text-mk"></i>Deskripsi & Persyaratan</h6>
            <div class="d-grid gap-3">
                <div>
                    <label class="form-label">Deskripsi Pekerjaan</label>
                    <textarea name="deskripsi" rows="8" class="form-control @error('deskripsi') is-invalid @enderror" placeholder="Jelaskan tanggung jawab dan detail pekerjaan..." required>{{ old('deskripsi', $lowongan->deskripsi) }}</textarea>
                    @error('deskripsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="form-label">Persyaratan</label>
                    <textarea name="persyaratan" rows="6" class="form-control" placeholder="Satu persyaratan per baris&#10;- Minimal S1&#10;- Menguasai Laravel">{{ old('persyaratan', $lowongan->persyaratan) }}</textarea>
                </div>
            </div>
        </div>

        <div class="card card-hover p-4" data-aos="fade-up">
            <h6 class="fw-bold mb-3"><i class="bi bi-lightning-charge me-2 text-mk"></i>Skill yang Dibutuhkan</h6>

            <div class="input-group mb-3" style="max-width:380px;">
                <span class="input-group-text bg-transparent"><i class="bi bi-search text-mk"></i></span>
                <input type="text" id="skillSearch" class="form-control" placeholder="Cari skill... (mis. Laravel, Figma, SQL)" autocomplete="off">
            </div>

            <div class="d-flex flex-wrap gap-2 mb-3" id="skillOptions">
                @foreach($skills as $skill)
                    <label class="chip mb-0" data-skill="{{ strtolower($skill->nama) }}">
                        <input type="checkbox" name="skills[]" value="{{ $skill->id }}" class="d-none" @checked(in_array($skill->id, old('skills', $lowongan->skills->pluck('id')->all())))>
                        <span>{{ $skill->nama }}</span>
                    </label>
                @endforeach
            </div>

            <div id="skillNoResult" class="text-muted-2 small d-none"><i class="bi bi-search me-1"></i>Skill tidak ditemukan.</div>
            <div class="text-muted-2 small" id="skillHint">Klik untuk memilih skill yang relevan dengan posisi ini.</div>
        </div>

        <div class="card card-hover p-4" data-aos="fade-up">
            <h6 class="fw-bold mb-3"><i class="bi bi-gear me-2 text-mk"></i>Status Publikasi</h6>
            <div class="row g-3 align-items-center">
                <div class="col-md-6">
                    <select name="status" class="form-select">
                        <option value="aktif" @selected(old('status', $lowongan->status) === 'aktif')>Aktif - Langsung tampil</option>
                        <option value="draft" @selected(old('status', $lowongan->status) === 'draft')>Draft - Simpan sementara</option>
                    </select>
                </div>
                <div class="col-md-6 d-flex justify-content-end gap-2">
                    <a href="{{ route('perusahaan.lowongan.index') }}" class="btn btn-soft px-4">Batal</a>
                    <button class="btn btn-mk px-5"><i class="bi bi-check-lg me-1"></i>{{ $lowongan->exists ? 'Simpan Perubahan' : 'Publikasikan' }}</button>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Toggle skill chip aktif
        document.querySelectorAll('#skillOptions input[type="checkbox"]').forEach(input => {
            input.addEventListener('change', () => {
                input.closest('.chip').classList.toggle('active', input.checked);
            });
            if (input.checked) input.closest('.chip').classList.add('active');
        });

        // Pencarian skill: filter chip secara realtime
        const search = document.getElementById('skillSearch');
        const noResult = document.getElementById('skillNoResult');
        const hint = document.getElementById('skillHint');
        const chips = document.querySelectorAll('#skillOptions .chip');

        search?.addEventListener('input', () => {
            const q = search.value.trim().toLowerCase();
            let visible = 0;

            chips.forEach(chip => {
                const match = !q || (chip.dataset.skill || '').includes(q);
                chip.style.display = match ? '' : 'none';
                if (match) visible++;
            });

            noResult?.classList.toggle('d-none', visible > 0);
            if (hint) hint.style.display = q ? 'none' : '';
        });
    });
</script>
@endpush
