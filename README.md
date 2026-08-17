# 🎓 MagangKu

Platform **magang & rekrutmen** berbasis web untuk menghubungkan **pelamar magang** dengan **perusahaan** di Indonesia. Dibangun dengan Laravel 11 + Bootstrap 5, lengkap dengan halaman admin, HRD, dan pelamar.

## ✨ Fitur Utama

### 👤 Pelamar
- Profil lengkap (data diri, pendidikan, CV, foto, ekspektasi gaji)
- Melamar lowongan & tracking status lamaran (timeline)
- Chat langsung dengan perusahaan
- Notifikasi in-app & email (bisa diatur preferensinya)
- Unduh surat penerimaan (Offer Letter PDF) saat diterima
- Simpan lowongan, ikuti perusahaan, skill assessment, career roadmap

### 🏢 Perusahaan (HRD)
- Dashboard statistik: pelamar per bulan, distribusi status, top lowongan
- Kelola lowongan & kategori (20+ bidang: IT, Kesehatan, Perbankan, dll.)
- Kelola pelamar: ubah status, jadwalkan interview, kirim feedback
- **Kalender interview** bulanan
- Aksi massal ubah status pelamar
- Chat dengan pelamar

### 🛡️ Admin
- Dashboard & laporan sistem (PDF/Excel/CSV)
- Verifikasi perusahaan (dokumen legal)
- Kelola pengguna, lowongan, dan seluruh konten (event, artikel, banner, FAQ, testimoni, kategori, skill)
- Broadcast notifikasi, aktivitas log, pengaturan kontak & media sosial

### 🔒 Keamanan
- Verifikasi email saat daftar
- Rate-limit login (anti brute-force)
- CV & dokumen verifikasi tersimpan privat (akses terotorisasi)
- Otorisasi berbasis role di semua endpoint

## 🖼️ Screenshot Halaman Publik

| Landing Page | Login |
|---|---|
| ![Landing Page](screenshots/public-landing.png) | ![Login](screenshots/public-login.png) |

| Halaman Daftar |
|---|
| ![Halaman Daftar](screenshots/public-daftar.png) |

## 🖼️ Screenshot Admin

| Dashboard | Kelola Lowongan |
|---|---|
| ![Dashboard Admin](screenshots/admin-dashboard.png) | ![Kelola Lowongan](screenshots/admin-lowongan.png) |

| Kelola Pengguna | Verifikasi Perusahaan |
|---|---|
| ![Kelola Pengguna](screenshots/admin-pengguna.png) | ![Verifikasi Perusahaan](screenshots/admin-verifikasi.png) |

| Kelola Konten | Pengaturan Kontak |
|---|---|
| ![Kelola Konten](screenshots/admin-konten.png) | ![Pengaturan Kontak](screenshots/admin-pengaturan.png) |

| Aktivitas Log | Laporan Sistem |
|---|---|
| ![Aktivitas Log](screenshots/admin-aktivitas.png) | ![Laporan Sistem](screenshots/admin-laporan.png) |

## 🖼️ Screenshot Pelamar

| Dashboard | Profil & CV |
|---|---|
| ![Dashboard Pelamar](screenshots/pelamar-dashboard.png) | ![Profil & CV](screenshots/pelamar-profil.png) |

| Tracking Lamaran | Chat dengan Perusahaan |
|---|---|
| ![Tracking Lamaran](screenshots/pelamar-lamaran.png) | ![Chat](screenshots/pelamar-chat.png) |

## 🖼️ Screenshot Perusahaan (HRD)

| Dashboard HRD | Kelola Lowongan |
|---|---|
| ![Dashboard HRD](screenshots/perusahaan-dashboard.png) | ![Kelola Lowongan](screenshots/perusahaan-lowongan.png) |

| Kelola Pelamar | Kalender Interview |
|---|---|
| ![Kelola Pelamar](screenshots/perusahaan-pelamar.png) | ![Kalender Interview](screenshots/perusahaan-kalender.png) |

## 🧰 Tech Stack

- **Backend:** Laravel 11, MySQL
- **Frontend:** Bootstrap 5, Vite, AOS
- **Fitur:** DomPDF (offer letter & laporan), QR Code, Laravel Socialite, PWA (service worker)

## 🔑 Akun Demo

| Role | Email | Password |
|---|---|---|
| Admin | `admin@magangku.id` | `password` |
| Perusahaan | `hrd@digitalkreatif.id` | `password` |
| Pelamar | `pelamar@magangku.id` | `password` |

## 🚀 Instalasi Lokal

```bash
# 1. Clone & install
git clone https://github.com/Nabhan23/MagangKu.git
cd MagangKu
composer install
npm install

# 2. Konfigurasi
cp .env.example .env
php artisan key:generate
# atur koneksi database di .env (DB_DATABASE, DB_USERNAME, DB_PASSWORD)

# 3. Migrasi & seed
php artisan migrate --seed

# 4. Jalankan
php artisan serve
npm run build
```

Buka `http://127.0.0.1:8000` — lalu login dengan akun demo di atas.

## 📄 Lisensi

Proyek ini dibuat untuk keperluan pembelajaran & pengembangan karier magang.
