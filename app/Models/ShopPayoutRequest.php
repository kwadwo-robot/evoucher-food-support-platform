<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopPayoutRequest extends Model
{
    protected $fillable = [
        'shop_user_id',
        'total_amount',
        'service_fee_percentage',
        'service_fee_amount',
        'amount_after_fee',
        'redemption_count',
        'status',
        'payment_reference',
        'admin_notes',
        'processed_by',
        'paid_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'total_amount' => 'decimal:2',
        'service_fee_percentage' => 'decimal:2',
        'service_fee_amount' => 'decimal:2',
        'amount_after_fee' => 'decimal:2',
    ];

    public function shop()
    {
        return $this->belongsTo(User::class, 'shop_user_id');
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function redemptions()
    {
        return $this->hasMany(Redemption::class, 'payout_request_id');
    }

    public function serviceFeeTransaction()
    {
        return $this->hasOne(ServiceFeeTransaction::class, 'payout_request_id');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending'  => 'bg-yellow-100 text-yellow-800',
            'approved' => 'bg-blue-100 text-blue-800',
            'paid'     => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
            default    => 'bg-gray-100 text-gray-800',
        };
    }
}
