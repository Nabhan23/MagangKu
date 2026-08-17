<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = ['judul', 'teks', 'gambar', 'link', 'posisi', 'urutan', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
