<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CareerRoadmap extends Model
{
    protected $fillable = ['judul', 'slug', 'bidang', 'ikon', 'deskripsi', 'tahapan', 'gaji_min', 'gaji_max', 'prospek', 'status'];

    protected $casts = [
        'tahapan' => 'array',
    ];

    public const BIDANG = [
        'teknologi' => 'Teknologi & IT',
        'desain' => 'Desain & Kreatif',
        'marketing' => 'Marketing & Sales',
        'keuangan' => 'Keuangan & Akuntansi',
        'konten' => 'Konten & Media',
        'umum' => 'Bidang Lainnya',
    ];

    public function getBidangLabelAttribute()
    {
        return self::BIDANG[$this->bidang] ?? $this->bidang;
    }

    public function getTotalTahapanAttribute()
    {
        return count($this->tahapan ?? []);
    }

    public function getEstimasiDurasiAttribute()
    {
        $bulan = array_sum(array_map(fn ($t) => (int) ($t['durasi'] ?? 0), $this->tahapan ?? []));
        if ($bulan < 12) {
            return $bulan . ' bulan';
        }
        $tahun = floor($bulan / 12);
        $sisa = $bulan % 12;
        return $tahun . ' tahun' . ($sisa ? ' ' . $sisa . ' bulan' : '');
    }

    public function getGajiRangeAttribute()
    {
        if (!$this->gaji_min) {
            return 'Beragam';
        }
        $fmt = fn ($n) => 'Rp ' . number_format($n, 0, ',', '.');
        return $this->gaji_max ? $fmt($this->gaji_min) . ' – ' . $fmt($this->gaji_max) : $fmt($this->gaji_min) . '+';
    }
}
