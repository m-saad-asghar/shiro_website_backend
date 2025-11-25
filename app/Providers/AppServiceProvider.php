<?php

namespace App\Providers;

use App\Models\Property;
use App\Models\SaleAgent;
use App\Observers\PropertyObserver;
use App\Observers\SaleAgentObserver;
use Illuminate\Support\ServiceProvider;
use App\Services\Currency\CurrencyService;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        SaleAgent::observe(SaleAgentObserver::class);
        Property::observe(PropertyObserver::class);
    }
}
