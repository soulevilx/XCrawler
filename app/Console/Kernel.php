<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('jav:onejav-daily')->daily();
        $schedule->command('jav:onejav-all')->everyMinute();

        /**
         * Flickr
         */
        $schedule->command('flickr:contacts')->weekly();
        $schedule->command('flickr:queues-favorites')->everyFiveMinutes();
        $schedule->command('flickr:queues-owner')->everyFiveMinutes();
        $schedule->command('flickr:queues-photos')->everyFiveMinutes();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        $this->load(__DIR__.'/../Modules/Core/Console');
        $this->load(__DIR__.'/../Modules/Crawling/Console');
        $this->load(__DIR__.'/../Modules/Jav/Console');
        $this->load(__DIR__.'/../Modules/Flickr/Console');

        require base_path('routes/console.php');
    }
}
