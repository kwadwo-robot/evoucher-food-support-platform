<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Check for expired surplus allocations every 5 minutes
        $schedule->job(\App\Jobs\ReassignExpiredSurplusItems::class)->everyFiveMinutes();
        
        // Allocate new surplus items every hour
        $schedule->command('app:allocate-surplus-items')->hourly();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
