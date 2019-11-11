<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Carbon\Carbon;
use App\LandlordContract;
use App\Notifications\LandlordContractDue;

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
        $schedule->call(ScheduleService::make('notifyContractDue'))
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
                ->daily()
                ->runInBackground();

        $schedule->call(ScheduleService::make('notifyBirth'))
                ->name('Landlord birth notify')
                ->daily()
                ->runInBackground();

        $schedule->call(ScheduleService::make('notifyTenantContractDueInTwoMonths'))
                ->name('Notify tenant contract due in two months')
                ->daily()
                ->runInBackground();

        $schedule->call(ScheduleService::make('notifyMaintenanceStatus'))
                ->name('Notify if maintenance status not changed for a long time')
                ->daily()
                ->runInBackground();

        $schedule->call(ScheduleService::make('genarateDebtCollections'))
                ->name('Genarate debt collections')
                ->dailyAt('07:00')
                ->runInBackground();

        $schedule->call(ScheduleService::make('notifyReversalErrorCases'))
                ->name('Notify User for unclosed ReversalErrorCases every day')
                ->before(function () {
                    // Task is about to start...
                })
                ->after(function () {
                    // Task is complete...
                })
                ->dailyAt('00:30')
                ->runInBackground();

        $schedule->call(ScheduleService::make('setMonthlyReportCarryFoward'))
                ->name('Set Monthly Report CarryFoward')
                ->dailyAt('05:00')
                ->runInBackground();

        $schedule->call(ScheduleService::make('storeMonthlyReportFromLandlordContracts', Carbon::now()->subMonth()))
                ->name('Create Monthly Report')
                ->monthlyOn(10, '05:30')
                ->runInBackground();

        $schedule->call(ScheduleService::make('genarateDepositInterest'))
                ->name('Generate Deposit interest every month')
                ->monthlyOn(Carbon::now()->endOfMonth()->day, '05:30')
                ->runInBackground();
        $schedule->call(ScheduleService::make('genarateCharterManagementFee'))
                ->name('Generate Mangement Fee for charter building every month')
                ->monthlyOn(Carbon::now()->endOfMonth()->day, '05:45')
                ->runInBackground();
        $schedule->call(ScheduleService::make('notifyKeyRequestBorrowAllowed'))
                ->name('Notify User Key Request expired')
                ->dailyAt('06:00')
                ->runInBackground();
        $schedule->call(ScheduleService::make('updateDepositPaid'))
            ->name("Update deposit_paid on active contract")
            ->dailyAt('03:00')
            ->runInBackground();
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
