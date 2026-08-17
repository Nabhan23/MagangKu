<?php

namespace Database\Seeders;

use App\Models\SkillAssessment;
use App\Models\SkillAssessmentQuestion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SkillAssessmentExtraSeeder extends Seeder
{
    public function run(): void
    {
        $assessments = [
            [
                'judul' => 'Tes Psikotes & Logika',
                'kategori' => 'psikotes',
                'deskripsi' => 'Tryout psikotes yang sering dipakai perusahaan: tes logika, numerik, verbal, dan penalaran.',
                'durasi' => 15,
                'soal' => [
                    ['Jika semua kucing adalah hewan, dan semua hewan bernapas, maka...', 'Semua kucing bernapas', 'Semua hewan adalah kucing', 'Kucing tidak bernapas', 'Sebagian hewan adalah kucing', 'a', 'Silogisme: jika semua kucing hewan dan semua hewan bernapas, maka semua kucing pasti bernapas.'],
                    ['Lanjutkan deret: 2, 4, 8, 16, ...', '24', '32', '30', '20', 'b', 'Pola: dikali 2 setiap langkah (16 × 2 = 32).'],
                    ['Arah: Anda berjalan 3 langkah ke utara, lalu 4 langkah ke timur. Jarak lurus dari titik awal?', '5 langkah', '7 langkah', '12 langkah', '1 langkah', 'a', 'Teorema Pythagoras: √(3² + 4²) = √25 = 5.'],
                    ['Pilih kata yang TIDAK sejenis:', 'Apel', 'Mangga', 'Wortel', 'Jeruk', 'c', 'Wortel adalah sayur; apel, mangga, dan jeruk adalah buah.'],
                    ['Jika BUKU = 4, maka PENSIL = ? (jumlah huruf vokal)', '1', '2', '3', '4', 'b', 'Vokal pada PENSIL adalah E dan I → 2 huruf vokal.'],
                ],
            ],
            [
                'judul' => 'Tes Keuangan & Akuntansi',
                'kategori' => 'keuangan',
                'deskripsi' => 'Uji pemahaman dasar akuntansi, laporan keuangan, dan istilah keuangan perusahaan.',
                'durasi' => 12,
                'soal' => [
                    ['Persamaan dasar akuntansi adalah...', 'Aset = Liabilitas + Ekuitas', 'Aset = Pendapatan - Beban', 'Liabilitas = Aset + Ekuitas', 'Ekuitas = Aset + Liabilitas', 'a', 'Persamaan dasar: Aset = Liabilitas + Ekuitas.'],
                    ['Laporan yang menunjukkan laba/rugi perusahaan disebut...', 'Neraca', 'Laporan Laba Rugi', 'Laporan Arus Kas', 'Laporan Perubahan Modal', 'b', 'Laba rugi (income statement) menunjukkan pendapatan dan beban dalam satu periode.'],
                    ['Apa itu aset lancar (current assets)?', 'Aset yang bisa menjadi kas dalam < 1 tahun', 'Aset tetap seperti gedung', 'Utang jangka panjang', 'Modal pemilik', 'a', 'Aset lancar: kas, piutang, persediaan — dapat dikonversi menjadi kas dalam satu tahun.'],
                    ['Depresiasi adalah...', 'Penurunan nilai aset tetap seiring waktu', 'Kenaikan harga saham', 'Pembayaran utang', 'Penambahan modal', 'a', 'Depresiasi mengalokasikan biaya aset tetap selama masa manfaatnya.'],
                    ['Rasio yang mengukur kemampuan membayar utang jangka pendek adalah...', 'ROE', 'Current Ratio', 'Debt to Equity', 'Gross Profit Margin', 'b', 'Current ratio = aset lancar ÷ liabilitas lancar (likuiditas jangka pendek).'],
                ],
            ],
        ];

        foreach ($assessments as $a) {
            $assessment = SkillAssessment::firstOrCreate(
                ['judul' => $a['judul']],
                [
                    'slug' => Str::slug($a['judul']),
                    'kategori' => $a['kategori'],
                    'deskripsi' => $a['deskripsi'],
                    'durasi_menit' => $a['durasi'],
                    'status' => 'aktif',
                ]
            );

            if ($assessment->questions()->exists()) {
                continue; // sudah ada, jangan duplikat soal
            }

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

        $this->command->info('Skill assessment tambahan (psikotes & keuangan) siap!');
    }
}
