<?php

namespace AgabaandreOffice365\ExchangeEmailService;

// Check if Laravel is available
if (class_exists('Illuminate\Support\ServiceProvider')) {
    class ExchangeEmailServiceProvider extends \Illuminate\Support\ServiceProvider
    {
        /**
         * Register services.
         */
        public function register()
        {
            $this->app->singleton(ExchangeEmailService::class, function ($app) {
                $config = [
                    'tenant_id' => \Illuminate\Support\Facades\Config::get('exchange_email.tenant_id'),
                    'client_id' => \Illuminate\Support\Facades\Config::get('exchange_email.client_id'),
                    'client_secret' => \Illuminate\Support\Facades\Config::get('exchange_email.client_secret'),
                    'redirect_uri' => \Illuminate\Support\Facades\Config::get('exchange_email.redirect_uri'),
                    'scope' => \Illuminate\Support\Facades\Config::get('exchange_email.scope'),
                    'from_email' => \Illuminate\Support\Facades\Config::get('exchange_email.from_email'),
                    'from_name' => \Illuminate\Support\Facades\Config::get('exchange_email.from_name'),
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
} else {
    // Fallback for non-Laravel environments
    class ExchangeEmailServiceProvider
    {
        public function __construct()
        {
            throw new \Exception('Laravel framework is required to use ExchangeEmailServiceProvider. Use ExchangeEmailService directly for non-Laravel applications.');
        }
    }
}