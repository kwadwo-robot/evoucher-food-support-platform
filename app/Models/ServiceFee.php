<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceFee extends Model
{
    protected $table = 'service_fees';

    protected $fillable = [
        'shop_user_id',
        'payout_request_id',
        'payout_amount',
        'service_fee_percentage',
        'service_fee_amount',
        'amount_after_fee',
        'status',
        'notes',
    ];

    protected $casts = [
        'payout_amount' => 'decimal:2',
        'service_fee_percentage' => 'decimal:2',
        'service_fee_amount' => 'decimal:2',
        'amount_after_fee' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the shop user associated with this service fee
     */
    public function shopUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'shop_user_id');
    }

    /**
     * Get the payout request associated with this service fee
     */
    public function payoutRequest(): BelongsTo
    {
        return $this->belongsTo(PayoutRequest::class, 'payout_request_id');
    }

    /**
     * Calculate service fee for a given amount
     */
    public static function calculateFee($amount, $percentage = null)
    {
        $percentage = $percentage ?? ServiceFeeSetting::getCurrentPercentage();
        $feeAmount = ($amount * $percentage) / 100;
        $amountAfterFee = $amount - $feeAmount;

        return [
            'service_fee_percentage' => $percentage,
            'service_fee_amount' => round($feeAmount, 2),
            'amount_after_fee' => round($amountAfterFee, 2),
        ];
    }

    /**
     * Create a service fee record for a payout
     */
    public static function createForPayout($shopUserId, $payoutRequestId, $payoutAmount)
    {
        $feeData = self::calculateFee($payoutAmount);

        return self::create([
            'shop_user_id' => $shopUserId,
            'payout_request_id' => $payoutRequestId,
            'payout_amount' => $payoutAmount,
            'service_fee_percentage' => $feeData['service_fee_percentage'],
            'service_fee_amount' => $feeData['service_fee_amount'],
            'amount_after_fee' => $feeData['amount_after_fee'],
            'status' => 'collected',
        ]);
    }

    /**
     * Get total service fees collected
     */
    public static function getTotalCollected()
    {
        return self::where('status', 'collected')->sum('service_fee_amount');
    }

    /**
     * Get total service fees for a specific shop
     */
    public static function getTotalForShop($shopUserId)
    {
        return self::where('shop_user_id', $shopUserId)
            ->where('status', 'collected')
            ->sum('service_fee_amount');
    }
}
