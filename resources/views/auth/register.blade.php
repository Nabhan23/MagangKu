@extends('layouts.auth')

@section('title', 'Daftar - MagangKu')

@section('auth-content')
    <h5 class="fw-bold mb-1">Buat Akun Baru 🚀</h5>
    <p class="text-muted-2 small mb-4">Mulai perjalanan kariermu bersama MagangKu.</p>

    @include('auth.partials.social-buttons')

    <form method="POST" action="{{ route('register') }}" class="d-grid gap-3" id="registerForm">
        @csrf

        {{-- Pilih peran --}}
        <div>
            <label class="form-label">Daftar sebagai</label>
            <div class="row g-2">
                <div class="col-6">
                    <input type="radio" class="btn-check" name="role" id="rolePelamar" value="pelamar" {{ old('role', 'pelamar') === 'pelamar' ? 'checked' : '' }}>
                    <label for="rolePelamar" class="btn role-option w-100 rounded-4 text-start p-3">
                        <span class="role-check"><i class="bi bi-check-lg"></i></span>
                        <i class="bi bi-person-badge-fill fs-4 text-mk"></i>
                        <span class="min-w-0">
                            <span class="d-block fw-bold small">Pelamar</span>
                            <span class="d-block text-muted-2" style="font-size:.7rem;">Mahasiswa / Fresh Graduate</span>
                        </span>
                    </label>
                </div>
                <div class="col-6">
                    <input type="radio" class="btn-check" name="role" id="rolePerusahaan" value="perusahaan" {{ old('role') === 'perusahaan' ? 'checked' : '' }}>
                    <label for="rolePerusahaan" class="btn role-option w-100 rounded-4 text-start p-3">
                        <span class="role-check"><i class="bi bi-check-lg"></i></span>
                        <i class="bi bi-buildings-fill fs-4 text-mk"></i>
                        <span class="min-w-0">
                            <span class="d-block fw-bold small">Perusahaan</span>
                            <span class="d-block text-muted-2" style="font-size:.7rem;">HRD / Rekruter</span>
                        </span>
                    </label>
                </div>
            </div>
            @error('role')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        {{-- ===== Field khusus Pelamar ===== --}}
        <div id="fieldsPelamar" class="d-grid gap-3 {{ old('role', 'pelamar') === 'perusahaan' ? 'd-none' : '' }}">
            <div>
                <label class="form-label">Nama Lengkap</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0 rounded-start-4"><i class="bi bi-person text-mk"></i></span>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-control rounded-start-0 {{ $errors->has('name') ? 'is-invalid' : '' }}" placeholder="Nama Lengkap" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div>
                <label class="form-label">No. HP <span class="text-muted-2 fw-normal">(opsional)</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0 rounded-start-4"><i class="bi bi-phone text-mk"></i></span>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="form-control rounded-start-0" placeholder="08xxxxxxxxxx">
                </div>
            </div>
        </div>

        {{-- ===== Field khusus Perusahaan ===== --}}
        <div id="fieldsPerusahaan" class="d-grid gap-3 {{ old('role') === 'perusahaan' ? '' : 'd-none' }}">
            <div>
                <label class="form-label">Nama Perusahaan</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0 rounded-start-4"><i class="bi bi-buildings text-mk"></i></span>
                    <input type="text" name="nama_perusahaan" value="{{ old('nama_perusahaan') }}" class="form-control rounded-start-0 {{ $errors->has('nama_perusahaan') ? 'is-invalid' : '' }}" placeholder="PT Contoh Indonesia" required>
                    @error('nama_perusahaan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row g-3">
                <div class="col-6">
                    <label class="form-label">Bidang / Industri</label>
                    <input type="text" name="industri" value="{{ old('industri') }}" list="industriList" class="form-control {{ $errors->has('industri') ? 'is-invalid' : '' }}" placeholder="Teknologi, Finansial..." required>
                    <datalist id="industriList">
                        <option value="Teknologi Informasi">
                        <option value="Desain & Kreatif">
                        <option value="Marketing & Sales">
                        <option value="Perbankan">
                        <option value="Fintech">
                        <option value="Pendidikan">
                        <option value="Media & Entertainment">
                        <option value="Konstruksi & Properti">
                        <option value="Agrikultur">
                        <option value="Kesehatan">
                        <option value="Logistik">
                        <option value="Retail & E-commerce">
                    </datalist>
                    @error('industri')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-6">
                    <label class="form-label">Lokasi / Kota</label>
                    <input type="text" name="lokasi" value="{{ old('lokasi') }}" class="form-control {{ $errors->has('lokasi') ? 'is-invalid' : '' }}" placeholder="Jakarta Selatan" required>
                    @error('lokasi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div>
                <label class="form-label">Alamat Lengkap <span class="text-muted-2 fw-normal">(opsional, untuk navigasi Google Maps)</span></label>
                <textarea name="alamat_lengkap" rows="2" class="form-control" placeholder="Jl. Sudirman No. 123, Kec. Setiabudi, Jakarta Selatan">{{ old('alamat_lengkap') }}</textarea>
            </div>
            <div class="row g-3">
                <div class="col-6">
                    <label class="form-label">Telepon <span class="text-muted-2 fw-normal">(opsional)</span></label>
                    <input type="text" name="telepon" value="{{ old('telepon') }}" class="form-control" placeholder="+62 21-xxxxxxx">
                </div>
                <div class="col-6">
                    <label class="form-label">Website <span class="text-muted-2 fw-normal">(opsional)</span></label>
                    <input type="url" name="website" value="{{ old('website') }}" class="form-control {{ $errors->has('website') ? 'is-invalid' : '' }}" placeholder="https://www.contoh.co.id">
                    @error('website')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row g-3">
                <div class="col-6">
                    <label class="form-label">Ukuran Perusahaan</label>
                    <select name="ukuran_perusahaan" class="form-select {{ $errors->has('ukuran_perusahaan') ? 'is-invalid' : '' }}">
                        <option value="">-- Pilih --</option>
                        @foreach(['1-10', '11-50', '51-200', '201-500', '500+'] as $u)
                            <option value="{{ $u }}" @selected(old('ukuran_perusahaan') === $u)>{{ $u }} karyawan</option>
                        @endforeach
                    </select>
                    @error('ukuran_perusahaan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-6">
                    <label class="form-label">Tahun Berdiri</label>
                    <input type="number" name="tahun_berdiri" value="{{ old('tahun_berdiri') }}" class="form-control {{ $errors->has('tahun_berdiri') ? 'is-invalid' : '' }}" min="1900" max="2030" placeholder="2015">
                    @error('tahun_berdiri')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div>
                <label class="form-label">Deskripsi Singkat <span class="text-muted-2 fw-normal">(opsional)</span></label>
                <textarea name="deskripsi" rows="2" class="form-control" placeholder="Sekilas tentang perusahaan Anda...">{{ old('deskripsi') }}</textarea>
            </div>
            <div class="alert alert-warning border-0 rounded-4 small mb-0">
                <i class="bi bi-info-circle me-1"></i>Setelah mendaftar, akun perusahaan Anda akan <b>diverifikasi oleh admin</b> sebelum dapat memposting lowongan.
            </div>
        </div>

        {{-- ===== Field bersama ===== --}}
        <div>
            <label class="form-label">Email</label>
            <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0 rounded-start-4"><i class="bi bi-envelope text-mk"></i></span>
                <input type="email" name="email" value="{{ old('email') }}" class="form-control rounded-start-0 {{ $errors->has('email') ? 'is-invalid' : '' }}" placeholder="nama@email.com" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div>
            <label class="form-label">Password</label>
            <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0 rounded-start-4"><i class="bi bi-lock text-mk"></i></span>
                <input type="password" name="password" class="form-control rounded-start-0 {{ $errors->has('password') ? 'is-invalid' : '' }}" placeholder="Minimal 8 karakter" required>
                <button type="button" class="btn btn-soft rounded-end-4 toggle-password" tabindex="-1" aria-label="Tampilkan password"><i class="bi bi-eye"></i></button>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div>
            <label class="form-label">Konfirmasi Password</label>
            <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0 rounded-start-4"><i class="bi bi-shield-lock text-mk"></i></span>
                <input type="password" name="password_confirmation" class="form-control rounded-start-0" placeholder="Ulangi password" required>
                <button type="button" class="btn btn-soft rounded-end-4 toggle-password" tabindex="-1" aria-label="Tampilkan password"><i class="bi bi-eye"></i></button>
            </div>
        </div>

        <button type="submit" class="btn btn-mk btn-lg">Daftar Gratis <i class="bi bi-arrow-right ms-1"></i></button>
    </form>

    <div class="text-center small text-muted-2 mt-4">
        Sudah punya akun? <a href="{{ route('login') }}" class="text-mk fw-semibold text-decoration-none">Login</a>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const pelamar = document.getElementById('fieldsPelamar');
            const perusahaan = document.getElementById('fieldsPerusahaan');
            const radios = document.querySelectorAll('input[name="role"]');

            const sync = () => {
                const isPerusahaan = document.getElementById('rolePerusahaan').checked;
                pelamar.classList.toggle('d-none', isPerusahaan);
                perusahaan.classList.toggle('d-none', !isPerusahaan);
                // Nonaktifkan field section tersembunyi agar tidak tervalidasi HTML
                pelamar.querySelectorAll('input,textarea').forEach(el => el.disabled = isPerusahaan);
                perusahaan.querySelectorAll('input,textarea').forEach(el => el.disabled = !isPerusahaan);
            };

            radios.forEach(r => r.addEventListener('change', sync));
            sync();
        });
    </script>
@endpush
