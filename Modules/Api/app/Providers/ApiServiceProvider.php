<?php

namespace Modules\Api\Providers;

use Illuminate\Support\ServiceProvider;
use Nwidart\Modules\Traits\PathNamespace;

class ApiServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Api';

    protected string $nameLower = 'api';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
    }


    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
    }
}
