<?php

namespace LaraModule\Core\Providers;

use LaraModularity\Providers\ModuleServiceProvider;

class CoreServiceProvider extends ModuleServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        parent::register();

        // Add module-specific registration logic here if needed
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        parent::boot();

        // Add module-specific boot logic here if needed
    }
}
