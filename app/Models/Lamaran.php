<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lamaran extends Model
{
    protected $fillable = ['lowongan_id', 'pelamar_profile_id', 'status', 'offer_letter_path', 'catatan', 'file_pendukung'];

    public const STATUS = [
        'menunggu' => ['Menunggu', 'secondary'],
        'diproses' => ['Diproses', 'info'],
        'psikotes' => ['Psikotes', 'warning'],
        'interview' => ['Interview', 'primary'],
        'diterima' => ['Diterima', 'success'],
        'ditolak' => ['Ditolak', 'danger'],
    ];

    public const URUTAN = ['menunggu' => 0, 'diproses' => 1, 'psikotes' => 2, 'interview' => 3, 'diterima' => 4, 'ditolak' => 4];

    public function lowongan()
    {
        return $this->belongsTo(Lowongan::class);
    }

    public function pelamar()
    {
        return $this->belongsTo(PelamarProfile::class, 'pelamar_profile_id');
    }

    public function interview()
    {
        return $this->hasOne(Interview::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function feedback()
    {
        return $this->hasOne(CompanyFeedback::class);
    }

    public function getStatusLabelAttribute()
    {
        return self::STATUS[$this->status][0] ?? $this->status;
    }

    public function getStatusColorAttribute()
    {
        return self::STATUS[$this->status][1] ?? 'secondary';
    }
}
