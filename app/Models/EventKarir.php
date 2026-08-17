<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EventKarir extends Model
{
    protected $fillable = [
        'judul', 'slug', 'kategori', 'deskripsi', 'tanggal_mulai', 'tanggal_selesai', 'waktu',
        'lokasi', 'is_online', 'link_event', 'gambar', 'kapasitas', 'penyelenggara', 'status', 'views',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'is_online' => 'boolean',
    ];

    public const KATEGORI = ['webinar' => 'Webinar', 'job_fair' => 'Job Fair', 'workshop' => 'Workshop', 'campus_hiring' => 'Campus Hiring'];

    public function registrations()
    {
        return $this->hasMany(EventRegistration::class, 'event_id');
    }

    public function getKategoriLabelAttribute()
    {
        return self::KATEGORI[$this->kategori] ?? $this->kategori;
    }

    public function getTerdaftarAttribute()
    {
        return $this->registrations()->count();
    }

    protected static function booted(): void
    {
        static::creating(function (EventKarir $event) {
            $event->slug = $event->slug ?: Str::slug($event->judul) . '-' . Str::random(5);
        });
    }
}
