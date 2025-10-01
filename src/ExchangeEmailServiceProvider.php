<?php

namespace SendMail\ExchangeEmailService;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;

class ExchangeEmailServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
        $this->app->singleton(ExchangeEmailService::class, function ($app) {
            $config = [
                'tenant_id' => config('exchange_email.tenant_id'),
                'client_id' => config('exchange_email.client_id'),
                'client_secret' => config('exchange_email.client_secret'),
                'redirect_uri' => config('exchange_email.redirect_uri'),
                'scope' => config('exchange_email.scope'),
                'from_email' => config('exchange_email.from_email'),
                'from_name' => config('exchange_email.from_name'),
            ];

            return new ExchangeEmailService($config);
        });

        $this->app->alias(ExchangeEmailService::class, 'exchange-email');
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
        // Publish configuration file
        $this->publishes([
            __DIR__ . '/../config/exchange-email.php' => config_path('exchange-email.php'),
        ], 'config');

        // Publish migrations
        $this->publishes([
            __DIR__ . '/../database/migrations/' => database_path('migrations'),
        ], 'migrations');

        // Load configuration
        $this->mergeConfigFrom(
            __DIR__ . '/../config/exchange-email.php', 'exchange_email'
        );
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides()
    {
        return [ExchangeEmailService::class, 'exchange-email'];
    }
}
