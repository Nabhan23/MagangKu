@extends('layouts.guest')

@section('title', 'Syarat & Ketentuan - MagangKu')

@section('content')
    <section class="section" style="padding-top:3rem;">
        <div class="container" style="max-width:820px;">
            <div class="text-center mb-5" data-aos="fade-up">
                <span class="badge-mk"><i class="bi bi-file-earmark-text me-1"></i>Legal</span>
                <h1 class="section-title mt-3">Syarat & Ketentuan</h1>
                <p class="text-muted-2">Terakhir diperbarui: {{ now()->translatedFormat('d F Y') }}</p>
            </div>

            <div class="card card-hover p-4 p-md-5 d-grid gap-4" data-aos="fade-up">
                @foreach([
                    ['title' => '1. Penerimaan Ketentuan', 'body' => "Dengan mengakses dan menggunakan platform MagangKu, Anda dianggap telah membaca, memahami, dan menyetujui seluruh syarat dan ketentuan yang berlaku."],
                    ['title' => '2. Akun Pengguna', 'body' => "Anda bertanggung jawab penuh atas kerahasiaan akun dan password Anda. MagangKu tidak bertanggung jawab atas penyalahgunaan akun akibat kelalaian pengguna."],
                    ['title' => '3. Peran Pengguna', 'body' => "Platform ini memiliki tiga peran: Pelamar, Perusahaan/HRD, dan Super Admin. Setiap pengguna hanya dapat mengakses fitur sesuai perannya masing-masing."],
                    ['title' => '4. Kewajiban Perusahaan', 'body' => "Perusahaan wajib memberikan informasi yang benar dan akurat terkait lowongan. Perusahaan yang terbukti melakukan penipuan akan dihapus dan dilaporkan ke pihak berwenang."],
                    ['title' => '5. Konten Pengguna', 'body' => "Pengguna bertanggung jawab atas konten yang diunggah, termasuk CV, portofolio, dan dokumen lainnya. Konten yang melanggar hukum atau norma akan dihapus."],
                    ['title' => '6. Batasan Tanggung Jawab', 'body' => "MagangKu hanyalah platform penghubung. Proses seleksi, wawancara, dan keputusan penerimaan sepenuhnya menjadi kewenangan perusahaan. MagangKu tidak menjamin diterimanya lamaran Anda."],
                    ['title' => '7. Perubahan Layanan', 'body' => "MagangKu berhak mengubah, menangguhkan, atau menghentikan sebagian atau seluruh layanan tanpa pemberitahuan sebelumnya."],
                    ['title' => '8. Hukum yang Berlaku', 'body' => "Syarat dan ketentuan ini diatur oleh hukum Republik Indonesia."],
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
