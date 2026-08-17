<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Banner;
use App\Models\CareerRoadmap;
use App\Models\Category;
use App\Models\SkillAssessment;
use App\Models\SkillAssessmentQuestion;
use App\Models\SkillAssessmentResult;
use App\Models\CompanyReview;
use App\Models\EventKarir;
use App\Models\EventRegistration;
use App\Models\Faq;
use App\Models\Interview;
use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\Notification;
use App\Models\PelamarProfile;
use App\Models\PerusahaanProfile;
use App\Models\Role;
use App\Models\SavedJob;
use App\Models\Skill;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /* ---------- Roles ---------- */
        foreach ([
            ['Super Admin', Role::SUPER_ADMIN, 'Mengelola seluruh platform'],
            ['Perusahaan / HRD', Role::PERUSAHAAN, 'Memasang lowongan dan merekrut'],
            ['Pelamar', Role::PELAMAR, 'Mencari magang dan pekerjaan'],
        ] as [$name, $slug, $desc]) {
            Role::create(compact('name', 'slug') + ['description' => $desc]);
        }

        $roleAdmin = Role::where('slug', Role::SUPER_ADMIN)->first();
        $rolePerusahaan = Role::where('slug', Role::PERUSAHAAN)->first();
        $rolePelamar = Role::where('slug', Role::PELAMAR)->first();

        /* ---------- Super Admin ---------- */
        User::create([
            'name' => 'Admin MagangKu',
            'email' => 'admin@magangku.id',
            'password' => 'password',
            'role_id' => $roleAdmin->id,
            'email_verified_at' => now(),
        ]);

        /* ---------- Kategori ---------- */
        $kategoriList = [
            ['Teknologi Informasi', 'teknologi-informasi', 'lowongan', 'bi-code-slash'],
            ['Desain & Kreatif', 'desain-kreatif', 'lowongan', 'bi-palette'],
            ['Marketing & Sales', 'marketing-sales', 'lowongan', 'bi-megaphone'],
            ['Keuangan & Akuntansi', 'keuangan-akuntansi', 'lowongan', 'bi-cash-coin'],
            ['Pendidikan', 'pendidikan', 'lowongan', 'bi-mortarboard'],
            ['Administrasi', 'administrasi', 'lowongan', 'bi-clipboard'],
        ];
        foreach ($kategoriList as [$nama, $slug, $tipe, $icon]) {
            Category::create(compact('nama', 'slug', 'tipe', 'icon'));
        }

        /* ---------- Kategori tambahan: semua jenis pekerjaan (RS, bank, dll) ---------- */
        $this->call(CategoryExtraSeeder::class);

        /* ---------- Skill (lengkap, semua bidang) ---------- */
        $this->call(SkillSeeder::class);
        $this->call(SkillExtraSeeder::class);

        /* ---------- Skill Assessment tambahan (psikotes & keuangan) ---------- */
        $this->call(SkillAssessmentExtraSeeder::class);

        /* ---------- Perusahaan ---------- */
        $companies = [
            ['hrd@teknologinusantara.id', 'PT Teknologi Nusantara', 'Teknologi Informasi', 'Jakarta Selatan', 'Perusahaan teknologi terkemuka yang mengembangkan solusi digital untuk berbagai industri di Indonesia.', 'teknologi-nusantara', true],
            ['hrd@digitalkreatif.id', 'PT Digital Kreatif Indonesia', 'Desain & Kreatif', 'Bandung', 'Kreator konten digital dan agensi desain dengan klien dari berbagai brand nasional.', 'digital-kreatif', true],
            ['hrd@bankmaju.id', 'Bank Maju Bersama', 'Perbankan', 'Jakarta Pusat', 'Bank nasional dengan jaringan luas dan layanan digital modern.', 'bank-maju-bersama', true],
            ['hrd@edukasipintar.id', 'PT Edukasi Pintar', 'Pendidikan', 'Yogyakarta', 'Platform edtech yang menghadirkan pembelajaran berkualitas untuk seluruh Indonesia.', 'edukasi-pintar', true],
            ['hrd@karyagemilang.id', 'PT Karya Gemilang', 'Konstruksi', 'Surabaya', 'Perusahaan konstruksi dan properti dengan proyek di seluruh Indonesia.', 'karya-gemilang', true],
            ['hrd@solusifinansial.id', 'PT Solusi Finansial', 'Fintech', 'Jakarta Selatan', 'Perusahaan fintech yang mempermudah akses keuangan digital bagi masyarakat.', 'solusi-finansial', true],
            ['hrd@mediainovasi.id', 'PT Media Inovasi', 'Media & Entertainment', 'Jakarta Utara', 'Rumah produksi konten dan media digital dengan jutaan penonton.', 'media-inovasi', true],
            ['hrd@agrolestari.id', 'PT Agro Lestari', 'Agrikultur', 'Medan', 'Perusahaan agribisnis yang berfokus pada produk pertanian berkelanjutan.', 'agro-lestari', true],
        ];

        $perusahaanProfiles = [];
        foreach ($companies as $i => [$email, $nama, $industri, $lokasi, $deskripsi, $logoSlug, $featured]) {
            $user = User::create([
                'name' => $nama,
                'email' => $email,
                'password' => 'password',
                'role_id' => $rolePerusahaan->id,
                'email_verified_at' => now(),
            ]);
            $perusahaanProfiles[] = PerusahaanProfile::create([
                'user_id' => $user->id,
                'nama_perusahaan' => $nama,
                'slug' => Str::slug($nama),
                'logo' => "img/companies/{$logoSlug}.svg",
                'industri' => $industri,
                'lokasi' => $lokasi,
                'deskripsi' => $deskripsi,
                'website' => 'https://www.' . $logoSlug . '.co.id',
                'email_kontak' => $email,
                'telepon' => '+62 21-' . rand(1000000, 9999999),
                'ukuran_perusahaan' => ['1-10', '11-50', '51-200', '201-500'][rand(0, 3)],
                'tahun_berdiri' => rand(2005, 2021),
                'status_verifikasi' => 'verified',
                'is_featured' => $featured,
            ]);
        }

        /* ---------- Pelamar ---------- */
        $pelamarData = [
            ['pelamar@magangku.id', 'Rahmat Syahputra', 'Universitas Indonesia', 'Teknik Informatika', 'Jakarta', 'L', 'Laravel, PHP, MySQL, Git, JavaScript'],
            ['sari.wulandari@gmail.com', 'Sari Wulandari', 'Universitas Gadjah Mada', 'Desain Komunikasi Visual', 'Yogyakarta', 'P', 'Figma, UI/UX, Adobe Photoshop, Illustrator'],
            ['budi.santoso@gmail.com', 'Budi Santoso', 'Institut Teknologi Bandung', 'Sistem Informasi', 'Bandung', 'L', 'Python, Data Analysis, Excel, SQL'],
            ['dewi.anggraini@gmail.com', 'Dewi Anggraini', 'Universitas Airlangga', 'Ilmu Komunikasi', 'Surabaya', 'P', 'Copywriting, SEO, Digital Marketing, Public Speaking'],
            ['ahmad.fauzi@gmail.com', 'Ahmad Fauzi', 'Universitas Brawijaya', 'Akuntansi', 'Malang', 'L', 'Excel, Akuntansi, Analisis Keuangan'],
            ['rini.kusuma@gmail.com', 'Rini Kusuma', 'Universitas Padjadjaran', 'Manajemen', 'Bandung', 'P', 'Manajemen, Komunikasi, Marketing'],
            ['fajar.prakoso@gmail.com', 'Fajar Prakoso', 'Universitas Diponegoro', 'Teknik Elektro', 'Semarang', 'L', 'Embedded System, Python, IoT'],
            ['nadia.putri@gmail.com', 'Nadia Putri', 'Universitas Bina Nusantara', 'Sistem Informasi', 'Jakarta', 'P', 'React, JavaScript, Tailwind CSS'],
            ['andi.prasetyo@gmail.com', 'Andi Prasetyo', 'Universitas Negeri Jakarta', 'Pendidikan', 'Jakarta', 'L', 'Mengajar, Komunikasi, Public Speaking'],
            ['maya.ramadhani@gmail.com', 'Maya Ramadhani', 'Universitas Hasanuddin', 'Ilmu Gizi', 'Makassar', 'P', 'Riset, Analisis Data, Komunikasi'],
            ['reza.firmansyah@gmail.com', 'Reza Firmansyah', 'Universitas Sriwijaya', 'Teknik Sipil', 'Palembang', 'L', 'AutoCAD, Manajemen Proyek, Excel'],
            ['lisa.permata@gmail.com', 'Lisa Permata', 'Universitas Telkom', 'Ilmu Komunikasi', 'Bandung', 'P', 'Content Creator, Copywriting, Social Media'],
        ];

        // CV contoh (PDF valid) — disimpan di disk privat (tidak bisa diakses publik)
        $cvDir = storage_path('app/private/cv-pelamar');
        File::ensureDirectoryExists($cvDir);
        $cvPath = 'cv-pelamar/contoh-cv.pdf';
        File::put($cvDir . '/contoh-cv.pdf', $this->makePdf('Contoh CV - MagangKu'));

        $pelamarProfiles = [];
        $badges = ['cv_lengkap', 'pelamar_aktif', 'top_talent', 'interview_expert'];
        foreach ($pelamarData as $i => [$email, $nama, $universitas, $jurusan, $lokasi, $jk, $keahlian]) {
            $user = User::create([
                'name' => $nama,
                'email' => $email,
                'password' => 'password',
                'role_id' => $rolePelamar->id,
                'phone' => '+62 8' . rand(1000000000, 9999999999),
                'email_verified_at' => now(),
            ]);
            $pelamarProfiles[] = PelamarProfile::create([
                'user_id' => $user->id,
                'nama_lengkap' => $nama,
                'jenis_kelamin' => $jk,
                'tanggal_lahir' => now()->subYears(rand(19, 26))->subMonths(rand(1, 11)),
                'universitas' => $universitas,
                'jurusan' => $jurusan,
                'pendidikan_terakhir' => ['D3', 'D4/S1'][rand(0, 1)],
                'tahun_lulus' => now()->year - rand(0, 3),
                'lokasi' => $lokasi,
                'keahlian' => $keahlian,
                'tentang' => "Mahasiswa aktif {$jurusan} di {$universitas} yang antusias mengembangkan karier. Memiliki kemampuan {$keahlian} serta pengalaman organisasi dan magang. Siap berkontribusi dan belajar hal baru.",
                'badge' => $badges[$i % count($badges)],
                'cv_path' => $i < 8 ? $cvPath : null,
                'views' => rand(10, 300),
            ]);
        }

        /* ---------- Lowongan ---------- */
        $lowonganList = [
            ['Frontend Developer', 'full_time', 0, 'teknologi-nusantara', 'Menjadi bagian tim pengembangan produk digital yang melayani jutaan pengguna.', "Persyaratan:\n- Minimal S1 Teknik Informatika/Sistem Informasi\n- Menguasai HTML, CSS, JavaScript\n- Pengalaman dengan framework modern (React/Vue)\n- Kemampuan bekerja dalam tim"],
            ['Backend Developer (Laravel)', 'full_time', 0, 'teknologi-nusantara', 'Mengembangkan dan memelihara API serta sistem backend menggunakan Laravel.', "Persyaratan:\n- Mahir PHP dan Laravel\n- Memahami MySQL dan optimasi query\n- Pengalaman REST API\n- Familiar dengan Git dan CI/CD"],
            ['UI/UX Designer', 'magang', 1, 'digital-kreatif', 'Program magang untuk desainer muda yang ingin mengasah kemampuan desain produk digital.', "Persyaratan:\n- Mahasiswa semester 5+ jurusan Desain/Informatika\n- Menguasai Figma\n- Memiliki portofolio desain\n- Kreatif dan detail"],
            ['Content Creator Intern', 'magang', 1, 'media-inovasi', 'Magang membuat konten kreatif untuk platform media sosial dengan jutaan audiens.', "Persyaratan:\n- Aktif di media sosial\n- Kemampuan menulis kreatif\n- Familiar dengan tren konten\n- Dapat mengoperasikan kamera/basic editing"],
            ['Data Analyst', 'full_time', 2, 'solusi-finansial', 'Menganalisis data keuangan untuk mendukung pengambilan keputusan bisnis.', "Persyaratan:\n- S1 Statistika/Informatika/Ekonomi\n- Mahir Excel dan SQL\n- Pengalaman Python/R nilai tambah\n- Kemampuan visualisasi data"],
            ['Digital Marketing Executive', 'full_time', 2, 'edukasi-pintar', 'Mengelola kampanye digital marketing untuk platform pembelajaran online.', "Persyaratan:\n- S1 Marketing/Komunikasi\n- Menguasai SEO dan Google Ads\n- Pengalaman media sosial\n- Analitik yang kuat"],
            ['Administrasi Keuangan', 'part_time', 3, 'karya-gemilang', 'Membantu proses administrasi keuangan proyek konstruksi.', "Persyaratan:\n- D3/S1 Akuntansi atau setara\n- Mahir Excel\n- Teliti dan jujur\n- Bisa bekerja paruh waktu"],
            ['Customer Service (Remote)', 'remote', 2, 'solusi-finansial', 'Melayani pertanyaan dan kebutuhan nasabah platform finansial digital.', "Persyaratan:\n- Komunikasi baik\n- Bisa bekerja remote\n- Familiar dengan aplikasi finansial\n- Siap bekerja shift"],
            ['Graphic Designer', 'freelance', 1, 'digital-kreatif', 'Mengerjakan proyek desain grafis untuk berbagai klien secara freelance.', "Persyaratan:\n- Mahir Adobe Photoshop/Illustrator\n- Portofolio kuat\n- Manajemen waktu baik\n- Bisa bekerja jarak jauh"],
            ['Guru Les Online', 'part_time', 4, 'edukasi-pintar', 'Mengajar siswa SD-SMA secara online untuk berbagai mata pelajaran.', "Persyaratan:\n- Mahasiswa pendidikan atau berpengalaman mengajar\n- Komunikasi baik\n- Koneksi internet stabil"],
            ['Research Assistant', 'magang', 5, 'agro-lestari', 'Membantu tim riset dalam pengumpulan dan analisis data pertanian.', "Persyaratan:\n- Mahasiswa pertanian/biologi\n- Kemampuan riset dasar\n- Familiar dengan Excel\n- Bersedia ke lapangan"],
            ['Mobile Developer Intern', 'magang', 0, 'teknologi-nusantara', 'Magang pengembangan aplikasi mobile menggunakan Flutter/React Native.', "Persyaratan:\n- Mahasiswa Informatika semester 5+\n- Dasar pemrograman mobile\n- Kemauan belajar tinggi"],
            ['Internal Audit Staff', 'full_time', 3, 'bank-maju-bersama', 'Membantu proses audit internal dan kepatuhan di lingkungan perbankan.', "Persyaratan:\n- S1 Akuntansi/Manajemen\n- Memahami standar audit\n- Integritas tinggi\n- Kemampuan analisis"],
            ['Human Capital Intern', 'magang', 3, 'bank-maju-bersama', 'Magang di divisi Human Capital untuk proses rekrutmen dan pengembangan karyawan.', "Persyaratan:\n- Mahasiswa Psikologi/Manajemen SDM\n- Komunikatif\n- Familiar dengan Microsoft Office"],
        ];

        $lowonganModels = [];
        $skillModel = Skill::all();
        foreach ($lowonganList as $i => [$posisi, $jenis, $catIdx, $companySlug, $deskripsi, $persyaratan]) {
            $perusahaan = $perusahaanProfiles[array_search($companySlug, array_column($companies, 5))];
            $minGaji = match ($jenis) {
                'magang' => rand(1000000, 2500000),
                'part_time' => rand(2000000, 3500000),
                'freelance' => rand(3000000, 5000000),
                'remote' => rand(3500000, 5500000),
                default => rand(5000000, 9000000),
            };
            $lowongan = Lowongan::create([
                'perusahaan_profile_id' => $perusahaan->id,
                'category_id' => $kategoriList[$catIdx] ? Category::where('slug', $kategoriList[$catIdx][1])->value('id') : null,
                'created_by' => $perusahaan->user_id,
                'posisi' => $posisi,
                'jenis' => $jenis,
                'deskripsi' => $deskripsi,
                'persyaratan' => $persyaratan,
                'gaji_min' => $minGaji,
                'gaji_max' => $minGaji + rand(1500000, 4000000),
                'lokasi' => $perusahaan->lokasi,
                'is_remote' => $jenis === 'remote',
                'kuota' => rand(1, 5),
                'deadline' => now()->addDays(rand(5, 35)),
                'status' => 'aktif',
                'views' => rand(50, 900),
            ]);
            $lowongan->skills()->attach($skillModel->random(min(4, $skillModel->count()))->pluck('id'));
            $lowonganModels[] = $lowongan;
        }

        /* ---------- Lamaran, Interview, Saved, Review ---------- */
        $statuses = ['menunggu', 'diproses', 'psikotes', 'interview', 'diterima', 'ditolak'];
        for ($i = 0; $i < 30; $i++) {
            $lowongan = $lowonganModels[array_rand($lowonganModels)];
            $pelamar = $pelamarProfiles[array_rand($pelamarProfiles)];
            if (Lamaran::where('lowongan_id', $lowongan->id)->where('pelamar_profile_id', $pelamar->id)->exists()) {
                continue;
            }
            $status = $statuses[array_rand($statuses)];
            $lamaran = Lamaran::create([
                'lowongan_id' => $lowongan->id,
                'pelamar_profile_id' => $pelamar->id,
                'status' => $status,
                'catatan' => rand(0, 1) ? 'Saya sangat tertarik dengan posisi ini dan yakin dapat memberikan kontribusi terbaik.' : null,
                'created_at' => now()->subDays(rand(1, 20)),
            ]);
            if ($status === 'interview') {
                Interview::create([
                    'lamaran_id' => $lamaran->id,
                    'tanggal' => now()->addDays(rand(1, 10)),
                    'waktu' => ['09:00', '10:30', '13:00', '15:00'][rand(0, 3)],
                    'tipe' => 'online',
                    'link' => 'https://meet.google.com/magangku-' . rand(100, 999),
                    'catatan' => 'Siapkan portofolio dan dokumen pendukung.',
                    'created_by' => $lowongan->perusahaan->user_id,
                ]);
            }
        }

        foreach ($pelamarProfiles as $i => $pelamar) {
            if ($i % 3 === 0 && isset($lowonganModels[$i % count($lowonganModels)])) {
                SavedJob::create(['user_id' => $pelamar->user_id, 'lowongan_id' => $lowonganModels[$i % count($lowonganModels)]->id]);
            }
        }

        $reviews = [
            ['Lingkungan kerja yang suportif, mentor yang sabar, dan banyak belajar hal baru. Sangat direkomendasikan!', 5],
            ['Kultur perusahaan yang baik, tapi beban kerja kadang cukup tinggi. Overall pengalaman yang berharga.', 4],
            ['Program magang terstruktur dengan baik, ada training dan feedback rutin dari supervisor.', 5],
            ['Komunikasi antar tim cukup baik, benefit menarik, tapi proses rekrutmen agak lama.', 4],
        ];
        for ($i = 0; $i < 12; $i++) {
            $perusahaan = $perusahaanProfiles[array_rand($perusahaanProfiles)];
            $pelamar = $pelamarProfiles[array_rand($pelamarProfiles)];
            if (CompanyReview::where('perusahaan_profile_id', $perusahaan->id)->where('pelamar_profile_id', $pelamar->id)->exists()) {
                continue;
            }
            [$komentar, $rating] = $reviews[array_rand($reviews)];
            CompanyReview::create([
                'perusahaan_profile_id' => $perusahaan->id,
                'pelamar_profile_id' => $pelamar->id,
                'rating' => $rating,
                'komentar' => $komentar,
            ]);
        }

        /* ---------- Skill Assessment ---------- */
        $assessments = [
            [
                'judul' => 'Tes Teknologi & IT',
                'kategori' => 'teknologi',
                'deskripsi' => 'Uji pemahaman dasarmu tentang dunia teknologi informasi, pemrograman, dan pengembangan web.',
                'soal' => [
                    ['Apa kepanjangan dari HTTP?', 'Hypertext Transfer Protocol', 'High Tech Transfer Process', 'Hyperlink Text Tool Protocol', 'Host Transfer Protocol', 'a', 'HTTP (Hypertext Transfer Protocol) adalah protokol untuk mengirim data di web.'],
                    ['Bahasa pemrograman apa yang digunakan oleh framework Laravel?', 'Python', 'PHP', 'JavaScript', 'Ruby', 'b', 'Laravel dibangun di atas bahasa PHP.'],
                    ['Apa fungsi utama CSS?', 'Mengatur tampilan dan layout halaman web', 'Menyimpan data pengguna', 'Membuat halaman interaktif', 'Menghubungkan ke server', 'a', 'CSS (Cascading Style Sheets) mengatur tampilan visual halaman web.'],
                    ['SQL digunakan untuk apa?', 'Membuat animasi', 'Mengelola dan mengquery database', 'Mengirim email', 'Mengompres file', 'b', 'SQL (Structured Query Language) adalah bahasa untuk mengelola database relasional.'],
                    ['Apa itu Git?', 'Bahasa pemrograman', 'Sistem kontrol versi untuk melacak perubahan kode', 'Framework frontend', 'Database NoSQL', 'b', 'Git adalah version control system yang melacak setiap perubahan kode.'],
                ],
            ],
            [
                'judul' => 'Tes Desain & Kreatif',
                'kategori' => 'desain',
                'deskripsi' => 'Ukur pemahamanmu tentang prinsip desain, tools desain, dan kreativitas visual.',
                'soal' => [
                    ['Software apa yang paling umum digunakan untuk desain UI/UX?', 'Adobe Premiere', 'Figma', 'Microsoft Word', 'AutoCAD', 'b', 'Figma adalah tools kolaboratif favorit untuk desain UI/UX.'],
                    ['Prinsip desain yang mengatur posisi elemen agar rapi disebut?', 'Alignment', 'Gradient', 'Opacity', 'Blur', 'a', 'Alignment memastikan elemen sejajar dan konsisten.'],
                    ['Apa itu wireframe?', 'Versi final desain', 'Sketsa dasar layout tanpa detail visual', 'Animasi interaksi', 'Warna tema', 'b', 'Wireframe adalah kerangka kasar layout sebelum desain visual dibuat.'],
                    ['Warna komplementer adalah...?', 'Warna yang sama', 'Warna netral', 'Warna pastel', 'Warna yang saling berlawanan di roda warna', 'd', 'Warna komplementer berada di posisi berlawanan pada color wheel.'],
                    ['Format gambar apa yang mendukung transparansi?', 'JPG', 'PNG', 'BMP', 'GIF animasi', 'b', 'PNG mendukung transparansi (alpha channel).'],
                ],
            ],
            [
                'judul' => 'Tes Marketing & Sales',
                'kategori' => 'marketing',
                'deskripsi' => 'Uji pengetahuan dasarmu tentang digital marketing, SEO, dan strategi penjualan.',
                'soal' => [
                    ['SEO adalah singkatan dari...?', 'Search Engine Optimization', 'Social Engagement Online', 'Sales Executive Order', 'System Error Output', 'a', 'SEO adalah optimasi agar website muncul di hasil pencarian.'],
                    ['Apa fungsi utama Google Ads?', 'Membuat website', 'Menampilkan iklan berbayar kepada audiens', 'Mengirim newsletter', 'Menganalisis competitor', 'b', 'Google Ads adalah platform iklan berbayar (PPC) milik Google.'],
                    ['CTR (Click Through Rate) adalah...?', 'Biaya per klik', 'Rasio jumlah klik dibanding tayangan iklan', 'Jumlah follower', 'Skor kualitas iklan', 'b', 'CTR mengukur seberapa efektif iklan menarik klik dari audiens.'],
                    ['Target pasar (target market) adalah...?', 'Semua orang di internet', 'Tim penjualan', 'Pesaing bisnis', 'Kelompok konsumen yang menjadi sasaran produk', 'd', 'Target market adalah segmen konsumen spesifik yang dituju bisnis.'],
                    ['Apa itu marketing funnel?', 'Alur perjalanan konsumen dari sadar hingga membeli', 'Iklan berbentuk corong', 'Rapat pemasaran', 'Riset kompetitor', 'a', 'Funnel menggambarkan tahapan konsumen: awareness → interest → decision → action.'],
                ],
            ],
            [
                'judul' => 'Tes Umum & Soft Skill',
                'kategori' => 'umum',
                'deskripsi' => 'Tes kemampuan dasar yang dicari semua perusahaan: komunikasi, kerja tim, dan cara kerja profesional.',
                'soal' => [
                    ['Metode STAR digunakan untuk...?', 'Menghitung gaji', 'Menjawab pertanyaan interview secara terstruktur', 'Membuat CV', 'Mengatur jadwal', 'b', 'STAR = Situation, Task, Action, Result — struktur jawaban interview yang efektif.'],
                    ['Manakah yang termasuk soft skill?', 'Python', 'Komunikasi efektif', 'Figma', 'Excel', 'b', 'Komunikasi adalah soft skill; sisanya hard skill teknis.'],
                    ['Apa arti kata "deadline"?', 'Batas waktu penyelesaian tugas', 'Awal pekerjaan', 'Istirahat siang', 'Target gaji', 'a', 'Deadline adalah batas waktu yang disepakati untuk menyelesaikan tugas.'],
                    ['Kerja tim yang baik ditandai dengan...?', 'Bekerja sendiri', 'Kompetisi antar anggota', 'Komunikasi terbuka dan saling mendukung', 'Menunggu instruksi', 'c', 'Tim yang sehat berkomunikasi terbuka dan saling mendukung.'],
                    ['Manakah yang termasuk cara profesional menerima kritik?', 'Membela diri', 'Mengabaikan', 'Mendengarkan, mengevaluasi, dan memperbaiki', 'Marah', 'c', 'Kritik diterima sebagai masukan untuk berkembang, bukan serangan pribadi.'],
                ],
            ],
        ];
        foreach ($assessments as $a) {
            $assessment = SkillAssessment::create([
                'judul' => $a['judul'],
                'slug' => Str::slug($a['judul']),
                'kategori' => $a['kategori'],
                'deskripsi' => $a['deskripsi'],
                'durasi_menit' => 10,
                'status' => 'aktif',
            ]);
            foreach ($a['soal'] as $i => [$pertanyaan, $opsiA, $opsiB, $opsiC, $opsiD, $benar, $pembahasan]) {
                SkillAssessmentQuestion::create([
                    'skill_assessment_id' => $assessment->id,
                    'pertanyaan' => $pertanyaan,
                    'opsi_a' => $opsiA,
                    'opsi_b' => $opsiB,
                    'opsi_c' => $opsiC,
                    'opsi_d' => $opsiD,
                    'jawaban_benar' => $benar,
                    'pembahasan' => $pembahasan,
                    'urutan' => $i,
                ]);
            }
        }

        // Beberapa hasil tes demo
        $assessmentTekno = SkillAssessment::where('kategori', 'teknologi')->first();
        $assessmentUmum = SkillAssessment::where('kategori', 'umum')->first();
        if ($assessmentTekno && $assessmentUmum) {
            $demoPelamar = $pelamarProfiles[0];
            $hasil = [
                [$demoPelamar->user_id, $assessmentTekno, 90, 5, 0],
                [$demoPelamar->user_id, $assessmentUmum, 60, 3, 2],
                [$pelamarProfiles[2]->user_id, $assessmentTekno, 80, 4, 1],
                [$pelamarProfiles[5]->user_id, $assessmentUmum, 100, 5, 0],
            ];
            foreach ($hasil as [$userId, $as, $skor, $benar, $salah]) {
                SkillAssessmentResult::create([
                    'user_id' => $userId,
                    'skill_assessment_id' => $as->id,
                    'skor' => $skor,
                    'total_soal' => $as->questions()->count(),
                    'benar' => $benar,
                    'salah' => $salah,
                    'status' => $skor >= 70 ? 'lulus' : 'gagal',
                ]);
            }
            // Badge Top Talent untuk demo pelamar
            $demoPelamar->update(['badge' => 'top_talent']);
        }

        /* ---------- Career Roadmap ---------- */
        $roadmaps = [
            [
                'judul' => 'Frontend Developer',
                'bidang' => 'teknologi',
                'ikon' => 'bi-window-desktop',
                'deskripsi' => 'Jalur menjadi pengembang tampilan web yang membuat produk digital terlihat dan terasa luar biasa.',
                'gaji' => [5000000, 15000000],
                'prospek' => 'Permintaan frontend developer terus meningkat seiring pertumbuhan industri digital. Prospek karier: Junior Developer → Mid Developer → Senior Developer → Tech Lead / Frontend Architect.',
                'tahapan' => [
                    ['judul' => 'Belajar HTML & CSS', 'durasi' => 2, 'deskripsi' => 'Pahami struktur halaman web dan styling. Kuasai flexbox, grid, dan responsive design.', 'skills' => ['HTML', 'CSS', 'Responsive Design']],
                    ['judul' => 'JavaScript Dasar', 'durasi' => 3, 'deskripsi' => 'Pelajari variabel, function, DOM manipulation, dan event handling untuk membuat halaman interaktif.', 'skills' => ['JavaScript', 'DOM', 'ES6']],
                    ['judul' => 'Framework Modern (React/Vue)', 'durasi' => 4, 'deskripsi' => 'Kuasi salah satu framework populer, pelajari component, state management, dan routing.', 'skills' => ['React', 'Vue.js', 'State Management']],
                    ['judul' => 'Git & Kolaborasi', 'durasi' => 1, 'deskripsi' => 'Belajar version control dan workflow kolaborasi tim: branching, pull request, code review.', 'skills' => ['Git', 'GitHub', 'Kolaborasi']],
                    ['judul' => 'Portofolio & Proyek Nyata', 'durasi' => 2, 'deskripsi' => 'Bangun 3-5 proyek nyata (landing page, dashboard, aplikasi) dan publish di GitHub.', 'skills' => ['Portofolio', 'Proyek']],
                    ['judul' => 'Lamaran & Interview', 'durasi' => 1, 'deskripsi' => 'Siapkan CV ATS, asah kemampuan technical test dan live coding interview.', 'skills' => ['CV', 'Interview', 'Komunikasi']],
                ],
            ],
            [
                'judul' => 'Backend Developer',
                'bidang' => 'teknologi',
                'ikon' => 'bi-hdd-stack',
                'deskripsi' => 'Jalur menjadi pengembang yang membangun logika, database, dan API di balik aplikasi.',
                'gaji' => [6000000, 18000000],
                'prospek' => 'Backend developer sangat dicari untuk membangun sistem yang stabil dan scalable. Prospek: Junior → Mid → Senior → Software Architect / Engineering Manager.',
                'tahapan' => [
                    ['judul' => 'Dasar PHP & OOP', 'durasi' => 3, 'deskripsi' => 'Kuasi sintaks PHP, konsep OOP: class, inheritance, polymorphism.', 'skills' => ['PHP', 'OOP']],
                    ['judul' => 'MySQL & Database', 'durasi' => 3, 'deskripsi' => 'Belajar desain database, relasi antar tabel, indexing, dan optimasi query.', 'skills' => ['MySQL', 'SQL', 'Database Design']],
                    ['judul' => 'Framework Laravel', 'durasi' => 4, 'deskripsi' => 'Pelajari MVC, Eloquent, migration, authentication, dan middleware di Laravel.', 'skills' => ['Laravel', 'Eloquent', 'MVC']],
                    ['judul' => 'REST API & Testing', 'durasi' => 2, 'deskripsi' => 'Bangun REST API dengan autentikasi token dan tulis unit/feature test.', 'skills' => ['REST API', 'Testing', 'Postman']],
                    ['judul' => 'Deploy & Server', 'durasi' => 2, 'deskripsi' => 'Belajar deploy ke server, environment setup, dan monitoring dasar.', 'skills' => ['Linux', 'Nginx', 'Deploy']],
                ],
            ],
            [
                'judul' => 'UI/UX Designer',
                'bidang' => 'desain',
                'ikon' => 'bi-palette',
                'deskripsi' => 'Jalur menjadi desainer yang menciptakan pengalaman digital yang mudah digunakan dan indah.',
                'gaji' => [4500000, 14000000],
                'prospek' => 'Perusahaan semakin sadar pentingnya pengalaman pengguna. Prospek: Junior Designer → UI/UX Designer → Product Designer → Design Lead.',
                'tahapan' => [
                    ['judul' => 'Fundamental Desain', 'durasi' => 2, 'deskripsi' => 'Pelajari prinsip desain, tipografi, warna, dan layout.', 'skills' => ['Prinsip Desain', 'Tipografi', 'Warna']],
                    ['judul' => 'Tools Desain', 'durasi' => 3, 'deskripsi' => 'Kuasi Figma untuk membuat design file, komponen, dan auto layout.', 'skills' => ['Figma', 'Adobe XD']],
                    ['judul' => 'Riset & UX', 'durasi' => 3, 'deskripsi' => 'Belajar user research, user persona, user flow, dan information architecture.', 'skills' => ['User Research', 'Persona', 'User Flow']],
                    ['judul' => 'Prototype & Testing', 'durasi' => 2, 'deskripsi' => 'Buat prototype interaktif dan lakukan usability testing.', 'skills' => ['Prototype', 'Usability Testing']],
                    ['judul' => 'Design System & Portofolio', 'durasi' => 2, 'deskripsi' => 'Pelajari design system dan susun case study portofolio yang kuat.', 'skills' => ['Design System', 'Portofolio']],
                ],
            ],
            [
                'judul' => 'Data Analyst',
                'bidang' => 'teknologi',
                'ikon' => 'bi-bar-chart-line',
                'deskripsi' => 'Jalur menjadi analis yang mengubah data mentah menjadi keputusan bisnis yang cerdas.',
                'gaji' => [5500000, 16000000],
                'prospek' => 'Data adalah aset berharga di era digital. Prospek: Data Analyst → Senior Analyst → Data Scientist / Analytics Manager.',
                'tahapan' => [
                    ['judul' => 'Excel & Spreadsheet', 'durasi' => 2, 'deskripsi' => 'Kuasi pivot table, formula lanjutan, dan visualisasi data di Excel/Google Sheets.', 'skills' => ['Excel', 'Google Sheets']],
                    ['judul' => 'SQL & Database', 'durasi' => 3, 'deskripsi' => 'Belajar query SQL untuk mengambil dan mengolah data dari database.', 'skills' => ['SQL', 'MySQL', 'PostgreSQL']],
                    ['judul' => 'Statistika Dasar', 'durasi' => 2, 'deskripsi' => 'Pelajari statistik deskriptif, probabilitas, dan pengujian hipotesis.', 'skills' => ['Statistika', 'Probabilitas']],
                    ['judul' => 'Python & Pandas', 'durasi' => 4, 'deskripsi' => 'Kuasi Python dengan Pandas dan NumPy untuk analisis data otomatis.', 'skills' => ['Python', 'Pandas', 'NumPy']],
                    ['judul' => 'Visualisasi & Storytelling', 'durasi' => 2, 'deskripsi' => 'Belajar Tableau/Power BI dan menyajikan insight yang mudah dipahami.', 'skills' => ['Tableau', 'Power BI', 'Storytelling']],
                ],
            ],
            [
                'judul' => 'Digital Marketing',
                'bidang' => 'marketing',
                'ikon' => 'bi-megaphone',
                'deskripsi' => 'Jalur menjadi marketer digital yang memasarkan produk dan brand secara online.',
                'gaji' => [4000000, 13000000],
                'prospek' => 'Bisnis terus bergeser ke digital. Prospek: Marketing Executive → Specialist → Manager → Head of Marketing.',
                'tahapan' => [
                    ['judul' => 'Fundamental Marketing Digital', 'durasi' => 2, 'deskripsi' => 'Pahami lanskap digital marketing: channel, funnel, dan KPI.', 'skills' => ['Digital Marketing', 'Funnel', 'KPI']],
                    ['judul' => 'SEO & Content', 'durasi' => 3, 'deskripsi' => 'Belajar on-page/off-page SEO dan strategi content marketing.', 'skills' => ['SEO', 'Content Marketing', 'Copywriting']],
                    ['judul' => 'Social Media Marketing', 'durasi' => 2, 'deskripsi' => 'Kelola akun media sosial, buat konten, dan analisis engagement.', 'skills' => ['Instagram', 'TikTok', 'LinkedIn']],
                    ['judul' => 'Iklan Berbayar', 'durasi' => 3, 'deskripsi' => 'Kuasi Google Ads dan Meta Ads: targeting, budget, dan optimasi kampanye.', 'skills' => ['Google Ads', 'Meta Ads', 'PPC']],
                    ['judul' => 'Analitik & Sertifikasi', 'durasi' => 2, 'deskripsi' => 'Pelajari Google Analytics dan ambil sertifikasi resmi Google/Meta.', 'skills' => ['Google Analytics', 'Sertifikasi']],
                ],
            ],
            [
                'judul' => 'Content Creator',
                'bidang' => 'konten',
                'ikon' => 'bi-camera-reels',
                'deskripsi' => 'Jalur menjadi kreator konten profesional yang membangun audiens dan personal branding.',
                'gaji' => [3500000, 12000000],
                'prospek' => 'Industri konten tumbuh pesat. Prospek: Content Creator → Specialist → Content Lead → Brand Strategist.',
                'tahapan' => [
                    ['judul' => 'Dasar Konten & Niche', 'durasi' => 2, 'deskripsi' => 'Tentukan niche, pelajari tren platform, dan analisis konten kompetitor.', 'skills' => ['Niche', 'Riset Tren']],
                    ['judul' => 'Copywriting & Storytelling', 'durasi' => 3, 'deskripsi' => 'Kuasi teknik menulis konten yang menarik dan bercerita.', 'skills' => ['Copywriting', 'Storytelling']],
                    ['judul' => 'Produksi & Editing', 'durasi' => 3, 'deskripsi' => 'Belajar shooting, editing video/foto dengan tools gratis maupun profesional.', 'skills' => ['Editing Video', 'Canva', 'CapCut']],
                    ['judul' => 'Platform & Algoritma', 'durasi' => 2, 'deskripsi' => 'Pahami algoritma Instagram, TikTok, dan YouTube serta strategi growth.', 'skills' => ['Instagram', 'TikTok', 'YouTube']],
                    ['judul' => 'Personal Branding & Monetisasi', 'durasi' => 2, 'deskripsi' => 'Bangun personal branding konsisten dan pelajari opsi monetisasi.', 'skills' => ['Personal Branding', 'Monetisasi']],
                ],
            ],
        ];
        foreach ($roadmaps as $r) {
            CareerRoadmap::create([
                'judul' => $r['judul'],
                'slug' => Str::slug($r['judul']),
                'bidang' => $r['bidang'],
                'ikon' => $r['ikon'],
                'deskripsi' => $r['deskripsi'],
                'tahapan' => $r['tahapan'],
                'gaji_min' => $r['gaji'][0],
                'gaji_max' => $r['gaji'][1],
                'prospek' => $r['prospek'],
                'status' => 'aktif',
            ]);
        }

        // Demo pelamar mengikuti jalur Frontend Developer
        $roadmapFrontend = CareerRoadmap::where('slug', 'frontend-developer')->first();
        if ($roadmapFrontend) {
            $pelamarProfiles[0]->update([
                'career_roadmap_id' => $roadmapFrontend->id,
                'roadmap_progress' => [0, 1],
            ]);
        }

        /* ---------- Event Karier ---------- */
        $events = [
            ['Webinar: Strategi CV ATS yang Dilirik Recruiter', 'webinar', now()->addDays(7), 'Online (Zoom)', true, 'https://zoom.us/j/magangku-webinar', 'Kementerian Ketenagakerjaan', 500],
            ['Job Fair Nasional 2026', 'job_fair', now()->addDays(20), 'Jakarta Convention Center', false, null, 'MagangKu x BUMN', 1000],
            ['Workshop: Taklukkan Interview Kerja', 'workshop', now()->addDays(12), 'Online (Zoom)', true, 'https://zoom.us/j/magangku-workshop', 'Tim MagangKu', 300],
            ['Campus Hiring: IT Batch 2026', 'campus_hiring', now()->addDays(30), 'Universitas Indonesia', false, null, 'PT Teknologi Nusantara', 200],
        ];
        $eventModels = [];
        foreach ($events as [$judul, $kategori, $tanggal, $lokasi, $online, $link, $penyelenggara, $kapasitas]) {
            $eventModels[] = EventKarir::create([
                'judul' => $judul,
                'kategori' => $kategori,
                'deskripsi' => "Bergabunglah di event karier \"{$judul}\" yang diselenggarakan oleh {$penyelenggara}. Event ini akan memberikan wawasan berharga untuk mempersiapkan kariermu. GRATIS untuk semua peserta!\n\nYang akan kamu dapatkan:\n- Materi dari praktisi berpengalaman\n- Sesi tanya jawab interaktif\n- Sertifikat digital partisipasi",
                'tanggal_mulai' => $tanggal,
                'tanggal_selesai' => $tanggal->copy()->addHours(3),
                'waktu' => '10:00',
                'lokasi' => $lokasi,
                'is_online' => $online,
                'link_event' => $link,
                'kapasitas' => $kapasitas,
                'penyelenggara' => $penyelenggara,
                'status' => 'aktif',
                'views' => rand(100, 600),
            ]);
        }
        // Beberapa registrasi event
        foreach ($pelamarProfiles as $i => $pelamar) {
            if ($i % 2 === 0) {
                EventRegistration::create([
                    'event_id' => $eventModels[$i % count($eventModels)]->id,
                    'user_id' => $pelamar->user_id,
                    'nama' => $pelamar->nama,
                    'email' => $pelamar->user->email,
                ]);
            }
        }

        /* ---------- Artikel ---------- */
        $articles = [
            ['7 Tips Membuat CV ATS-Friendly yang Dilirik Recruiter', 'tips_cv_ats', 'CV adalah gerbang pertamamu menuju interview. Namun, banyak CV yang bagus secara desain justru gagal lolos sistem ATS (Applicant Tracking System) yang digunakan perusahaan.\n\nBerikut 7 tipsnya:\n\n1. Gunakan format sederhana tanpa tabel dan kolom kompleks\n2. Gunakan kata kunci dari deskripsi lowongan\n3. Simpan dalam format PDF dengan nama file profesional\n4. Tulis pencapaian dengan angka, bukan sekadar tanggung jawab\n5. Gunakan font standar seperti Arial atau Calibri\n6. Pastikan urutan informasi logis: data diri, ringkasan, pengalaman, pendidikan, skill\n7. Periksa ejaan dan konsistensi format\n\nDengan menerapkan tips ini, peluang CV-mu dilirik recruiter akan meningkat drastis!', 'Tim MagangKu'],
            ['10 Pertanyaan Interview yang Sering Muncul dan Cara Menjawabnya', 'tips_interview', 'Interview kerja seringkali menegangkan, apalagi jika tidak siap. Berikut 10 pertanyaan paling umum beserta strategi menjawabnya.\n\n1. Ceritakan tentang diri Anda\n2. Mengapa Anda melamar posisi ini?\n3. Apa kelebihan dan kekurangan Anda?\n4. Di mana Anda melihat diri Anda 5 tahun ke depan?\n5. Mengapa kami harus memilih Anda?\n6. Ceritakan pengalaman kerja tim Anda\n7. Bagaimana Anda menghadapi tekanan?\n8. Apa gaji yang Anda harapkan?\n9. Apakah Anda punya pertanyaan?\n10. Kapan Anda bisa mulai bekerja?\n\nKunci suksesnya: gunakan metode STAR (Situation, Task, Action, Result) untuk menjawab pertanyaan perilaku.', 'Andi Wijaya'],
            ['Fresh Graduate? Ini 5 Strategi Dapat Kerja Pertamamu', 'dunia_kerja', 'Mencari kerja pertama memang penuh tantangan. Namun dengan strategi yang tepat, kamu bisa mempercepat prosesnya.\n\n1. Bangun personal branding di LinkedIn\n2. Ikut program magang dan volunteer\n3. Perluas jaringan lewat event karier\n4. Asah skill yang banyak dicari perusahaan\n5. Jangan takut melamar ke banyak tempat\n\nIngat, proses mencari kerja adalah proses belajar. Setiap penolakan membawamu lebih dekat ke kesempatan yang tepat!', 'Sari Wulandari'],
            ['Magang Pertama: Panduan Lengkap untuk Mahasiswa', 'magang', 'Magang adalah jembatan antara dunia kampus dan dunia kerja. Berikut panduan lengkapnya.\n\nSebelum magang:\n- Riset perusahaan dan posisi yang dilamar\n- Siapkan CV dan portofolio\n- Pelajari budaya kerja perusahaan\n\nSaat magang:\n- Datang tepat waktu dan berpakaian profesional\n- Aktif bertanya dan meminta feedback\n- Bangun relasi dengan rekan kerja\n- Catat semua pencapaianmu\n\nSetelah magang:\n- Minta surat rekomendasi\n- Update CV dengan pengalaman magangmu\n- Jaga hubungan baik dengan mentor\n\nMagang yang baik bisa menjadi tiket menuju pekerjaan tetap!', 'Tim MagangKu'],
            ['Skill Paling Dicari Perusahaan di Tahun 2026', 'pengembangan_skill', 'Dunia kerja terus berubah. Berikut skill yang paling dicari perusahaan saat ini.\n\nHard Skill:\n- Data analysis & SQL\n- Programming (terutama web & mobile)\n- Digital marketing & SEO\n- UI/UX design\n- Cloud computing\n\nSoft Skill:\n- Komunikasi efektif\n- Adaptabilitas\n- Pemecahan masalah\n- Kerja sama tim\n- Manajemen waktu\n\nKombinasi hard skill dan soft skill adalah kunci untuk menonjol di mata recruiter.', 'Budi Santoso'],
            ['Cara Membangun Personal Branding di LinkedIn untuk Mahasiswa', 'pengembangan_skill', 'LinkedIn adalah platform terbaik untuk membangun personal branding profesional. Bagi mahasiswa, ini adalah investasi jangka panjang yang sangat berharga.\n\nLangkah awal:\n1. Gunakan foto profesional\n2. Tulis headline yang menarik\n3. Lengkapi profil dengan pengalaman organisasi\n4. Tulis ringkasan yang mencerminkan tujuan kariermu\n5. Aktif berbagi konten dan insight\n6. Bangun koneksi dengan profesional di bidangmu\n\nMulailah hari ini, karena rekruter aktif mencari kandidat di LinkedIn!', 'Rini Kusuma'],
        ];
        foreach ($articles as [$judul, $kategori, $konten, $penulis]) {
            Article::create([
                'judul' => $judul,
                'kategori' => $kategori,
                'ringkasan' => Str::limit(strip_tags($konten), 120),
                'konten' => $konten,
                'penulis' => $penulis,
                'published_at' => now()->subDays(rand(1, 30)),
                'status' => 'published',
                'views' => rand(100, 1500),
                'is_featured' => rand(0, 1) === 1,
            ]);
        }

        /* ---------- Testimoni ---------- */
        $testimonials = [
            ['Rahmat Syahputra', 'Universitas Indonesia', 'Frontend Developer di PT Teknologi Nusantara', 'Berkat MagangKu saya mendapatkan magang pertama yang akhirnya menjadi pekerjaan tetap. Fitur tracking lamarannya sangat membantu!'],
            ['Sari Wulandari', 'Universitas Gadjah Mada', 'UI/UX Designer', 'Proses lamaran di MagangKu sangat mudah dan transparan. Saya bisa melihat status lamaran secara realtime. Sangat direkomendasikan!'],
            ['Budi Santoso', 'Institut Teknologi Bandung', 'Data Analyst', 'Suka banget sama fitur portofolio publiknya. Recruiter bisa langsung lihat karya saya. Alhamdulillah dapat tawaran kerja dalam 2 minggu!'],
            ['Dewi Anggraini', 'Universitas Airlangga', 'Content Writer', 'Event karier di MagangKu sangat bermanfaat. Saya ikut webinar CV ATS dan langsung dipraktekkan, hasilnya CV saya banyak dilirik recruiter.'],
            ['Ahmad Fauzi', 'Universitas Brawijaya', 'Staff Keuangan', 'Sebagai fresh graduate, MagangKu membantu saya menemukan perusahaan terverifikasi sehingga aman dari penipuan lowongan. Terima kasih MagangKu!'],
            ['Nadia Putri', 'Universitas Bina Nusantara', 'Mobile Developer', 'Perusahaan di MagangKu berkualitas dan prosesnya cepat. Saya dapat interview dalam seminggu setelah melamar. Recommended banget!'],
        ];
        foreach ($testimonials as [$nama, $universitas, $role, $komentar]) {
            Testimonial::create(compact('nama', 'universitas', 'role', 'komentar'));
        }

        /* ---------- FAQ ---------- */
        $faqs = [
            ['Apa itu MagangKu?', 'MagangKu adalah platform rekrutmen dan magang yang menghubungkan mahasiswa, fresh graduate, dan pencari kerja dengan perusahaan terpercaya di seluruh Indonesia.', 'umum'],
            ['Apakah pendaftaran di MagangKu gratis?', 'Ya, pendaftaran dan penggunaan fitur dasar MagangKu 100% gratis untuk pelamar.', 'umum'],
            ['Bagaimana cara melamar lowongan?', 'Daftar akun sebagai Pelamar, lengkapi profil dan unggah CV, lalu klik tombol "Lamar Sekarang" pada lowongan yang diminati.', 'pelamar'],
            ['Bagaimana cara melacak status lamaran?', 'Masuk ke menu "Tracking Lamaran" di dashboard pelamar. Status akan diperbarui secara realtime oleh perusahaan.', 'pelamar'],
            ['Apakah CV saya aman?', 'CV Anda hanya akan dilihat oleh perusahaan yang lowongannya Anda lamar. Kami tidak membagikan data Anda ke pihak ketiga.', 'privasi'],
            ['Bagaimana cara perusahaan memasang lowongan?', 'Daftar sebagai Perusahaan, lengkapi profil, kirim dokumen verifikasi, dan setelah terverifikasi Anda bisa langsung memposting lowongan.', 'perusahaan'],
            ['Berapa lama proses verifikasi perusahaan?', 'Proses verifikasi umumnya memakan waktu 1-3 hari kerja setelah dokumen diterima.', 'perusahaan'],
            ['Apakah event di MagangKu gratis?', 'Sebagian besar event MagangKu gratis. Beberapa event kerja sama mungkin memiliki biaya yang akan diinformasikan di halaman event.', 'event'],
        ];
        foreach ($faqs as $i => [$pertanyaan, $jawaban, $kategori]) {
            Faq::create(compact('pertanyaan', 'jawaban', 'kategori') + ['urutan' => $i]);
        }

        /* ---------- Banner ---------- */
        Banner::create([
            'judul' => 'Job Fair Nasional 2026',
            'teks' => 'Daftar sekarang dan temukan 1.000+ lowongan dari 200 perusahaan!',
            'gambar' => 'img/events/jobfair-nasional.svg',
            'link' => route('events.index'),
            'posisi' => 'home',
            'urutan' => 1,
        ]);

        /* ---------- Notifikasi contoh ---------- */
        Notification::create([
            'user_id' => User::where('email', 'pelamar@magangku.id')->value('id'),
            'judul' => 'Selamat Datang di MagangKu! 🎉',
            'pesan' => 'Lengkapi profil dan unggah CV untuk mulai melamar lowongan impianmu.',
            'type' => 'broadcast',
            'link' => route('pelamar.profile.edit'),
        ]);
        Notification::create([
            'user_id' => User::where('email', 'hrd@teknologinusantara.id')->value('id'),
            'judul' => 'Perusahaan Anda Terverifikasi ✅',
            'pesan' => 'Selamat! PT Teknologi Nusantara telah terverifikasi. Anda kini bisa memposting lowongan.',
            'type' => 'verifikasi',
            'link' => route('perusahaan.lowongan.index'),
        ]);

        $this->command->info('Seeder MagangKu selesai!');
        $this->command->info('Akun demo:');
        $this->command->info('  Admin     : admin@magangku.id / password');
        $this->command->info('  Perusahaan: hrd@teknologinusantara.id / password');
        $this->command->info('  Pelamar   : pelamar@magangku.id / password');
    }

    protected function makePdf(string $title): string
    {
        $content = "BT /F1 20 Tf 72 720 Td (" . $title . ") Tj ET";
        $objects = [
            "1 0 obj\n<</Type/Catalog/Pages 2 0 R>>\nendobj\n",
            "2 0 obj\n<</Type/Pages/Kids[3 0 R]/Count 1>>\nendobj\n",
            "3 0 obj\n<</Type/Page/Parent 2 0 R/MediaBox[0 0 612 792]/Contents 4 0 R/Resources<</Font<</F1 5 0 R>>>>>>\nendobj\n",
            "4 0 obj\n<</Length " . strlen($content) . ">>\nstream\n" . $content . "\nendstream\nendobj\n",
            "5 0 obj\n<</Type/Font/Subtype/Type1/BaseFont/Helvetica>>\nendobj\n",
        ];
        $pdf = "%PDF-1.4\n";
        $offsets = [];
        foreach ($objects as $obj) {
            $offsets[] = strlen($pdf);
            $pdf .= $obj;
        }
        $xref = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";
        foreach ($offsets as $o) {
            $pdf .= sprintf("%010d 00000 n \n", $o);
        }
        $pdf .= "trailer\n<</Size " . (count($objects) + 1) . "/Root 1 0 R>>\nstartxref\n" . $xref . "\n%%EOF";

        return $pdf;
    }
}
