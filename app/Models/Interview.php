<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Interview extends Model
{
    protected $fillable = ['lamaran_id', 'tanggal', 'waktu', 'tipe', 'link', 'lokasi', 'catatan', 'status', 'kode', 'created_by'];

    /**
     * URL publik untuk QR interview.
     */
    public function publicUrl(): ?string
    {
        return $this->kode ? route('interview.public', $this->kode) : null;
    }

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function lamaran()
    {
        return $this->belongsTo(Lamaran::class);
    }
}
