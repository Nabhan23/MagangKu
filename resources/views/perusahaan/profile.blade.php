@extends('layouts.app')

@section('title', 'Profil Perusahaan - MagangKu')
@section('page-title', 'Profil Perusahaan')

@section('content')
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card card-hover p-4 text-center mb-4" data-aos="fade-up">
                @php $initial = strtoupper(substr($perusahaan->nama_perusahaan, 0, 1)); @endphp
                @if($perusahaan->logo_url)
                    <img src="{{ $perusahaan->logo_url }}" class="rounded-4 mx-auto mb-3" width="110" height="110" style="object-fit:cover;border:4px solid var(--mk-secondary);">
                @else
                    <div class="company-logo-lg mx-auto mb-3" style="width:110px;height:110px;font-size:2.4rem;background:var(--mk-gradient);">{{ $initial }}</div>
                @endif
                <h6 class="fw-bold">{{ $perusahaan->nama_perusahaan }}</h6>
                <span class="badge rounded-pill text-bg-{{ $perusahaan->status_verifikasi_badge[0] }}">{{ $perusahaan->status_verifikasi_badge[1] }}</span>

                <form action="{{ route('perusahaan.profile.uploadLogo') }}" method="POST" enctype="multipart/form-data" class="d-grid gap-2 mt-3">
                    @csrf
                    <input type="file" name="logo" class="form-control form-control-sm" accept="image/*" required>
                    <button class="btn btn-soft btn-sm"><i class="bi bi-image me-1"></i>Upload Logo</button>
                </form>
            </div>

            <div class="card card-hover p-4 mb-4" data-aos="fade-up">
                <h6 class="fw-bold mb-3"><i class="bi bi-shield-check me-2 text-mk"></i>Verifikasi Perusahaan</h6>
                @if($perusahaan->dokumen_verifikasi)
                    <div class="alert alert-success border-0 rounded-4 small">
                        <i class="bi bi-check-circle-fill me-1"></i>Dokumen terunggah.
                        <a href="{{ route('perusahaan.profile.dokumen') }}" target="_blank" class="d-block mt-1 fw-semibold">Lihat Dokumen</a>
                    </div>
                @endif
                <form action="{{ route('perusahaan.profile.uploadDokumen') }}" method="POST" enctype="multipart/form-data" class="d-grid gap-2">
                    @csrf
                    <input type="file" name="dokumen" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png" required>
                    <button class="btn btn-mk btn-sm"><i class="bi bi-cloud-arrow-up me-1"></i>Kirim Dokumen Verifikasi</button>
                    <div class="text-muted-2 small text-center">NIB / Akta Pendirian (PDF/Image, max 5MB)</div>
                </form>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card card-hover p-4" data-aos="fade-up">
                <h6 class="fw-bold mb-4"><i class="bi bi-building-gear me-2 text-mk"></i>Data Perusahaan</h6>
                <form method="POST" action="{{ route('perusahaan.profile.update') }}" class="d-grid gap-3">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Perusahaan</label>
                            <input type="text" name="nama_perusahaan" value="{{ old('nama_perusahaan', $perusahaan->nama_perusahaan) }}" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Industri</label>
                            <input type="text" name="industri" value="{{ old('industri', $perusahaan->industri) }}" class="form-control" placeholder="Teknologi, Finansial, Pendidikan...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Lokasi / Kota</label>
                            <input type="text" name="lokasi" value="{{ old('lokasi', $perusahaan->lokasi) }}" class="form-control" placeholder="Jakarta Selatan">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Alamat Lengkap <span class="text-muted-2 fw-normal">(untuk navigasi Google Maps)</span></label>
                            <textarea name="alamat_lengkap" rows="2" class="form-control" placeholder="Jl. Sudirman No. 123, RT 05/RW 02, Kec. Setiabudi, Jakarta Selatan">{{ old('alamat_lengkap', $perusahaan->alamat_lengkap) }}</textarea>
                            <div class="form-text small text-muted-2">Alamat ini dipakai tombol "Buka di Maps" di halaman publik perusahaan.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ukuran Perusahaan</label>
                            <select name="ukuran_perusahaan" class="form-select">
                                <option value="">-- Pilih --</option>
                                @foreach(['1-10', '11-50', '51-200', '201-500', '500+'] as $u)
                                    <option value="{{ $u }}" @selected($perusahaan->ukuran_perusahaan === $u)>{{ $u }} karyawan</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Website</label>
                            <input type="url" name="website" value="{{ old('website', $perusahaan->website) }}" class="form-control" placeholder="https://perusahaan.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tahun Berdiri</label>
                            <input type="number" name="tahun_berdiri" value="{{ old('tahun_berdiri', $perusahaan->tahun_berdiri) }}" class="form-control" min="1900" max="2030">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email Kontak</label>
                            <input type="email" name="email_kontak" value="{{ old('email_kontak', $perusahaan->email_kontak) }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Telepon</label>
                            <input type="text" name="telepon" value="{{ old('telepon', $perusahaan->telepon) }}" class="form-control">
                        </div>
                    </div>

                    <div>
                        <label class="form-label">Deskripsi Perusahaan</label>
                        <textarea name="deskripsi" rows="6" class="form-control" placeholder="Ceritakan tentang perusahaan Anda...">{{ old('deskripsi', $perusahaan->deskripsi) }}</textarea>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button class="btn btn-mk px-5"><i class="bi bi-check-lg me-1"></i>Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
