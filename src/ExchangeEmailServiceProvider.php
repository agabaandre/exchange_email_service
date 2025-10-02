<?php

namespace AgabaandreOffice365\ExchangeEmailService;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;

/**
 * Exchange Email Service Provider for Laravel
 * 
 * @author Andre Agaba
 * @version 1.0.0
 */
class ExchangeEmailServiceProvider extends ServiceProvider
{
    /**
     * Register services
     */
    public function register()
    {
        $this->app->singleton('exchange-email', function ($app) {
            $config = $this->getConfig();
            return new ExchangeEmailService($config);
        });

        $this->app->alias('exchange-email', ExchangeEmailService::class);
    }

    /**
     * Bootstrap services
     */
    public function boot()
    {
        // Publish configuration file
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/exchange-email.php' => config_path('exchange-email.php'),
            ], 'config');
        }

        // Merge configuration
        $this->mergeConfigFrom(
            __DIR__ . '/../config/exchange-email.php',
            'exchange-email'
        );
    }

    /**
     * Get configuration
     */
    protected function getConfig()
    {
        $config = [];
        
        // Try to get from Laravel config
        if (function_exists('config')) {
            $config = config('exchange-email', []);
        }
        
        // Fallback to environment variables
        $envConfig = [
            'tenant_id' => env('EXCHANGE_TENANT_ID', ''),
            'client_id' => env('EXCHANGE_CLIENT_ID', ''),
            'client_secret' => env('EXCHANGE_CLIENT_SECRET', ''),
            'redirect_uri' => env('EXCHANGE_REDIRECT_URI', ''),
            'scope' => env('EXCHANGE_SCOPE', 'https://graph.microsoft.com/Mail.Send'),
            'auth_method' => env('EXCHANGE_AUTH_METHOD', 'client_credentials'),
            'from_email' => env('MAIL_FROM_ADDRESS', ''),
            'from_name' => env('MAIL_FROM_NAME', 'Exchange Email Service'),
        ];
        
        return array_merge($envConfig, $config);
    }

    /**
     * Get the services provided by the provider
     */
    public function provides()
    {
        return ['exchange-email', ExchangeEmailService::class];
    }
}
