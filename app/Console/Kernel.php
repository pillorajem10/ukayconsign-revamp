<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('tally:save')->dailyAt('00:00');
    }    

    /**
     * The commands provided by your application.
     */
    protected $commands = [
        \App\Console\Commands\SaveTally::class,
        \App\Console\Commands\CreateBillings::class,
        \App\Console\Commands\CreateBillings2::class,
    ];

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
