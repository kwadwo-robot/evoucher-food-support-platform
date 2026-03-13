<?php

namespace App\Traits;

use App\Models\SystemLog;

trait LogsUserActivity
{
    /**
     * Log an activity for non-admin users (school/care, VCFSE, recipients)
     */
    protected function logActivity($action, $entityType, $description, $entityId = null, $changes = null)
    {
        $user = auth()->user();
        
        // Only log for non-admin users
        if ($user && in_array($user->role, ['school_care', 'vcfse', 'recipient'])) {
            SystemLog::create([
                'user_id' => $user->id,
                'action' => $action,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'description' => $description,
                'changes' => $changes,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }
    }
}
