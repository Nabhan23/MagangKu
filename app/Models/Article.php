<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Article extends Model
{
    protected $fillable = ['judul', 'slug', 'kategori', 'ringkasan', 'konten', 'gambar', 'penulis', 'published_at', 'status', 'views', 'is_featured'];

    protected $casts = [
        'published_at' => 'datetime',
        'is_featured' => 'boolean',
    ];

    public const KATEGORI = [
        'tips_cv_ats' => 'Tips CV ATS',
        'tips_interview' => 'Tips Interview',
        'dunia_kerja' => 'Dunia Kerja',
        'magang' => 'Magang',
        'pengembangan_skill' => 'Pengembangan Skill',
    ];

    public function getKategoriLabelAttribute()
    {
        return self::KATEGORI[$this->kategori] ?? $this->kategori;
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')->whereNotNull('published_at');
    }

    protected static function booted(): void
    {
        static::creating(function (Article $article) {
            $article->slug = $article->slug ?: Str::slug($article->judul);
        });
    }
}
