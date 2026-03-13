<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemLog;
use Illuminate\Http\Request;

class SystemLogController extends Controller
{
    public function index(Request $request)
    {
        $query = SystemLog::with('user');

        // Filter by user role (only non-admin users)
        if ($request->user_role) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('role', $request->user_role);
            });
        } else {
            // By default, only show logs for non-admin users
            $query->whereHas('user', function($q) {
                $q->whereIn('role', ['recipient', 'vcfse', 'school_care']);
            });
        }

        if ($request->action) {
            $query->where('action', $request->action);
        }

        if ($request->entity_type) {
            $query->where('entity_type', $request->entity_type);
        }

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->latest()->paginate(50);
        
        $actions = SystemLog::distinct('action')->pluck('action');
        $entityTypes = SystemLog::distinct('entity_type')->pluck('entity_type');

        return view('admin.system-logs.index', compact('logs', 'actions', 'entityTypes'));
    }

    public function show(SystemLog $log)
    {
        $log->load('user');
        return view('admin.system-logs.show', compact('log'));
    }

    public function export(Request $request)
    {
        $query = SystemLog::with('user');

        if ($request->action) {
            $query->where('action', $request->action);
        }

        if ($request->entity_type) {
            $query->where('entity_type', $request->entity_type);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->latest()->get();

        // Export to CSV
        $filename = 'system-logs-' . now()->format('Y-m-d-H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=$filename",
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'User', 'Action', 'Entity Type', 'Entity ID', 'Description', 'IP Address', 'Date']);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->user?->name ?? 'System',
                    $log->action,
                    $log->entity_type,
                    $log->entity_id,
                    $log->description,
                    $log->ip_address,
                    $log->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
