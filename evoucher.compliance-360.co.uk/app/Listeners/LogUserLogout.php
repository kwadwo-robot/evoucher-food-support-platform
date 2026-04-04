<?php

namespace App\Listeners;

use App\Models\SystemLog;
use Illuminate\Auth\Events\Logout;

class LogUserLogout
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Logout $event): void
    {
        // Only log logouts for non-admin users (school/care, VCFSE, recipients)
        if (in_array($event->user->role, ['school_care', 'vcfse', 'recipient'])) {
            SystemLog::create([
                'user_id' => $event->user->id,
                'action' => 'logout',
                'entity_type' => 'User',
                'entity_id' => $event->user->id,
                'description' => $event->user->name . ' (' . $event->user->email . ') logged out',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }
    }
}
