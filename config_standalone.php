<?php
/**
 * Standalone Configuration
 * 
 * Copy this file and customize it for your application.
 * This is an alternative to using environment variables.
 */

return [
    // Microsoft Graph OAuth Configuration
    'tenant_id' => 'your-tenant-id-here',
    'client_id' => 'your-client-id-here', 
    'client_secret' => 'your-client-secret-here',
    'redirect_uri' => 'http://your-domain.com/oauth/callback',
    'scope' => 'https://graph.microsoft.com/Mail.Send',
    
    // Email Configuration
    'from_email' => 'noreply@yourcompany.com',
    'from_name' => 'Your Company Name',
    
    // Optional: Database Configuration (for token storage)
    'database' => [
        'host' => 'localhost',
        'database' => 'your_database',
        'username' => 'your_username',
        'password' => 'your_password',
    ]
];
