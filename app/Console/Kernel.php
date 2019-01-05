<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\GitLabTasks::class,
        Commands\ServerStates::class,
        Commands\ServerTasks::class,
        Commands\ServerUsers::class,
        Commands\ServerSSLCertificates::class,
        Commands\ReadStudents::class,
        Commands\Tools::class,
        Commands\LocalTask::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
       // $schedule->command('webdb:states')->everyFiveMinutes()->withoutOverlapping();
//        $schedule->command('webdb:tasks')->everyMinute()->withoutOverlapping();
    }
}
