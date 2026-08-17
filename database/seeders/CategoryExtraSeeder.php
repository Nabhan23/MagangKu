<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategoryExtraSeeder extends Seeder
{
    /**
     * Kategori lowongan tambahan untuk semua jenis pekerjaan
     * (rumah sakit, bank, hukum, media, konstruksi, dll).
     * updateOrCreate by slug agar aman dijalankan ulang.
     */
    public function run(): void
    {
        $kategoriList = [
            ['Kesehatan & Medis', 'kesehatan-medis', 'lowongan', 'bi-heart-pulse'],
            ['Perbankan & Asuransi', 'perbankan-asuransi', 'lowongan', 'bi-bank'],
            ['Hukum & Legal', 'hukum-legal', 'lowongan', 'bi-shield-check'],
            ['Media & Komunikasi', 'media-komunikasi', 'lowongan', 'bi-broadcast'],
            ['Konstruksi & Properti', 'konstruksi-properti', 'lowongan', 'bi-building'],
            ['Logistik & Supply Chain', 'logistik-supply-chain', 'lowongan', 'bi-truck'],
            ['Pariwisata & Hospitality', 'pariwisata-hospitality', 'lowongan', 'bi-airplane'],
            ['Manufaktur & Industri', 'manufaktur-industri', 'lowongan', 'bi-gear-wide-connected'],
            ['Energi & Sumber Daya', 'energi-sumber-daya', 'lowongan', 'bi-lightning-charge'],
            ['Retail & E-commerce', 'retail-ecommerce', 'lowongan', 'bi-bag'],
            ['Transportasi', 'transportasi', 'lowongan', 'bi-bus-front'],
            ['Pemerintahan & BUMN', 'pemerintahan-bumn', 'lowongan', 'bi-building-check'],
            ['Pertanian & Pangan', 'pertanian-pangan', 'lowongan', 'bi-tree'],
            ['Telekomunikasi', 'telekomunikasi', 'lowongan', 'bi-wifi'],
        ];

        foreach ($kategoriList as [$nama, $slug, $tipe, $icon]) {
            Category::updateOrCreate(
                ['slug' => $slug],
                ['nama' => $nama, 'tipe' => $tipe, 'icon' => $icon],
            );
        }
    }
}
