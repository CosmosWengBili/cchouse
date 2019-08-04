<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Carbon\Carbon;
use App\LandlordContract;
use App\Notifications\ContractDueInTwoMonths;

use App\Services\ScheduleService;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();


        // $schedule->call(new DeleteRecentUsers)->daily();
        $schedule->call(ScheduleService::make('notifyContractDueInTwoMonths'))
                ->name('Notify contract due in two months')
                ->before(function () {
                    // Task is about to start...
                })
                ->after(function () {
                    // Task is complete...
                })
                ->daily()
                ->runInBackground();
                // ->emailOutputTo('foo@example.com');
                // ->emailOutputOnFailure('foo@example.com');


        $schedule->call(ScheduleService::make('adjustRent'))
                ->name('Adjust rent')
                ->before(function () {
                    // Task is about to start...
                })
                ->after(function () {
                    // Task is complete...
                })
                ->daily()
                ->runInBackground();
                // ->emailOutputTo('foo@example.com');
                // ->emailOutputOnFailure('foo@example.com');
                
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
