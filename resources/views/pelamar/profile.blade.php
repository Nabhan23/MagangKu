@extends('layouts.app')

@section('title', 'Profil & CV - MagangKu')
@section('page-title', 'Profil & CV')

@section('content')
    <div class="row g-4">
        {{-- Kolom kiri: foto & CV --}}
        <div class="col-lg-4">
            <div class="card card-hover p-4 text-center mb-4" data-aos="fade-up">
                @if($profile->foto_url)
                    <div class="position-relative d-inline-block mx-auto mb-3">
                        <img src="{{ $profile->foto_url }}" class="rounded-circle" width="110" height="110" style="object-fit:cover;border:4px solid var(--mk-secondary);">
                        <form action="{{ route('pelamar.profile.deleteFoto') }}" method="POST" id="deleteFotoForm" class="position-absolute bottom-0 end-0">
                            @csrf
                            <button type="button" class="btn btn-danger btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width:30px;height:30px;border:2px solid #fff;" title="Hapus Foto" aria-label="Hapus Foto" data-bs-toggle="modal" data-bs-target="#hapusFotoModal"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                @else
                    <div class="avatar avatar-lg mx-auto mb-3" style="background:var(--mk-gradient);width:110px;height:110px;font-size:2.4rem;">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                @endif
                <h6 class="fw-bold mb-1">{{ $profile->nama ?: auth()->user()->name }}</h6>
                <p class="small text-muted-2 mb-3">{{ $profile->universitas ?: 'Belum mengisi universitas' }} @if($profile->jurusan) • {{ $profile->jurusan }} @endif</p>

                <form action="{{ route('pelamar.profile.uploadFoto') }}" method="POST" enctype="multipart/form-data" class="d-grid gap-2">
                    @csrf
                    <input type="file" name="foto" class="form-control form-control-sm" accept="image/*" required>
                    <button class="btn btn-soft btn-sm"><i class="bi bi-camera me-1"></i>Upload Foto</button>
                </form>
            </div>

            {{-- Modal konfirmasi hapus foto --}}
            <div class="modal fade" id="hapusFotoModal" tabindex="-1" aria-labelledby="hapusFotoModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content rounded-4 border-0 shadow">
                        <div class="modal-body text-center p-4 pt-5 pb-4">
                            <div class="feature-icon mx-auto mb-3" style="width:60px;height:60px;font-size:1.4rem;border-radius:50%;background:#fee2e2;color:#dc2626;"><i class="bi bi-trash"></i></div>
                            <h5 class="fw-bold mb-2">Hapus Foto Profil?</h5>
                            <p class="text-muted-2 small mb-4">Foto profil akan dihapus permanen dari akun Anda.</p>
                            <div class="d-flex gap-2 justify-content-center">
                                <button type="button" class="btn btn-soft px-4" data-bs-dismiss="modal">Tidak</button>
                                <button type="submit" form="deleteFotoForm" class="btn btn-danger px-4"><i class="bi bi-trash me-1"></i>Ya, Hapus</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal konfirmasi hapus CV --}}
            <div class="modal fade" id="hapusCvModal" tabindex="-1" aria-labelledby="hapusCvModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content rounded-4 border-0 shadow">
                        <div class="modal-body text-center p-4 pt-5 pb-4">
                            <div class="feature-icon mx-auto mb-3" style="width:60px;height:60px;font-size:1.4rem;border-radius:50%;background:#fee2e2;color:#dc2626;"><i class="bi bi-file-earmark-x"></i></div>
                            <h5 class="fw-bold mb-2">Hapus CV?</h5>
                            <p class="text-muted-2 small mb-4">CV akan dihapus permanen dari profil Anda.</p>
                            <div class="d-flex gap-2 justify-content-center">
                                <button type="button" class="btn btn-soft px-4" data-bs-dismiss="modal">Tidak</button>
                                <button type="submit" form="deleteCvForm" class="btn btn-danger px-4"><i class="bi bi-trash me-1"></i>Ya, Hapus</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-hover p-4 mb-4" data-aos="fade-up">
                <h6 class="fw-bold mb-3"><i class="bi bi-file-earmark-text me-2 text-mk"></i>CV Saya</h6>
                @if($profile->cv_path)
                    <div class="alert alert-success border-0 rounded-4 small">
                        <i class="bi bi-check-circle-fill me-1"></i>CV sudah diunggah.
                    </div>
                    <a href="{{ route('pelamar.profile.downloadCv') }}" target="_blank" class="btn btn-soft btn-sm w-100 mb-2"><i class="bi bi-eye me-1"></i>Lihat CV</a>
                    <form action="{{ route('pelamar.profile.deleteCv') }}" method="POST" id="deleteCvForm">
                        @csrf
                        <button type="button" class="btn btn-outline-danger btn-sm w-100 mb-2" data-bs-toggle="modal" data-bs-target="#hapusCvModal"><i class="bi bi-trash me-1"></i>Hapus CV</button>
                    </form>
                @else
                    <div class="alert alert-warning border-0 rounded-4 small">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>Unggah CV untuk bisa melamar lowongan.
                    </div>
                @endif
                <form action="{{ route('pelamar.profile.uploadCv') }}" method="POST" enctype="multipart/form-data" class="d-grid gap-2">
                    @csrf
                    <input type="file" name="cv" class="form-control form-control-sm" accept=".pdf,.doc,.docx" required>
                    <button class="btn btn-mk btn-sm"><i class="bi bi-cloud-arrow-up me-1"></i>Upload CV Baru</button>
                    <div class="text-muted-2 small text-center">PDF / Word, maks 5MB</div>
                </form>
            </div>

            <div class="card card-hover p-4" data-aos="fade-up">
                <h6 class="fw-bold mb-3"><i class="bi bi-award me-2 text-mk"></i>Badge Prestasi</h6>
                <div class="d-flex flex-wrap gap-2">
                    <span class="chip {{ $profile->badge === 'cv_lengkap' ? 'active' : '' }}" style="cursor:default;">📄 CV Lengkap</span>
                    <span class="chip {{ $profile->badge === 'pelamar_aktif' ? 'active' : '' }}" style="cursor:default;">⚡ Pelamar Aktif</span>
                    <span class="chip {{ $profile->badge === 'top_talent' ? 'active' : '' }}" style="cursor:default;">🏆 Top Talent</span>
                    <span class="chip {{ $profile->badge === 'interview_expert' ? 'active' : '' }}" style="cursor:default;">🎤 Interview Expert</span>
                </div>
                <p class="small text-muted-2 mt-3 mb-0">Lengkapi profil & CV untuk mendapatkan badge <strong>CV Lengkap</strong>.</p>
            </div>
        </div>

        {{-- Kolom kanan: form profil --}}
        <div class="col-lg-8">
            <div class="card card-hover p-4" data-aos="fade-up">
                <h6 class="fw-bold mb-4"><i class="bi bi-person-gear me-2 text-mk"></i>Data Diri</h6>

                <form method="POST" action="{{ route('pelamar.profile.update') }}" class="d-grid gap-3">
                    @csrf
                    @method('PUT')

                    @if($errors->any())
                        <div class="alert alert-danger border-0 rounded-4">
                            <i class="bi bi-exclamation-circle me-1"></i>
                            <strong>Profil tidak tersimpan.</strong> Perbaiki isian di bawah ini:
                            <ul class="small mb-0 mt-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $profile->nama_lengkap) }}" class="form-control @error('nama_lengkap') is-invalid @enderror" required>
                            @error('nama_lengkap')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" value="{{ auth()->user()->email }}" class="form-control" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">No. HP</label>
                            <input type="text" name="phone" value="{{ old('phone', auth()->user()->phone) }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $profile->tanggal_lahir?->format('Y-m-d')) }}" class="form-control @error('tanggal_lahir') is-invalid @enderror">
                            @error('tanggal_lahir')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $profile->tempat_lahir) }}" class="form-control @error('tempat_lahir') is-invalid @enderror" placeholder="Jakarta">
                            @error('tempat_lahir')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror">
                                <option value="">-- Pilih --</option>
                                <option value="L" @selected($profile->jenis_kelamin === 'L')>Laki-laki</option>
                                <option value="P" @selected($profile->jenis_kelamin === 'P')>Perempuan</option>
                            </select>
                            @error('jenis_kelamin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Lokasi Domisili</label>
                            <input type="text" name="lokasi" value="{{ old('lokasi', $profile->lokasi) }}" class="form-control" placeholder="Jakarta">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Alamat Lengkap</label>
                            <textarea name="alamat" rows="2" class="form-control @error('alamat') is-invalid @enderror" placeholder="Jl. Merdeka No. 123, RT 01/RW 02, Kel. Gambir, Kec. Gambir, Jakarta Pusat, DKI Jakarta 10110">{{ old('alamat', $profile->alamat) }}</textarea>
                            @error('alamat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ekspektasi Gaji <span class="text-muted-2 small fw-normal">(opsional)</span></label>
                            <input type="text" name="ekspektasi_gaji" value="{{ old('ekspektasi_gaji', $profile->ekspektasi_gaji) }}" class="form-control @error('ekspektasi_gaji') is-invalid @enderror" placeholder="Rp 5.000.000 – Rp 8.000.000">
                            @error('ekspektasi_gaji')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <hr style="border-color:var(--mk-border);">

                    <h6 class="fw-bold"><i class="bi bi-mortarboard me-2 text-mk"></i>Pendidikan</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Universitas</label>
                            <input type="text" name="universitas" value="{{ old('universitas', $profile->universitas) }}" class="form-control @error('universitas') is-invalid @enderror" placeholder="Universitas Indonesia">
                            @error('universitas')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jurusan</label>
                            <input type="text" name="jurusan" value="{{ old('jurusan', $profile->jurusan) }}" class="form-control @error('jurusan') is-invalid @enderror" placeholder="Teknik Informatika">
                            @error('jurusan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Pendidikan Terakhir</label>
                            <select name="pendidikan_terakhir" class="form-select @error('pendidikan_terakhir') is-invalid @enderror">
                                <option value="">-- Pilih --</option>
                                @foreach(['SMA/SMK', 'D3', 'D4/S1', 'S2', 'S3'] as $p)
                                    <option value="{{ $p }}" @selected($profile->pendidikan_terakhir === $p)>{{ $p }}</option>
                                @endforeach
                            </select>
                            @error('pendidikan_terakhir')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tahun Lulus</label>
                            <input type="number" name="tahun_lulus" value="{{ old('tahun_lulus', $profile->tahun_lulus) }}" class="form-control @error('tahun_lulus') is-invalid @enderror" min="1980" max="2030">
                            @error('tahun_lulus')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Keahlian</label>
                            <input type="text" name="keahlian" value="{{ old('keahlian', $profile->keahlian) }}" class="form-control" placeholder="Laravel, Figma, SEO (pisahkan koma)">
                        </div>
                    </div>

                    <hr style="border-color:var(--mk-border);">

                    <h6 class="fw-bold"><i class="bi bi-palette me-2 text-mk"></i>Portofolio</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Website / Portofolio</label>
                            <input type="text" name="website_portfolio" value="{{ old('website_portfolio', $profile->website_portfolio) }}" class="form-control @error('website_portfolio') is-invalid @enderror" placeholder="portfolio-ku.com">
                            @error('website_portfolio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">LinkedIn</label>
                            <input type="text" name="linkedin" value="{{ old('linkedin', $profile->linkedin) }}" class="form-control @error('linkedin') is-invalid @enderror" placeholder="linkedin.com/in/...">
                            @error('linkedin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">GitHub</label>
                            <input type="text" name="github" value="{{ old('github', $profile->github) }}" class="form-control @error('github') is-invalid @enderror" placeholder="github.com/...">
                            @error('github')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_portfolio_public" id="portfolioPublic" @checked($profile->is_portfolio_public)>
                                <label class="form-check-label small" for="portfolioPublic">Publikasikan halaman portofolio saya</label>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="form-label">Tentang Saya</label>
                        <textarea name="tentang" rows="4" class="form-control @error('tentang') is-invalid @enderror" placeholder="Ceritakan tentang dirimu, pengalaman, dan tujuan kariermu...">{{ old('tentang', $profile->tentang) }}</textarea>
                        @error('tentang')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex justify-content-end">
                        <button class="btn btn-mk px-5"><i class="bi bi-check-lg me-1"></i>Simpan Profil</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
