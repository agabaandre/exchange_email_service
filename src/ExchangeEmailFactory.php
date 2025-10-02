<?php

namespace AgabaandreOffice365\ExchangeEmailService;

/**
 * Exchange Email Factory
 * 
 * Simple factory class for creating ExchangeEmailService instances
 * in vanilla PHP projects without dependency injection.
 * 
 * @author Andre Agaba
 * @version 1.0.0
 */
class ExchangeEmailFactory
{
    /**
     * Create ExchangeEmailService instance
     */
    public static function create(array $config = [])
    {
        // Load default configuration if not provided
        if (empty($config)) {
            $config = self::loadDefaultConfig();
        }

        return new ExchangeEmailService($config);
    }

    /**
     * Create ExchangeEmailService instance from environment variables
     */
    public static function createFromEnv()
    {
        $config = [
            'tenant_id' => getenv('EXCHANGE_TENANT_ID') ?: '',
            'client_id' => getenv('EXCHANGE_CLIENT_ID') ?: '',
            'client_secret' => getenv('EXCHANGE_CLIENT_SECRET') ?: '',
            'redirect_uri' => getenv('EXCHANGE_REDIRECT_URI') ?: '',
            'scope' => getenv('EXCHANGE_SCOPE') ?: 'https://graph.microsoft.com/Mail.Send',
            'auth_method' => getenv('EXCHANGE_AUTH_METHOD') ?: 'client_credentials',
            'from_email' => getenv('MAIL_FROM_ADDRESS') ?: '',
            'from_name' => getenv('MAIL_FROM_NAME') ?: 'Exchange Email Service',
        ];

        return new ExchangeEmailService($config);
    }

    /**
     * Create ExchangeEmailService instance from config file
     */
    public static function createFromConfig($configPath = null)
    {
        $configPath = $configPath ?: __DIR__ . '/../config/exchange-email.php';
        
        if (file_exists($configPath)) {
            $config = include $configPath;
        } else {
            $config = self::loadDefaultConfig();
        }

        return new ExchangeEmailService($config);
    }

    /**
     * Load default configuration
     */
    protected static function loadDefaultConfig()
    {
        return [
            'tenant_id' => '',
            'client_id' => '',
            'client_secret' => '',
            'redirect_uri' => '',
            'scope' => 'https://graph.microsoft.com/Mail.Send',
            'auth_method' => 'client_credentials',
            'from_email' => '',
            'from_name' => 'Exchange Email Service',
            'token_storage' => [
                'type' => 'file',
                'path' => 'tokens/oauth_tokens.json',
                'permissions' => 0644,
            ],
            'defaults' => [
                'is_html' => true,
                'timeout' => 30,
                'retry_attempts' => 3,
                'debug' => false,
            ]
        ];
    }

    /**
     * Quick setup for common scenarios
     */
    public static function quickSetup($tenantId, $clientId, $clientSecret, $fromEmail, $fromName = null)
    {
        $config = [
            'tenant_id' => $tenantId,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'from_email' => $fromEmail,
            'from_name' => $fromName ?: 'Exchange Email Service',
            'auth_method' => 'client_credentials',
        ];

        return new ExchangeEmailService($config);
    }
}
