<?php

namespace App\Providers;

use App\EditorialReview;
use App\Observers\EditorialReviewObserver;
use App\Observers\ShareholderObserver;
use App\Observers\LandlordObserver;
use App\Observers\LandlordContractObserver;
use App\Shareholder;
use App\Landlord;
use App\LandlordContract;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Shareholder::observe(ShareholderObserver::class);
        Landlord::observe(LandlordObserver::class);
        LandlordContract::observe(LandlordContractObserver::class);
        EditorialReview::observe(EditorialReviewObserver::class);
    }
}
