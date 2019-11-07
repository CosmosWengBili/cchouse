<?php

namespace App\Providers;

use App\Deposit;
use App\EditorialReview;
use App\Observers\EditorialReviewObserver;
use App\Observers\LandlordObserver;
use App\Observers\LandlordContractObserver;
use App\Landlord;
use App\LandlordContract;
use Illuminate\Support\ServiceProvider;
use DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (config('app.debug')) {
            DB::enableQueryLog();
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Landlord::observe(LandlordObserver::class);
        LandlordContract::observe(LandlordContractObserver::class);
        EditorialReview::observe(EditorialReviewObserver::class);
    }
}
