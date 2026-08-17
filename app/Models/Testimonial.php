<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = ['nama', 'universitas', 'role', 'komentar', 'foto', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
