<?php

namespace EID\Console;

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
        \EID\Console\Commands\Inspire::class,
        \EID\Console\Commands\EidEngine::class,
        \EID\Console\Commands\ResultsCommand::class,
        \EID\Console\Commands\DashboardUpdater::class,
        \EID\Console\Commands\POCEngine::class,
        \EID\Console\Commands\EidColumnUpdater::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //Run the task every day at midnight ->daily()
        $schedule->command('eiddashboard:update')->daily();    
    }
}
