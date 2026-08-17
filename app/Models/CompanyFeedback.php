<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyFeedback extends Model
{
    protected $table = 'company_feedbacks';

    protected $fillable = ['perusahaan_profile_id', 'lamaran_id', 'rating', 'feedback'];

    public function perusahaan()
    {
        return $this->belongsTo(PerusahaanProfile::class, 'perusahaan_profile_id');
    }

    public function lamaran()
    {
        return $this->belongsTo(Lamaran::class);
    }

    public function pelamar()
    {
        return $this->hasOneThrough(PelamarProfile::class, Lamaran::class, 'id', 'id', 'lamaran_id', 'pelamar_profile_id');
    }
}
