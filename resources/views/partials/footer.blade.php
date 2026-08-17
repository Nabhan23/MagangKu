<footer class="footer-mk pt-5 pb-4 mt-auto">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <x-logo variant="icon" height="40"/>
                    <span class="fw-bold fs-5 text-white" style="letter-spacing:1px;">MAGANG<span class="text-mk">KU</span></span>
                </div>
                <p class="small" style="color:rgba(255,255,255,.7);">
                    Platform rekrutmen dan magang terpercaya di Indonesia.
                    Menghubungkan talenta muda dengan perusahaan terbaik.
                    <span class="d-block mt-2 fst-italic">"Langkah Awal Menuju Karier Impian"</span>
                </p>
                <div class="d-flex gap-2 mt-3">
                    @php $socials = [
                        'Instagram' => ['social_instagram', 'bi-instagram'],
                        'LinkedIn' => ['social_linkedin', 'bi-linkedin'],
                        'Facebook' => ['social_facebook', 'bi-facebook'],
                        'TikTok' => ['social_tiktok', 'bi-tiktok'],
                        'YouTube' => ['social_youtube', 'bi-youtube'],
                    ]; @endphp
                    @foreach($socials as $label => [$key, $icon])
                        @php $url = \App\Models\Setting::get($key); @endphp
                        @if($url)
                            <a href="{{ $url }}" class="social-btn" title="{{ $label }}" target="_blank" rel="noopener"><i class="bi {{ $icon }}"></i></a>
                        @endif
                    @endforeach
                </div>
            </div>

            <div class="col-6 col-lg-2">
                <h6 class="text-white fw-bold mb-3">Perusahaan</h6>
                <ul class="list-unstyled small d-grid gap-2">
                    <li><a href="{{ route('jobs.index') }}">Cari Lowongan</a></li>
                    <li><a href="{{ route('jobs.index', ['jenis' => 'magang']) }}">Cari Magang</a></li>
                    <li><a href="{{ route('companies.index') }}">Daftar Perusahaan</a></li>
                    <li><a href="{{ route('register') }}">Pasang Lowongan</a></li>
                </ul>
            </div>

            <div class="col-6 col-lg-2">
                <h6 class="text-white fw-bold mb-3">Pelamar</h6>
                <ul class="list-unstyled small d-grid gap-2">
                    <li><a href="{{ route('events.index') }}">Event Karier</a></li>
                    <li><a href="{{ route('articles.index') }}">Tips Karier</a></li>
                    <li><a href="{{ route('faq') }}">FAQ</a></li>
                    <li><a href="{{ route('about') }}">Tentang Kami</a></li>
                </ul>
            </div>

            <div class="col-lg-4">
                <h6 class="text-white fw-bold mb-3">Kontak Kami</h6>
                <ul class="list-unstyled small d-grid gap-2" style="color:rgba(255,255,255,.7);">
                    <li><i class="bi bi-geo-alt me-2 text-mk"></i>{{ \App\Models\Setting::get('contact_address', 'Jl. Karier Impian No. 1, Jakarta Selatan') }}</li>
                    <li><i class="bi bi-envelope me-2 text-mk"></i>{{ \App\Models\Setting::get('contact_email', 'halo@magangku.id') }}</li>
                    <li><i class="bi bi-telephone me-2 text-mk"></i>{{ \App\Models\Setting::get('contact_phone', '+62 812-3456-7890') }}</li>
                    <li><i class="bi bi-clock me-2 text-mk"></i>{{ \App\Models\Setting::get('contact_hours', 'Senin - Jumat, 09.00 - 17.00 WIB') }}</li>
                </ul>
            </div>
        </div>

        <hr style="border-color:rgba(255,255,255,.15);">

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 small">
            <span style="color:rgba(255,255,255,.6);">© {{ date('Y') }} MagangKu. Semua hak dilindungi.</span>
            <div class="d-flex gap-3">
                <a href="{{ route('privacy') }}">Kebijakan Privasi</a>
                <a href="{{ route('terms') }}">Syarat & Ketentuan</a>
                <a href="{{ route('contact') }}">Kontak</a>
            </div>
        </div>
    </div>
</footer>
