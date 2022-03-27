<?php

namespace Tonystore\LaravelRoundRobin;

use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Tonystore\LaravelRoundRobin\Services\RoundRobin;

class LaravelRoundRobinProvider extends ServiceProvider
{

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/round-robin.php', 'round-robin');
        $this->app->singleton('RoundRobin', function () {
            return new RoundRobin([]);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerCollectionMacro();
        $this->registerPublishables();
    }
    public function registerCollectionMacro()
    {
        Collection::macro('toObject', function () {
            return json_decode(json_encode($this->values(), true));
        });
        Collection::macro('firstLeg', function () {
            return $this->filter(function ($rounds) {
                $rounds =  $rounds->where('phase', config('round-robin.one_phase'));
                return $rounds->isNotEmpty();
            })->values();
        });
        Collection::macro('secondLeg', function () {
            return $this->filter(function ($rounds) {
                $rounds =  $rounds->where('phase', config('round-robin.way_phase'));
                return $rounds->isNotEmpty();
            })->values();
        });
    }
    public function registerPublishables()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/round-robin.php' => config_path('round-robin.php'),
            ], 'round-robin');
        }
    }
}
