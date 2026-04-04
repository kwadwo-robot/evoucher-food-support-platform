<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Redemption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->latest()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function getUnread()
    {
        $user = Auth::user();
        $notifications = Notification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->latest()
            ->take(10)
            ->get();

        $notificationList = $notifications->map(function ($notif) {
            return [
                'id' => $notif->id,
                'title' => $notif->title,
                'message' => $notif->message,
                'type' => $notif->type,
                'read_at' => $notif->read_at,
                'created_at' => $notif->created_at,
            ];
        });

        // For shop users, include pending redemptions as notifications
        if (strpos($user->role, 'shop') !== false || $user->role === 'local_shop') {
            $pendingRedemptions = Redemption::where('shop_user_id', $user->id)
                ->where('status', 'pending')
                ->with(['foodListing', 'recipient.recipientProfile'])
                ->latest()
                ->take(10)
                ->get();

            $redemptionNotifications = $pendingRedemptions->map(function ($redemption) {
                return [
                    'id' => 'redemption-' . $redemption->id,
                    'title' => 'Pending Redemption',
                    'message' => $redemption->recipient->name . ' is waiting to collect ' . $redemption->foodListing->item_name,
                    'type' => 'redemption',
                    'read_at' => null,
                    'created_at' => $redemption->created_at,
                ];
            });

            $allNotifications = array_merge($notificationList->toArray(), $redemptionNotifications->toArray());
            $notificationList = collect($allNotifications)
                ->sortByDesc('created_at')
                ->values()
                ->take(10);
        }

        return response()->json([
            'count' => $notificationList->count(),
            'notifications' => $notificationList->values(),
        ]);
    }

    public function markAsRead(Notification $notification)
    {
        abort_if($notification->user_id !== Auth::id(), 403);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    public function delete(Notification $notification)
    {
        abort_if($notification->user_id !== Auth::id(), 403);
        $notification->delete();

        return response()->json(['success' => true]);
    }

    public function deleteAll()
    {
        Notification::where('user_id', Auth::id())->delete();

        return response()->json(['success' => true]);
    }
}
