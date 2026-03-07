<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function unread()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['count' => 0, 'notifications' => []]);
        }

        $notifications = Notification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->latest()
            ->take(10)
            ->get();

        return response()->json([
            'count' => $notifications->count(),
            'notifications' => $notifications->map(function ($notif) {
                return [
                    'id' => $notif->id,
                    'title' => $notif->title,
                    'message' => $notif->message,
                    'type' => $notif->type,
                    'read_at' => $notif->read_at,
                    'created_at' => $notif->created_at,
                ];
            }),
        ]);
    }

    public function markAsRead($id)
    {
        $notification = Notification::find($id);
        if ($notification && $notification->user_id === Auth::id()) {
            $notification->update(['read_at' => now()]);
        }
        return response()->json(['success' => true]);
    }
}
