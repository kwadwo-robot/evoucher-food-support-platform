<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurplusAlert extends Model
{
    protected $fillable = [
        'food_listing_id',
        'organisation_user_id',
        'alert_sent_at',
        'expires_at',
        'status',
        'sequence_number',
    ];

    protected $casts = [
        'alert_sent_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function foodListing()
    {
        return $this->belongsTo(FoodListing::class);
    }

    public function organisation()
    {
        return $this->belongsTo(User::class, 'organisation_user_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }
}
