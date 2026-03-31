<?php

namespace App\Http\Controllers\Recipient;

use App\Http\Controllers\Controller;
use App\Models\Broadcast;
use Illuminate\Support\Facades\Auth;

class RecipientBroadcastController extends Controller
{
    /**
     * Display a listing of broadcasts for the recipient.
     */
    public function index()
    {
        $broadcasts = Broadcast::whereJsonContains('recipient_user_ids', Auth::id())
            ->where('status', 'sent')
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('recipient.broadcasts.index', compact('broadcasts'));
    }

    /**
     * Display a specific broadcast message.
     */
    public function show(Broadcast $broadcast)
    {
        // Check if the current user is a recipient of this broadcast
        $recipientIds = $broadcast->recipient_user_ids ?? [];
        
        if (!in_array(Auth::id(), $recipientIds)) {
            abort(403, 'Unauthorized access to this broadcast.');
        }

        // Mark as read
        $broadcast->reads()->firstOrCreate([
            'user_id' => Auth::id(),
        ], [
            'read_at' => now(),
        ]);

        return view('recipient.broadcasts.show', compact('broadcast'));
    }
}
