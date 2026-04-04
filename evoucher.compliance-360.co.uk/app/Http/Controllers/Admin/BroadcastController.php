<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\BroadcastMail;
use App\Models\Broadcast;
use App\Models\BroadcastDelivery;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class BroadcastController extends Controller
{
    public function index()
    {
        $broadcasts = Broadcast::with('admin')->orderByDesc('created_at')->paginate(20);
        return view('admin.broadcasts.index', compact('broadcasts'));
    }

    public function create()
    {
        $users = User::where('role', 'recipient')
            ->orWhere('role', 'vcfse')
            ->orWhere('role', 'school')
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();
        
        return view('admin.broadcasts.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'recipients' => 'required|array|min:1',
            'recipients.*' => 'integer',
        ]);

        $recipientIds = $validated['recipients'];
        $users = User::whereIn('id', $recipientIds)->get();

        $broadcast = Broadcast::create([
            'title' => $validated['title'],
            'message' => $validated['message'],
            'recipient_type' => 'individual',
            'recipient_user_ids' => $recipientIds,
            'recipients_count' => count($users),
            'status' => 'sent',
            'admin_user_id' => auth()->id(),
            'sent_at' => now(),
        ]);

        // Send emails to recipients
        foreach ($users as $user) {
            try {
                Mail::to($user->email)->send(new BroadcastMail($broadcast, $user));
                
                // Log delivery
                BroadcastDelivery::create([
                    'broadcast_id' => $broadcast->id,
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'status' => 'sent',
                    'sent_at' => now(),
                ]);
            } catch (\Exception $e) {
                BroadcastDelivery::create([
                    'broadcast_id' => $broadcast->id,
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);
                \Log::error('Failed to send broadcast email to ' . $user->email . ': ' . $e->getMessage());
            }
        }

        return redirect()->route('admin.broadcasts.show', $broadcast)
            ->with('success', 'Broadcast created successfully and sent to ' . count($users) . ' recipients.');
    }

    public function show(Broadcast $broadcast)
    {
        $deliveries = BroadcastDelivery::where('broadcast_id', $broadcast->id)->get();
        $stats = [
            'total' => $deliveries->count(),
            'sent' => $deliveries->where('status', 'sent')->count(),
            'failed' => $deliveries->where('status', 'failed')->count(),
            'pending' => $deliveries->where('status', 'pending')->count(),
            'read' => 0,
        ];
        
        return view('admin.broadcasts.show', compact('broadcast', 'deliveries', 'stats'));
    }

    public function destroy(Broadcast $broadcast)
    {
        $broadcast->delete();
        return redirect()->route('admin.broadcasts.index')
            ->with('success', 'Broadcast deleted successfully.');
    }

    public function getAllUsers()
    {
        $users = User::where('role', 'recipient')
            ->orWhere('role', 'vcfse')
            ->orWhere('role', 'school')
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();

        return response()->json($users);
    }
}
