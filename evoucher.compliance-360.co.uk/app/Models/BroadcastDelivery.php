<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BroadcastDelivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'broadcast_id',
        'user_id',
        'email',
        'recipient_email',
        'recipient_name',
        'status',
        'error_message',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    /**
     * Get the broadcast associated with this delivery.
     */
    public function broadcast()
    {
        return $this->belongsTo(Broadcast::class);
    }

    /**
     * Get the user associated with this delivery.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
