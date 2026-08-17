<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Lowongan extends Model
{
    protected $fillable = [
        'perusahaan_profile_id', 'category_id', 'created_by', 'posisi', 'slug', 'jenis',
        'deskripsi', 'persyaratan', 'gaji_min', 'gaji_max', 'lokasi', 'is_remote',
        'kuota', 'deadline', 'status', 'views',
    ];

    protected $casts = [
        'gaji_min' => 'integer',
        'gaji_max' => 'integer',
        'is_remote' => 'boolean',
        'deadline' => 'date',
    ];

    public const JENIS_LABEL = [
        'magang' => 'Magang',
        'full_time' => 'Full Time',
        'part_time' => 'Part Time',
        'freelance' => 'Freelance',
        'remote' => 'Remote',
    ];

    public function perusahaan()
    {
        return $this->belongsTo(PerusahaanProfile::class, 'perusahaan_profile_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'job_skills');
    }

    public function lamarans()
    {
        return $this->hasMany(Lamaran::class);
    }

    public function savedBy()
    {
        return $this->belongsToMany(User::class, 'saved_jobs');
    }

    public function getJenisLabelAttribute()
    {
        return self::JENIS_LABEL[$this->jenis] ?? $this->jenis;
    }

    public function getGajiLabelAttribute()
    {
        if (!$this->gaji_min && !$this->gaji_max) {
            return 'Gaji Kompetitif';
        }
        $fmt = fn ($v) => 'Rp ' . number_format($v, 0, ',', '.');
        if ($this->gaji_min && $this->gaji_max) {
            return $fmt($this->gaji_min) . ' - ' . $fmt($this->gaji_max);
        }
        return $this->gaji_min ? '≥ ' . $fmt($this->gaji_min) : '≤ ' . $fmt($this->gaji_max);
    }

    public function getIsMagangAttribute()
    {
        return $this->jenis === 'magang';
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'aktif' => ['success', 'Aktif'],
            'ditutup' => ['danger', 'Ditutup'],
            default => ['secondary', 'Draft'],
        };
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif')->where('deadline', '>=', now()->toDateString());
    }

    protected static function booted(): void
    {
        static::creating(function (Lowongan $lowongan) {
            $lowongan->slug = $lowongan->slug ?: Str::slug($lowongan->posisi) . '-' . Str::random(5);
        });
    }
}
