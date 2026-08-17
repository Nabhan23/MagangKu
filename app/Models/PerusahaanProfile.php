<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerusahaanProfile extends Model
{
    protected $fillable = [
        'user_id', 'nama_perusahaan', 'slug', 'logo', 'industri', 'lokasi', 'alamat_lengkap', 'deskripsi',
        'website', 'email_kontak', 'telepon', 'ukuran_perusahaan', 'tahun_berdiri',
        'dokumen_verifikasi', 'status_verifikasi', 'pesan_verifikasi', 'is_featured',
    ];

    /**
     * URL navigasi Google Maps menuju alamat perusahaan.
     */
    public function getMapsUrlAttribute(): string
    {
        $q = trim(($this->alamat_lengkap ?: $this->lokasi ?: $this->nama_perusahaan));

        return 'https://www.google.com/maps/dir/?api=1&destination=' . urlencode($q);
    }

    protected $casts = [
        'tahun_berdiri' => 'integer',
        'is_featured' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lowongans()
    {
        return $this->hasMany(Lowongan::class);
    }

    public function reviews()
    {
        return $this->hasMany(CompanyReview::class);
    }

    public function followers()
    {
        return $this->hasMany(CompanyFollow::class);
    }

    public function getFollowersCountAttribute(): int
    {
        return $this->followers()->count();
    }

    public function getIsFollowedAttribute(): bool
    {
        if (! auth()->check()) {
            return false;
        }

        return $this->followers()->where('user_id', auth()->id())->exists();
    }

    public function approvedReviews()
    {
        return $this->hasMany(CompanyReview::class)->where('status', 'approved');
    }

    public function lowonganAktif()
    {
        return $this->lowongans()->where('status', 'aktif')->where('deadline', '>=', now()->toDateString());
    }

    public function getRatingAttribute()
    {
        return round($this->approvedReviews()->avg('rating') ?? 0, 1);
    }

    public function getLogoUrlAttribute()
    {
        if ($this->logo && file_exists(public_path('storage/' . $this->logo))) {
            return asset('storage/' . $this->logo);
        }
        return null;
    }

    public function getStatusVerifikasiBadgeAttribute()
    {
        return match ($this->status_verifikasi) {
            'verified' => ['success', 'Terverifikasi'],
            'rejected' => ['danger', 'Ditolak'],
            default => ['warning', 'Menunggu'],
        };
    }
}
