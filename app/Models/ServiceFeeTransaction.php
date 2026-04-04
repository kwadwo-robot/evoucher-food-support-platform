<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceFeeTransaction extends Model
{
    protected $fillable = [
        'payout_request_id',
        'shop_user_id',
        'total_amount',
        'service_fee_percentage',
        'service_fee_amount',
        'amount_after_fee',
        'status',
        'notes',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'service_fee_percentage' => 'decimal:2',
        'service_fee_amount' => 'decimal:2',
        'amount_after_fee' => 'decimal:2',
    ];

    public function payoutRequest()
    {
        return $this->belongsTo(ShopPayoutRequest::class, 'payout_request_id');
    }

    public function shop()
    {
        return $this->belongsTo(User::class, 'shop_user_id');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending'   => 'bg-yellow-100 text-yellow-800',
            'collected' => 'bg-green-100 text-green-800',
            'refunded'  => 'bg-red-100 text-red-800',
            default     => 'bg-gray-100 text-gray-800',
        };
    }
}
