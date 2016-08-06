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
        '\App\Console\Commands\ApiRefresh',
        '\App\Console\Commands\ApiPrices',
        '\App\Console\Commands\ApiBuild',
        '\App\Console\Commands\ApiWaypoints',
        '\App\Console\Commands\VersionCommand',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('api:prices')
                 ->everyTenMinutes();
        $schedule->command('api:build')
                 ->everyFiveMinutes();
    }
}
