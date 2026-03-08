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
            'link' => route('recipient.browse'),
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
}
