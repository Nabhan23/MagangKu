<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PelamarProfile extends Model
{
    protected $fillable = [
        'user_id', 'nama_lengkap', 'tanggal_lahir', 'tempat_lahir', 'jenis_kelamin', 'foto', 'cv_path',
        'universitas', 'jurusan', 'pendidikan_terakhir', 'tahun_lulus', 'lokasi', 'alamat', 'ekspektasi_gaji', 'tentang',
        'keahlian', 'website_portfolio', 'linkedin', 'github', 'badge', 'is_portfolio_public', 'views',
        'career_roadmap_id', 'roadmap_progress',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tahun_lulus' => 'integer',
        'is_portfolio_public' => 'boolean',
        'roadmap_progress' => 'array',
    ];

    public function roadmap()
    {
        return $this->belongsTo(CareerRoadmap::class, 'career_roadmap_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lamarans()
    {
        return $this->hasMany(Lamaran::class);
    }

    public function reviews()
    {
        return $this->hasMany(CompanyReview::class);
    }

    public function getNamaAttribute()
    {
        return $this->nama_lengkap ?: $this->user?->name;
    }

    public function getFotoUrlAttribute()
    {
        if ($this->foto && file_exists(public_path('storage/' . $this->foto))) {
            return asset('storage/' . $this->foto);
        }
        return $this->user?->avatar ? asset('storage/' . $this->user->avatar) : null;
    }
}
