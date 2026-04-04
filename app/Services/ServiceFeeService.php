<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\ShopPayoutRequest;
use App\Models\ServiceFeeTransaction;

class ServiceFeeService
{
    /**
     * Get the current service fee percentage from settings
     */
    public static function getServiceFeePercentage(): float
    {
        return (float) Setting::get('service_fee_percentage', 10.00);
    }

    /**
     * Set the service fee percentage
     */
    public static function setServiceFeePercentage(float $percentage): void
    {
        if ($percentage < 0 || $percentage > 100) {
            throw new \InvalidArgumentException('Service fee percentage must be between 0 and 100');
        }
        Setting::set('service_fee_percentage', $percentage);
    }

    /**
     * Calculate service fee for a given amount
     */
    public static function calculateServiceFee(float $amount, ?float $percentage = null): array
    {
        $percentage = $percentage ?? self::getServiceFeePercentage();
        $serviceFeeAmount = round($amount * ($percentage / 100), 2);
        $amountAfterFee = round($amount - $serviceFeeAmount, 2);

        return [
            'total_amount' => $amount,
            'service_fee_percentage' => $percentage,
            'service_fee_amount' => $serviceFeeAmount,
            'amount_after_fee' => $amountAfterFee,
        ];
    }

    /**
     * Apply service fee to a payout request
     */
    public static function applyServiceFeeToPayoutRequest(ShopPayoutRequest $payout): void
    {
        $feeData = self::calculateServiceFee($payout->total_amount);

        $payout->update([
            'service_fee_percentage' => $feeData['service_fee_percentage'],
            'service_fee_amount' => $feeData['service_fee_amount'],
            'amount_after_fee' => $feeData['amount_after_fee'],
        ]);

        // Create a service fee transaction record
        ServiceFeeTransaction::create([
            'payout_request_id' => $payout->id,
            'shop_user_id' => $payout->shop_user_id,
            'total_amount' => $feeData['total_amount'],
            'service_fee_percentage' => $feeData['service_fee_percentage'],
            'service_fee_amount' => $feeData['service_fee_amount'],
            'amount_after_fee' => $feeData['amount_after_fee'],
            'status' => 'pending',
        ]);
    }

    /**
     * Get total service fees collected
     */
    public static function getTotalServiceFeesCollected(): float
    {
        return (float) ServiceFeeTransaction::where('status', 'collected')->sum('service_fee_amount');
    }

    /**
     * Get total service fees pending
     */
    public static function getTotalServiceFeesPending(): float
    {
        return (float) ServiceFeeTransaction::where('status', 'pending')->sum('service_fee_amount');
    }

    /**
     * Get service fee statistics
     */
    public static function getServiceFeeStats(): array
    {
        return [
            'total_collected' => self::getTotalServiceFeesCollected(),
            'total_pending' => self::getTotalServiceFeesPending(),
            'current_percentage' => self::getServiceFeePercentage(),
            'total_transactions' => ServiceFeeTransaction::count(),
            'collected_transactions' => ServiceFeeTransaction::where('status', 'collected')->count(),
            'pending_transactions' => ServiceFeeTransaction::where('status', 'pending')->count(),
        ];
    }

    /**
     * Mark service fee as collected when payout is marked as paid
     */
    public static function markServiceFeeAsCollected(ShopPayoutRequest $payout): void
    {
        $transaction = ServiceFeeTransaction::where('payout_request_id', $payout->id)->first();
        if ($transaction) {
            $transaction->update(['status' => 'collected']);
        }
    }
}
