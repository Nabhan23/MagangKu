<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name', 'slug', 'description'];

    public const SUPER_ADMIN = 'super_admin';
    public const PERUSAHAAN = 'perusahaan';
    public const PELAMAR = 'pelamar';

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
