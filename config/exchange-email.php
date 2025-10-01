<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Exchange Email Service Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the Exchange Email Service using Microsoft Graph API
    |
    */

    // Microsoft Graph OAuth Configuration
    'tenant_id' => env('EXCHANGE_TENANT_ID'),
    'client_id' => env('EXCHANGE_CLIENT_ID'),
    'client_secret' => env('EXCHANGE_CLIENT_SECRET'),
    'redirect_uri' => env('EXCHANGE_REDIRECT_URI'),
    'scope' => env('EXCHANGE_SCOPE', 'https://graph.microsoft.com/Mail.Send'),

    // Email Configuration
    'from_email' => env('MAIL_FROM_ADDRESS', 'noreply@example.com'),
    'from_name' => env('MAIL_FROM_NAME', 'Exchange Email Service'),

    // Database Configuration (for token storage)
    'database' => [
        'connection' => env('DB_CONNECTION', 'mysql'),
        'table' => 'oauth_tokens',
    ],

    // OAuth Configuration
    'oauth' => [
        'authorize_url' => 'https://login.microsoftonline.com/{tenant_id}/oauth2/v2.0/authorize',
        'token_url' => 'https://login.microsoftonline.com/{tenant_id}/oauth2/v2.0/token',
        'graph_url' => 'https://graph.microsoft.com/v1.0',
    ],

    // Email Templates
    'templates' => [
        'welcome' => 'welcome',
        'notification' => 'notification',
        'confirmation' => 'confirmation',
        'test' => 'test',
    ],

    // Default Settings
    'defaults' => [
        'is_html' => true,
        'timeout' => 30,
        'retry_attempts' => 3,
    ],
];
