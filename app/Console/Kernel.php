<?php

namespace App\Console;

use Artisan;
use DB;
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
        'App\Console\Commands\Inspire',
        'App\Console\Commands\courseCreationTest',
        'App\Console\Commands\accountCreationTest',
    ];

    /**
     * Define the application's command schedule.
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        $schedule->call(function () {

            // $jobCount = DB::statement("SELECT COUNT(*) AS `totalJobs` FROM `jobs`");
            // $jobCount = DB::select("SELECT * FROM `jobs`");
            $jobCount = DB::select('SELECT COUNT(*) AS `totalJobs` FROM `jobs`')[0]->totalJobs;
            for ($i = 0; $i < $jobCount; $i++) {
                Artisan::call('queue:work');
            }

        })->cron('* * * * * *');

        // $schedule->command('queue:work')->cron("* * * * * *");
    }
}
