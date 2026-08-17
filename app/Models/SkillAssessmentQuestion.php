<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkillAssessmentQuestion extends Model
{
    protected $fillable = ['skill_assessment_id', 'pertanyaan', 'opsi_a', 'opsi_b', 'opsi_c', 'opsi_d', 'jawaban_benar', 'pembahasan', 'urutan'];

    public const OPSI = ['a' => 'opsi_a', 'b' => 'opsi_b', 'c' => 'opsi_c', 'd' => 'opsi_d'];

    public function assessment()
    {
        return $this->belongsTo(SkillAssessment::class, 'skill_assessment_id');
    }

    public function getJawabanTextAttribute()
    {
        return $this->{self::OPSI[$this->jawaban_benar] ?? 'opsi_a'};
    }
}
