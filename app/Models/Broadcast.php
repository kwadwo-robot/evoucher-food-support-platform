<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Broadcast extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_user_id',
        'title',
        'message',
        'recipient_type',
        'recipient_role',
        'recipient_user_ids',
        'status',
        'scheduled_at',
        'sent_at',
        'recipients_count',
    ];

    protected $casts = [
        'recipient_user_ids' => 'array',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    /**
     * Get the admin user who created this broadcast.
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }

    /**
     * Get all read records for this broadcast.
     */
    public function reads()
    {
        return $this->hasMany(BroadcastRead::class);
    }

    /**
     * Get all delivery records for this broadcast.
     */
    public function deliveries()
    {
        return $this->hasMany(BroadcastDelivery::class);
    }

    /**
     * Get delivery statistics for this broadcast.
     */
    public function getDeliveryStats()
    {
        return [
            'total' => $this->deliveries()->count(),
            'sent' => $this->deliveries()->where('status', 'sent')->count(),
            'failed' => $this->deliveries()->where('status', 'failed')->count(),
            'pending' => $this->deliveries()->where('status', 'pending')->count(),
        ];
    }
}
