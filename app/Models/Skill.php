<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    protected $fillable = ['nama', 'slug', 'kategori'];

    public const KATEGORI = [
        'teknologi' => 'Teknologi & IT',
        'desain' => 'Desain & Kreatif',
        'marketing' => 'Marketing & Sales',
        'keuangan' => 'Keuangan & Akuntansi',
        'sdm' => 'SDM & HR',
        'administrasi' => 'Administrasi & Perkantoran',
        'pendidikan' => 'Pendidikan',
        'media' => 'Media & Konten',
        'hukum' => 'Hukum & Legal',
        'kesehatan' => 'Kesehatan',
        'konstruksi' => 'Konstruksi & Teknik',
        'umum' => 'Umum & Soft Skill',
    ];

    public function lowongans()
    {
        return $this->belongsToMany(Lowongan::class, 'job_skills');
    }

    public function getKategoriLabelAttribute()
    {
        return self::KATEGORI[$this->kategori] ?? 'Umum & Soft Skill';
    }
}
