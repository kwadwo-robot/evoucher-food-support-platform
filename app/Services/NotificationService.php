<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Notify admins about a new shop registration
     */
    public static function notifyNewShopRegistration(User $shop)
    {
        $admins = User::where('role', 'admin')->orWhere('role', 'super_admin')->get();
        
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'title' => 'New Shop Registered',
                'message' => $shop->name . ' has registered as a local shop and is ready to list food items.',
                'type' => 'new_shop',
                'icon' => 'fas fa-store',
                'link' => route('admin.users'),
                'read_at' => null,
            ]);
        }
        
        Log::info("New shop registration notification sent for: {$shop->name}");
    }

    /**
     * Notify admins about a new donation
     */
    public static function notifyNewDonation($amount, $donorEmail = null)
    {
        $admins = User::where('role', 'admin')->orWhere('role', 'super_admin')->get();
        
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'title' => 'New Donation Received',
                'message' => '£' . number_format($amount, 2) . ' donation received' . ($donorEmail ? ' from ' . $donorEmail : '') . '. Check the Payments page for details.',
                'type' => 'new_donation',
                'icon' => 'fas fa-heart',
                'link' => route('admin.payments'),
                'read_at' => null,
            ]);
        }
        
        Log::info("New donation notification sent for: £{$amount}");
    }

    /**
     * Notify VCFSE/School about surplus food available
     */
    public static function notifySurplusFoodAlert($listing)
    {
        // Get all VCFSE and School/Care users
        $organisations = User::whereIn('role', ['vcfse', 'school_care'])->get();
        
        foreach ($organisations as $org) {
            Notification::create([
                'user_id' => $org->id,
                'title' => 'Surplus Food Available',
                'message' => $listing->item_name . ' from ' . $listing->shop->name . ' is available as surplus food. Available for 2 hours only!',
                'type' => 'surplus_food_alert',
                'icon' => 'fas fa-boxes-stacked',
                'link' => '#',
                'read_at' => null,
            ]);
        }
        
        Log::info("Surplus food alert sent for: {$listing->item_name}");
    }

    /**
     * Notify shop about a voucher redemption
     */
    public static function notifyShopVoucherRedemption($redemption)
    {
        $shop = $redemption->shop;
        
        Notification::create([
            'user_id' => $shop->id,
            'title' => 'Voucher Redeemed',
            'message' => 'A voucher has been redeemed at your shop for ' . $redemption->foodListing->item_name . '. Amount: £' . number_format($redemption->amount_used, 2),
            'type' => 'voucher_redeemed',
            'icon' => 'fas fa-check-circle',
            'link' => route('shop.redemptions'),
            'read_at' => null,
        ]);
        
        Log::info("Voucher redemption notification sent to shop: {$shop->name}");
    }

    /**
     * Notify recipient about a new voucher
     */
    public static function notifyRecipientNewVoucher($voucher)
    {
        $recipient = $voucher->recipient;
        
        Notification::create([
            'user_id' => $recipient->id,
            'title' => 'New Voucher Issued',
            'message' => 'You have received a new voucher worth £' . number_format($voucher->remaining_value, 2) . '. Expires on ' . $voucher->expiry_date->format('d M Y'),
            'type' => 'new_voucher',
            'icon' => 'fas fa-ticket',
            'link' => route('recipient.food.browse'),
            'read_at' => null,
        ]);
        
        Log::info("New voucher notification sent to recipient: {$recipient->name}");
    }

    /**
     * Notify shop about a payout being processed
     */
    public static function notifyShopPayoutProcessed($payout)
    {
        $shop = $payout->shop;
        
        Notification::create([
            'user_id' => $shop->id,
            'title' => 'Payout Processed',
            'message' => 'Your payout request of £' . number_format($payout->total_amount, 2) . ' has been processed. Reference: ' . $payout->payment_reference,
            'type' => 'payout_processed',
            'icon' => 'fas fa-money-bill-wave',
            'link' => route('shop.payouts'),
            'read_at' => null,
        ]);
        
        Log::info("Payout processed notification sent to shop: {$shop->name}");
    }

    /**
     * Notify shop about a payout being approved
     */
    public static function notifyShopPayoutApproved($payout)
    {
        $shop = $payout->shop;
        
        Notification::create([
            'user_id' => $shop->id,
            'title' => 'Payout Request Approved',
            'message' => 'Your payout request of £' . number_format($payout->total_amount, 2) . ' has been approved and is being processed.',
            'type' => 'payout_approved',
            'icon' => 'fas fa-check-circle',
            'link' => route('shop.payouts'),
            'read_at' => null,
        ]);
        
        Log::info("Payout approved notification sent to shop: {$shop->name}");
    }

    /**
     * Notify shop about a payout being rejected
     */
    public static function notifyShopPayoutRejected($payout)
    {
        $shop = $payout->shop;
        
        Notification::create([
            'user_id' => $shop->id,
            'title' => 'Payout Request Rejected',
            'message' => 'Your payout request of £' . number_format($payout->total_amount, 2) . ' has been rejected. Reason: ' . ($payout->admin_notes ?? 'No reason provided'),
            'type' => 'payout_rejected',
            'icon' => 'fas fa-times-circle',
            'link' => route('shop.payouts'),
            'read_at' => null,
        ]);
        
        Log::info("Payout rejected notification sent to shop: {$shop->name}");
    }

    /**
     * Get unread notification count for a user
     */
    public static function getUnreadCount($userId)
    {
        return Notification::where('user_id', $userId)
            ->whereNull('read_at')
            ->count();
    }

    /**
     * Get unread notifications for a user
     */
    public static function getUnreadNotifications($userId, $limit = 10)
    {
        return Notification::where('user_id', $userId)
            ->whereNull('read_at')
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Mark notification as read
     */
    public static function markAsRead($notificationId)
    {
        $notification = Notification::find($notificationId);
        if ($notification) {
            $notification->update(['read_at' => now()]);
        }
    }

    /**
     * Mark all notifications as read for a user
     */
    public static function markAllAsRead($userId)
    {
        Notification::where('user_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    /**
     * Notify recipient about successful redemption
     */
    public static function notifyRecipientRedemptionConfirmed($redemption)
    {
        $recipient = $redemption->recipient;
        $amountOwed = $redemption->amount_owed_at_shop > 0 ? ' You owe £' . number_format($redemption->amount_owed_at_shop, 2) . ' at the shop.' : '';
        
        Notification::create([
            'user_id' => $recipient->id,
            'title' => 'Redemption Confirmed',
            'message' => 'Your redemption for ' . $redemption->foodListing->item_name . ' has been confirmed. Please collect from ' . $redemption->shop->name . '.' . $amountOwed,
            'type' => 'redemption_confirmed',
            'icon' => 'fas fa-check-circle',
            'link' => route('recipient.history'),
            'read_at' => null,
        ]);
        
        Log::info("Redemption confirmation notification sent to recipient: {$recipient->name}");
    }

    /**
     * Notify recipient about voucher expiring soon
     */
    public static function notifyRecipientVoucherExpiring($voucher)
    {
        $recipient = $voucher->recipient;
        
        Notification::create([
            'user_id' => $recipient->id,
            'title' => 'Voucher Expiring Soon',
            'message' => 'Your voucher worth £' . number_format($voucher->remaining_value, 2) . ' (Code: ' . $voucher->code . ') will expire on ' . $voucher->expiry_date->format('d M Y') . '. Use it now!',
            'type' => 'voucher_expiring',
            'icon' => 'fas fa-clock',
            'link' => route('recipient.food.browse'),
            'read_at' => null,
        ]);
        
        Log::info("Voucher expiring notification sent to recipient: {$recipient->name}");
    }

    /**
     * Notify recipient about voucher expired
     */
    public static function notifyRecipientVoucherExpired($voucher)
    {
        $recipient = $voucher->recipient;
        
        Notification::create([
            'user_id' => $recipient->id,
            'title' => 'Voucher Expired',
            'message' => 'Your voucher (Code: ' . $voucher->code . ') has expired. Contact your support worker for a new voucher.',
            'type' => 'voucher_expired',
            'icon' => 'fas fa-exclamation-circle',
            'link' => route('recipient.vouchers'),
            'read_at' => null,
        ]);
        
        Log::info("Voucher expired notification sent to recipient: {$recipient->name}");
    }

    /**
     * Notify recipient about food item unavailable
     */
    public static function notifyRecipientFoodUnavailable($foodListing, $recipient)
    {
        Notification::create([
            'user_id' => $recipient->id,
            'title' => 'Food Item Unavailable',
            'message' => $foodListing->item_name . ' from ' . $foodListing->shop->name . ' is no longer available. Please browse other items.',
            'type' => 'food_unavailable',
            'icon' => 'fas fa-ban',
            'link' => route('recipient.food.browse'),
            'read_at' => null,
        ]);
        
        Log::info("Food unavailable notification sent to recipient: {$recipient->name}");
    }

    /**
     * Notify shop about food listing approved
     */
    public static function notifyShopListingApproved($listing)
    {
        $shop = $listing->shop;
        
        Notification::create([
            'user_id' => $shop->id,
            'title' => 'Food Listing Approved',
            'message' => 'Your food listing for ' . $listing->item_name . ' has been approved and is now visible to recipients.',
            'type' => 'listing_approved',
            'icon' => 'fas fa-check-circle',
            'link' => route('shop.listings'),
            'read_at' => null,
        ]);
        
        Log::info("Listing approved notification sent to shop: {$shop->name}");
    }

    /**
     * Notify shop about food listing rejected
     */
    public static function notifyShopListingRejected($listing, $reason = null)
    {
        $shop = $listing->shop;
        
        Notification::create([
            'user_id' => $shop->id,
            'title' => 'Food Listing Rejected',
            'message' => 'Your food listing for ' . $listing->item_name . ' has been rejected.' . ($reason ? ' Reason: ' . $reason : ''),
            'type' => 'listing_rejected',
            'icon' => 'fas fa-times-circle',
            'link' => route('shop.listings'),
            'read_at' => null,
        ]);
        
        Log::info("Listing rejected notification sent to shop: {$shop->name}");
    }

    /**
     * Notify shop about pending redemption reminder
     */
    public static function notifyShopPendingRedemptionReminder($shop, $pendingCount)
    {
        Notification::create([
            'user_id' => $shop->id,
            'title' => 'Pending Redemptions',
            'message' => 'You have ' . $pendingCount . ' pending redemption(s) waiting to be completed. Please check your dashboard.',
            'type' => 'pending_redemption_reminder',
            'icon' => 'fas fa-hourglass-half',
            'link' => route('shop.redemptions'),
            'read_at' => null,
        ]);
        
        Log::info("Pending redemption reminder sent to shop: {$shop->name}");
    }

    /**
     * Notify shop about redemption completed
     */
    public static function notifyShopRedemptionCompleted($redemption)
    {
        $shop = $redemption->shop;
        
        Notification::create([
            'user_id' => $shop->id,
            'title' => 'Redemption Completed',
            'message' => 'Redemption for ' . $redemption->foodListing->item_name . ' by ' . $redemption->recipient->name . ' has been completed.',
            'type' => 'redemption_completed',
            'icon' => 'fas fa-check-circle',
            'link' => route('shop.redemptions'),
            'read_at' => null,
        ]);
        
        Log::info("Redemption completed notification sent to shop: {$shop->name}");
    }

    /**
     * Notify VCFSE/School about wallet balance low
     */
    public static function notifyOrganisationLowBalance($organisation, $balance)
    {
        Notification::create([
            'user_id' => $organisation->id,
            'title' => 'Low Wallet Balance',
            'message' => 'Your wallet balance is running low at £' . number_format($balance, 2) . '. Please top up to continue issuing vouchers.',
            'type' => 'low_balance',
            'icon' => 'fas fa-exclamation-triangle',
            'link' => route('organisation.fund-load'),
            'read_at' => null,
        ]);
        
        Log::info("Low balance notification sent to organisation: {$organisation->name}");
    }

    /**
     * Notify VCFSE/School about wallet topped up
     */
    public static function notifyOrganisationWalletToppedUp($organisation, $amount, $newBalance)
    {
        Notification::create([
            'user_id' => $organisation->id,
            'title' => 'Wallet Topped Up',
            'message' => 'Your wallet has been topped up with £' . number_format($amount, 2) . '. New balance: £' . number_format($newBalance, 2),
            'type' => 'wallet_topped_up',
            'icon' => 'fas fa-plus-circle',
            'link' => route('organisation.dashboard'),
            'read_at' => null,
        ]);
        
        Log::info("Wallet topped up notification sent to organisation: {$organisation->name}");
    }

    /**
     * Notify VCFSE/School about fund load approved
     */
    public static function notifyOrganisationFundLoadApproved($organisation, $amount)
    {
        Notification::create([
            'user_id' => $organisation->id,
            'title' => 'Fund Load Approved',
            'message' => 'Your fund load request of £' . number_format($amount, 2) . ' has been approved and added to your wallet.',
            'type' => 'fund_load_approved',
            'icon' => 'fas fa-check-circle',
            'link' => route('organisation.dashboard'),
            'read_at' => null,
        ]);
        
        Log::info("Fund load approved notification sent to organisation: {$organisation->name}");
    }

    /**
     * Notify VCFSE/School about fund load rejected
     */
    public static function notifyOrganisationFundLoadRejected($organisation, $amount, $reason = null)
    {
        Notification::create([
            'user_id' => $organisation->id,
            'title' => 'Fund Load Rejected',
            'message' => 'Your fund load request of £' . number_format($amount, 2) . ' has been rejected.' . ($reason ? ' Reason: ' . $reason : ''),
            'type' => 'fund_load_rejected',
            'icon' => 'fas fa-times-circle',
            'link' => route('organisation.dashboard'),
            'read_at' => null,
        ]);
        
        Log::info("Fund load rejected notification sent to organisation: {$organisation->name}");
    }

    /**
     * Notify admin about user account deactivated
     */
    public static function notifyAdminUserDeactivated($user)
    {
        $admins = User::where('role', 'admin')->orWhere('role', 'super_admin')->get();
        
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'title' => 'User Account Deactivated',
                'message' => $user->name . ' (' . $user->role . ') account has been deactivated.',
                'type' => 'user_deactivated',
                'icon' => 'fas fa-user-slash',
                'link' => route('admin.users'),
                'read_at' => null,
            ]);
        }
        
        Log::info("User deactivated notification sent to admins for: {$user->name}");
    }

    /**
     * Notify user about account deactivation
     */
    public static function notifyUserAccountDeactivated($user)
    {
        Notification::create([
            'user_id' => $user->id,
            'title' => 'Account Deactivated',
            'message' => 'Your account has been deactivated. Please contact support for more information.',
            'type' => 'account_deactivated',
            'icon' => 'fas fa-exclamation-circle',
            'link' => '#',
            'read_at' => null,
        ]);
        
        Log::info("Account deactivated notification sent to user: {$user->name}");
    }

    /**
     * Notify user about account reactivation
     */
    public static function notifyUserAccountReactivated($user)
    {
        Notification::create([
            'user_id' => $user->id,
            'title' => 'Account Reactivated',
            'message' => 'Your account has been reactivated. Welcome back!',
            'type' => 'account_reactivated',
            'icon' => 'fas fa-check-circle',
            'link' => route('dashboard'),
            'read_at' => null,
        ]);
        
        Log::info("Account reactivated notification sent to user: {$user->name}");
    }

    /**
     * Notify admin about fraudulent activity detected
     */
    public static function notifyAdminFraudulentActivity($user, $activity)
    {
        $admins = User::where('role', 'admin')->orWhere('role', 'super_admin')->get();
        
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'title' => 'Suspicious Activity Detected',
                'message' => 'Suspicious activity detected for user ' . $user->name . ': ' . $activity,
                'type' => 'fraudulent_activity',
                'icon' => 'fas fa-exclamation-triangle',
                'link' => route('admin.users'),
                'read_at' => null,
            ]);
        }
        
        Log::warning("Fraudulent activity notification sent for user: {$user->name}");
    }

    /**
     * Notify admin about system error
     */
    public static function notifyAdminSystemError($errorMessage)
    {
        $admins = User::where('role', 'admin')->orWhere('role', 'super_admin')->get();
        
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'title' => 'System Error Occurred',
                'message' => 'A system error has occurred: ' . substr($errorMessage, 0, 100),
                'type' => 'system_error',
                'icon' => 'fas fa-exclamation-circle',
                'link' => '#',
                'read_at' => null,
            ]);
        }
        
        Log::error("System error notification sent: {$errorMessage}");
    }
}
