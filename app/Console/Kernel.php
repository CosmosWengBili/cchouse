<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Carbon\Carbon;
use App\LandlordContract;
use App\Notifications\ContractDueInTwoMonths;

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
        $schedule->call(function () {
                    LandlordContract::where('commission_end_date', Carbon::today()->addMonth(2))
                                    ->with('commissioner')
                                    ->get()
                                    ->each(function ($landlordContract) {
                                        $landlordContract->commissioner->notify(new ContractDueInTwoMonths($landlordContract));
                                    });
                })->before(function () {
                    // Task is about to start...
                })
                ->after(function () {
                    // Task is complete...
                })
                ->daily()
                ->runInBackground();
                // ->emailOutputTo('foo@example.com');
                // ->emailOutputOnFailure('foo@example.com');


        $schedule->call(function () {
                    LandlordContract::with('building.rooms')
                                    ->where('rent_adjusted_date', Carbon::today())
                                    ->get()
                                    ->each(function ($landlordContract) {
                                        $landlordContract->building->rooms->each(function ($room) use ($landlordContract) {
                                            $room->rent_list_price = intval(round($room->rent_list_price * (100 - $landlordContract->adjust_ratio ) / 100));
                                            $room->rent_landlord = intval(round($room->rent_landlord * (100 - $landlordContract->adjust_ratio ) / 100));
                                            $room->save();
                                        });
                                    });
                })->before(function () {
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
