<?php

namespace App\Console;

use App\Jobs\SendAppointmentReminderJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->job(new SendAppointmentReminderJob())->everyMinute()->withoutOverlapping();

        $schedule->command('appointments:generate-recurring')->dailyAt('03:00');

        $schedule->command('reminders:retry-failed')->everyTenMinutes();


    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
