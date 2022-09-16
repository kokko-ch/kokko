<?php

namespace App\Console;

use App\Models\NotificationJob;
use App\Notifications\RecurringNotification;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        foreach (
            NotificationJob::where('is_active', true)->get() as $notificationJob
        ) {
            $schedule->call(function () use ($notificationJob) {
                $notificationJob
                    ->user
                    ->notify(new RecurringNotification($notificationJob));
            })->cron(
                "{$notificationJob->minute} {$notificationJob->hour} {$notificationJob->day} {$notificationJob->month} {$notificationJob->weekday}"
            )->timezone($notificationJob->timezone);
        }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
