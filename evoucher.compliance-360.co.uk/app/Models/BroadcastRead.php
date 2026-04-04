<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BroadcastRead extends Model
{
    protected $fillable = [
        'broadcast_id',
        'user_id',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function broadcast()
    {
        return $this->belongsTo(Broadcast::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
