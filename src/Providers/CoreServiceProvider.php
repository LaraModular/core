<?php

namespace LaraModule\Core\Providers;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\ServiceProvider;
use LaraModule\Core\Middleware\ApiLocale;
use LaraModule\Core\Middleware\AddSecurityHeaders;

class CoreServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.debug')) {
            // Macro for query debugging purposes.
            QueryBuilder::macro('toSqlWithBindings', function (): string {
                /** @var \Illuminate\Database\Query\Builder $this */
                $sql = $this->toSql();
                $bindings = $this->getBindings();

                return vsprintf(str_replace('?', "'%s'", $sql), array_map('addslashes', $bindings));
            });

            EloquentBuilder::macro('toSqlWithBindings', function (): string {
                /** @var \Illuminate\Database\Eloquent\Builder $this */
                return $this->getQuery()->toSqlWithBindings();
            });
        }

        $router = $this->app['router'];
        $router->pushMiddlewareToGroup('api', ApiLocale::class);
        $router->pushMiddlewareToGroup('api', AddSecurityHeaders::class);
    }
}
