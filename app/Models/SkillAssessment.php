<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkillAssessment extends Model
{
    protected $fillable = ['judul', 'slug', 'kategori', 'deskripsi', 'durasi_menit', 'status'];

    public const KATEGORI = [
        'teknologi' => 'Teknologi & IT',
        'desain' => 'Desain & Kreatif',
        'marketing' => 'Marketing & Sales',
        'keuangan' => 'Keuangan & Akuntansi',
        'umum' => 'Umum & Soft Skill',
        'psikotes' => 'Psikotes & Tes Logika',
    ];

    public function questions()
    {
        return $this->hasMany(SkillAssessmentQuestion::class)->orderBy('urutan');
    }

    public function results()
    {
        return $this->hasMany(SkillAssessmentResult::class);
    }

    public function getKategoriLabelAttribute()
    {
        return self::KATEGORI[$this->kategori] ?? $this->kategori;
    }

    public function getJumlahPesertaAttribute()
    {
        return $this->results()->count();
    }
}
