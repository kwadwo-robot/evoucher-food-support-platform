<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Broadcast;
use App\Models\User;
use App\Models\SystemLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BroadcastController extends Controller
{
    public function index()
    {
        $broadcasts = Broadcast::with('admin')
            ->latest()
            ->paginate(15);
        return view('admin.broadcasts.index', compact('broadcasts'));
    }

    public function create()
    {
        $roles = ['recipient', 'vcfse', 'school_care', 'local_shop'];
        return view('admin.broadcasts.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'recipient_type' => 'required|in:all,group,individual',
            'recipient_role' => 'required_if:recipient_type,group|in:recipient,vcfse,school_care,local_shop',
            'recipient_user_ids' => 'required_if:recipient_type,individual|array',
            'scheduled_at' => 'nullable|date_format:Y-m-d H:i',
        ]);

        $broadcast = Broadcast::create([
            'admin_user_id' => Auth::id(),
            'title' => $request->title,
            'message' => $request->message,
            'recipient_type' => $request->recipient_type,
            'recipient_role' => $request->recipient_role,
            'recipient_user_ids' => $request->recipient_type === 'individual' ? $request->recipient_user_ids : null,
            'status' => $request->scheduled_at ? 'scheduled' : 'draft',
            'scheduled_at' => $request->scheduled_at ? now()->parse($request->scheduled_at) : null,
        ]);

        // Calculate recipients count
        $count = $this->getRecipientsCount($broadcast);
        $broadcast->update(['recipients_count' => $count]);

        SystemLog::log('broadcast_created', 'broadcast', $broadcast->id, "Broadcast '{$broadcast->title}' created");

        return redirect()->route('admin.broadcasts.show', $broadcast)
            ->with('success', 'Broadcast created successfully.');
    }

    public function show(Broadcast $broadcast)
    {
        $broadcast->load('admin', 'reads.user');
        $readCount = $broadcast->reads()->count();
        return view('admin.broadcasts.show', compact('broadcast', 'readCount'));
    }

    public function send(Broadcast $broadcast)
    {
        if ($broadcast->status === 'sent') {
            return back()->with('error', 'This broadcast has already been sent.');
        }

        $recipients = $this->getRecipients($broadcast);
        
        // In a real application, you would queue this for background processing
        // For now, we'll just mark it as sent
        $broadcast->update([
            'status' => 'sent',
            'sent_at' => now(),
            'recipients_count' => count($recipients),
        ]);

        SystemLog::log('broadcast_sent', 'broadcast', $broadcast->id, "Broadcast '{$broadcast->title}' sent to {$broadcast->recipients_count} recipients");

        return back()->with('success', 'Broadcast sent successfully to ' . $broadcast->recipients_count . ' recipients.');
    }

    public function destroy(Broadcast $broadcast)
    {
        if ($broadcast->status === 'sent') {
            return back()->with('error', 'Cannot delete a sent broadcast.');
        }

        $broadcast->delete();
        SystemLog::log('broadcast_deleted', 'broadcast', $broadcast->id, "Broadcast deleted");

        return redirect()->route('admin.broadcasts.index')
            ->with('success', 'Broadcast deleted successfully.');
    }

    private function getRecipients(Broadcast $broadcast)
    {
        if ($broadcast->recipient_type === 'all') {
            return User::where('is_active', true)->get();
        } elseif ($broadcast->recipient_type === 'group') {
            return User::where('role', $broadcast->recipient_role)
                ->where('is_active', true)
                ->get();
        } else {
            return User::whereIn('id', $broadcast->recipient_user_ids)
                ->where('is_active', true)
                ->get();
        }
    }

    private function getRecipientsCount(Broadcast $broadcast)
    {
        // Use count() in the query instead of fetching all records
        if ($broadcast->recipient_type === 'all') {
            return User::where('is_active', true)->count();
        } elseif ($broadcast->recipient_type === 'group') {
            return User::where('role', $broadcast->recipient_role)
                ->where('is_active', true)
                ->count();
        } else {
            return User::whereIn('id', $broadcast->recipient_user_ids)
                ->where('is_active', true)
                ->count();
        }
    }
}
