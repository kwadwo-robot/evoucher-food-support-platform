<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Redemption extends Model
{
    protected $fillable = [
        'voucher_id', 'food_listing_id', 'shop_user_id', 'recipient_user_id',
        'amount_used', 'amount_owed_at_shop', 'payment_collected', 'payment_method',
        'status', 'redeemed_at', 'notes', 'payout_request_id',
    ];

    protected $casts = [
        'redeemed_at'         => 'datetime',
        'amount_used'         => 'decimal:2',
        'amount_owed_at_shop' => 'decimal:2',
        'payment_collected'   => 'boolean',
    ];

    public function voucher() { return $this->belongsTo(Voucher::class); }
    public function foodListing() { return $this->belongsTo(FoodListing::class); }
    public function recipient() { return $this->belongsTo(User::class, 'recipient_user_id'); }
    public function shop() { return $this->belongsTo(User::class, 'shop_user_id'); }
    public function payoutRequest() { return $this->belongsTo(ShopPayoutRequest::class, 'payout_request_id'); }
}
