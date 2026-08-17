<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyReview extends Model
{
    protected $fillable = ['perusahaan_profile_id', 'pelamar_profile_id', 'rating', 'komentar', 'status'];

    public function perusahaan()
    {
        return $this->belongsTo(PerusahaanProfile::class, 'perusahaan_profile_id');
    }

    public function pelamar()
    {
        return $this->belongsTo(PelamarProfile::class, 'pelamar_profile_id');
    }
}
