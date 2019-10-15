<?php

namespace App\Providers;

use App\Deposit;
use App\EditorialReview;
use App\Observers\DepositObserver;
use App\Observers\EditorialReviewObserver;
use App\Observers\ShareholderObserver;
use App\Shareholder;
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
        Deposit::observe(DepositObserver::class);
        EditorialReview::observe(EditorialReviewObserver::class);
    }
}
