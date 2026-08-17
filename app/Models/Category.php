<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['nama', 'slug', 'tipe', 'icon'];

    public function lowongans()
    {
        return $this->hasMany(Lowongan::class);
    }
}
