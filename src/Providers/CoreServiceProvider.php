<?php

namespace LaraModule\Core\Providers;

use Illuminate\Support\ServiceProvider;

class CoreServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Merge package configs
        $this->mergeConfigFrom(__DIR__.'/../Configs/telescope.php', 'telescope');
        $this->mergeConfigFrom(__DIR__.'/../Configs/pulse.php', 'pulse');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Load migrations from the package
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');

        // Load views from the package
        $this->loadViewsFrom(__DIR__.'/../Views', 'laramodular-core');

        // Publish configs
        $this->publishes([
            __DIR__.'/../Configs/telescope.php' => config_path('telescope.php'),
        ], 'laramodular-core-config');

        $this->publishes([
            __DIR__.'/../Configs/pulse.php' => config_path('pulse.php'),
        ], 'laramodular-core-config');

        // Publish migrations
        $this->publishes([
            __DIR__.'/../Database/Migrations' => database_path('migrations'),
        ], 'laramodular-core-migrations');

        // Publish views
        $this->publishes([
            __DIR__.'/../Views' => resource_path('views/vendor/laramodular-core'),
        ], 'laramodular-core-views');

        // Publish public assets (Telescope)
        $this->publishes([
            __DIR__.'/../Public/telescope' => public_path('vendor/telescope'),
        ], 'telescope-assets');
    }
}
