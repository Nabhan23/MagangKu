<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SkillExtraSeeder extends Seeder
{
    /**
     * Skill tambahan untuk semua bidang pekerjaan — melengkapi SkillSeeder.
     * updateOrCreate by slug agar aman dijalankan ulang.
     */
    public function run(): void
    {
        $skills = [
            'kesehatan' => [
                'Dokter Umum', 'Dokter Spesialis', 'Dokter Gigi', 'Radiologi', 'Anestesi', 'Anestesiologi',
                'Rekam Medis', 'Koding Medis', 'Manajemen Rumah Sakit', 'Medikal Check Up', 'Homecare',
                'Terapi Wicara', 'Optometri', 'Epidemiologi', 'Administrasi Kesehatan', 'Keselamatan Pasien',
                'Diagnosa Medis', 'Pemeriksaan Laboratorium', 'Ibu dan Anak (KIA)', 'Posyandu', 'Imunisasi',
            ],
            'perbankan' => [
                'Teller', 'Customer Banking Officer', 'Customer Relationship Officer', 'Analis Kredit',
                'Kepatuhan Perbankan', 'Anti Money Laundering', 'Treasury', 'Foreign Exchange', 'Remittance',
                'Kredit Analisis', 'Manajemen Aset', 'Wealth Management', 'Digital Banking', 'Mobile Banking',
                'Payment Gateway', 'E-wallet', 'Asuransi', 'Underwriting', 'Klaim Asuransi', 'Agen Asuransi',
                'Aktuaria', 'Rekonsiliasi Bank', 'Transaksi Keuangan', 'Kartu Kredit',
            ],
            'hukum' => [
                'Hukum Perdata', 'Hukum Pidana', 'Hukum Pajak', 'Hukum Perbankan', 'Legal Officer',
                'Notaris', 'Legal Research', 'Mediasi & Arbitrase', 'Hak Kekayaan Intelektual (HKI)',
                'Data Protection', 'Legal Due Diligence', 'Paralegal',
            ],
            'media' => [
                'SEO Content', 'Copy Editing', 'Presenter', 'Voice Over', 'Animasi 2D', 'Animasi 3D',
                'Manajemen Media Sosial', 'Digital PR', 'News Writing', 'Feature Writing', 'Liputan Lapangan',
            ],
            'konstruksi' => [
                'Quantity Surveyor', 'HSE (Health Safety Environment)', 'Manajemen Konstruksi',
                'Proyek Infrastruktur', 'Interior Design', 'Plumbing', 'HVAC', 'Struktur Bangunan',
                'Jalan & Jembatan', 'Pengawasan Proyek', 'Gambar Teknik', 'BIM (Building Information Modeling)',
            ],
            'logistik' => [
                'Manajemen Gudang', 'Supply Chain', 'Inventory Management', 'Shipping', 'Freight Forwarding',
                'Ekspedisi', 'Distribusi', 'Purchasing', 'Procurement', 'Warehouse Management System',
                'Last Mile Delivery', 'Kepabeanan (Customs)', 'Penjadwalan Pengiriman',
            ],
            'hospitality' => [
                'Hotel Management', 'Front Office', 'Housekeeping', 'Food & Beverage Service', 'Barista',
                'Chef', 'Event Organizer', 'Tour Guide', 'Travel Consultant', 'Restaurant Management',
                'Banquet Service', 'Reservation', 'Guest Relation', 'Pastry & Bakery', 'Mixology',
            ],
            'manufaktur' => [
                'Quality Control', 'Quality Assurance', 'Production Planning', 'Lean Manufacturing',
                'Six Sigma', 'Operator Produksi', 'Maintenance', 'Welding', 'CNC', 'Teknik Industri',
                'Industri Kimia', 'Hazard Analysis', 'PPIC', 'Automasi Industri', 'Pengemasan',
            ],
            'energi' => [
                'Energi Terbarukan', 'Tenaga Surya', 'Tenaga Angin', 'Pembangkit Listrik', 'Oil & Gas',
                'HSE Energi', 'Grid & Distribusi', 'Pertambangan', 'Geologi', 'Geoteknik', 'Drilling',
                'Elektrikal Power', 'Manajemen Energi', 'Audit Energi',
            ],
            'retail' => [
                'Merchandising', 'Visual Merchandising', 'Retail Management', 'Store Operations',
                'E-commerce', 'Marketplace', 'Dropshipping', 'Brand Ambassador', 'Cashier',
                'Omnichannel', 'Category Management', 'Retail Sales', 'Inventory Retail',
            ],
            'transportasi' => [
                'Manajemen Armada', 'Logistik Transportasi', 'Transportasi Publik', 'Safety Driving',
                'Traffic Management', 'Pelayaran', 'Penerbangan', 'Ground Handling', 'Pengemudi',
                'Dispatcher', 'Perawatan Kendaraan', 'Rute & Penjadwalan',
            ],
            'pemerintahan' => [
                'Administrasi Pemerintahan', 'Kebijakan Publik', 'Pelayanan Publik',
                'Perencanaan Pembangunan', 'Humas Pemerintahan', 'Statistik', 'Penyusunan Laporan',
                'Manajemen BUMN', 'Good Governance', 'Pengadaan Barang & Jasa',
            ],
            'pertanian' => [
                'Agribisnis', 'Agronomi', 'Peternakan', 'Perikanan', 'Teknologi Pangan',
                'Pertanian Organik', 'Hortikultura', 'Food Processing', 'Budidaya Tanaman',
                'Agroteknologi', 'Quality Control Pangan', 'Manajemen Kebun', 'Irigasi',
            ],
            'telekomunikasi' => [
                'Jaringan Telekomunikasi', '5G', 'Fiber Optik', 'Radio Frequency', 'Telekomunikasi Seluler',
                'IP Network', 'VoIP', 'Network Engineering', 'Optical Transmission', 'BTS', 'Core Network',
            ],
        ];

        foreach ($skills as $kategori => $daftar) {
            foreach ($daftar as $nama) {
                Skill::updateOrCreate(
                    ['slug' => Str::slug($nama)],
                    ['nama' => $nama, 'kategori' => $kategori],
                );
            }
        }
    }
}
