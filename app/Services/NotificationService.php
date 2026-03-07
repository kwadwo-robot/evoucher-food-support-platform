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
