<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkillAssessmentResult extends Model
{
    protected $fillable = ['user_id', 'skill_assessment_id', 'skor', 'total_soal', 'benar', 'salah', 'detail', 'status'];

    protected $casts = [
        'detail' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assessment()
    {
        return $this->belongsTo(SkillAssessment::class, 'skill_assessment_id');
    }

    public function getIsLulusAttribute()
    {
        return $this->status === 'lulus';
    }

    public function getIsTopTalentAttribute()
    {
        return $this->skor >= 85;
    }

    /**
     * Nomor sertifikat unik: MK-{tahun}-{id:5 digit}.
     */
    public function getNoSertifikatAttribute(): string
    {
        return 'MK-' . now()->year . '-' . str_pad((string) $this->id, 5, '0', STR_PAD_LEFT);
    }
}
