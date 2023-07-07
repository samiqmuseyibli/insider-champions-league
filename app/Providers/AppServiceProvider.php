<?php

namespace App\Providers;

use App\Models\Fixture;
use App\Models\Team;
use App\Services\FixtureService;
use App\Services\LeagueService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(LeagueService::class, function ($app) {
            return new LeagueService($app->make(Team::class), $app->make(Fixture::class));
        });
        $this->app->bind(FixtureService::class, function ($app) {
            return new FixtureService($app->make(Fixture::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
