@extends('layouts.guest')

@section('title', 'Kebijakan Privasi - MagangKu')

@section('content')
    <section class="section" style="padding-top:3rem;">
        <div class="container" style="max-width:820px;">
            <div class="text-center mb-5" data-aos="fade-up">
                <span class="badge-mk"><i class="bi bi-shield-lock me-1"></i>Privasi</span>
                <h1 class="section-title mt-3">Kebijakan Privasi</h1>
                <p class="text-muted-2">Terakhir diperbarui: {{ now()->translatedFormat('d F Y') }}</p>
            </div>

            <div class="card card-hover p-4 p-md-5 d-grid gap-4" data-aos="fade-up">
                @foreach([
                    ['title' => '1. Data yang Kami Kumpulkan', 'body' => "Kami mengumpulkan data yang Anda berikan saat mendaftar, seperti nama, email, nomor telepon, data profil (universitas, jurusan, CV), serta data penggunaan platform seperti riwayat lamaran, lowongan yang disimpan, dan aktivitas lainnya."],
                    ['title' => '2. Penggunaan Data', 'body' => "Data Anda digunakan untuk: (a) memproses lamaran dan menghubungkan Anda dengan perusahaan, (b) memberikan notifikasi terkait lamaran dan event, (c) meningkatkan kualitas layanan, serta (d) keperluan keamanan dan kepatuhan hukum."],
                    ['title' => '3. Perlindungan Data', 'body' => "Kami menerapkan langkah keamanan teknis dan organisasi yang memadai untuk melindungi data Anda dari akses tidak sah, perubahan, pengungkapan, atau penghancuran."],
                    ['title' => '4. Berbagi Data', 'body' => "Data profil dan CV Anda hanya dibagikan kepada perusahaan yang lowongannya Anda lamar. Kami tidak menjual data pribadi Anda kepada pihak ketiga."],
                    ['title' => '5. Hak Anda', 'body' => "Anda berhak mengakses, memperbaiki, atau menghapus data pribadi Anda. Hubungi kami melalui halaman Kontak untuk menjalankan hak tersebut."],
                    ['title' => '6. Perubahan Kebijakan', 'body' => "Kami dapat memperbarui kebijakan privasi ini sewaktu-waktu. Perubahan akan diumumkan melalui platform ini."],
                ] as $section)
                    <div>
                        <h5 class="fw-bold mb-2 text-mk">{{ $section['title'] }}</h5>
                        <p class="text-muted-2 mb-0">{{ $section['body'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
