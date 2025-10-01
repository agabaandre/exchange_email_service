<?php

namespace AgabaandreOffice365\ExchangeEmailService;

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
                'tenant_id' => Config::get('exchange_email.tenant_id'),
                'client_id' => Config::get('exchange_email.client_id'),
                'client_secret' => Config::get('exchange_email.client_secret'),
                'redirect_uri' => Config::get('exchange_email.redirect_uri'),
                'scope' => Config::get('exchange_email.scope'),
                'from_email' => Config::get('exchange_email.from_email'),
                'from_name' => Config::get('exchange_email.from_name'),
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
        // Load configuration
        $this->mergeConfigFrom(
            __DIR__ . '/../config/exchange-email.php', 'exchange_email'
        );

        // Publish configuration file
        $this->publishes([
            __DIR__ . '/../config/exchange-email.php' => config_path('exchange-email.php'),
        ], 'exchange-email-config');

        // Publish migrations
        $this->publishes([
            __DIR__ . '/../database/migrations/' => database_path('migrations'),
        ], 'exchange-email-migrations');
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides()
    {
        return [ExchangeEmailService::class, 'exchange-email'];
    }
}