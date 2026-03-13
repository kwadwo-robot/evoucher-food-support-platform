<?php

namespace App\Listeners;

use App\Models\SystemLog;
use Illuminate\Auth\Events\Login;

class LogUserLogin
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
    public function handle(Login $event): void
    {
        // Only log logins for non-admin users (school/care, VCFSE, recipients)
        if (in_array($event->user->role, ['school_care', 'vcfse', 'recipient'])) {
            SystemLog::create([
                'user_id' => $event->user->id,
                'action' => 'login',
                'entity_type' => 'User',
                'entity_id' => $event->user->id,
                'description' => $event->user->name . ' (' . $event->user->email . ') logged in',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }
    }
}
