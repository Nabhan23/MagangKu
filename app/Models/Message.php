<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = ['lamaran_id', 'sender_id', 'receiver_id', 'pesan', 'is_read'];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function lamaran()
    {
        return $this->belongsTo(Lamaran::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
