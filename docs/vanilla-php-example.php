<?php
/**
 * Vanilla PHP Usage Example
 * 
 * This example shows how to use the Exchange Email Service
 * in a vanilla PHP project without any framework.
 */

require_once 'vendor/autoload.php';

use AgabaandreOffice365\ExchangeEmailService\ExchangeEmailFactory;

// Method 1: Using Factory with configuration array
$emailService = ExchangeEmailFactory::create([
    'tenant_id' => 'your-tenant-id',
    'client_id' => 'your-client-id',
    'client_secret' => 'your-client-secret',
    'from_email' => 'noreply@yourdomain.com',
    'from_name' => 'Your App Name',
    'auth_method' => 'client_credentials'
]);

// Method 2: Using Factory with environment variables
// Set these in your .env file or system environment
putenv('EXCHANGE_TENANT_ID=your-tenant-id');
putenv('EXCHANGE_CLIENT_ID=your-client-id');
putenv('EXCHANGE_CLIENT_SECRET=your-client-secret');
putenv('MAIL_FROM_ADDRESS=noreply@yourdomain.com');
putenv('MAIL_FROM_NAME=Your App Name');

$emailService = ExchangeEmailFactory::createFromEnv();

// Method 3: Using Factory with config file
$emailService = ExchangeEmailFactory::createFromConfig('path/to/your/config.php');

// Method 4: Quick setup for simple cases
$emailService = ExchangeEmailFactory::quickSetup(
    'your-tenant-id',
    'your-client-id', 
    'your-client-secret',
    'noreply@yourdomain.com',
    'Your App Name'
);

// Check if service is configured
if (!$emailService->isConfigured()) {
    die('Email service is not configured. Please provide tenant_id, client_id, and client_secret.');
}

// Send a simple email
try {
    $result = $emailService->sendEmail(
        'recipient@example.com',
        'Test Email',
        '<h1>Hello World!</h1><p>This is a test email.</p>',
        true // HTML email
    );
    
    if ($result) {
        echo "Email sent successfully!";
    } else {
        echo "Failed to send email.";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

// Send HTML email
$emailService->sendHtmlEmail(
    'recipient@example.com',
    'HTML Email',
    '<h1>Hello!</h1><p>This is an HTML email.</p>'
);

// Send text email
$emailService->sendTextEmail(
    'recipient@example.com',
    'Text Email',
    'Hello! This is a plain text email.'
);

// Send email with template
$template = '<h1>Welcome {{name}}!</h1><p>Your order {{order_id}} is confirmed.</p>';
$data = [
    'name' => 'John Doe',
    'order_id' => '12345'
];

$emailService->sendTemplateEmail(
    'recipient@example.com',
    'Order Confirmation',
    $template,
    $data
);

// Send email with CC and BCC
$emailService->sendEmail(
    'recipient@example.com',
    'Email with CC/BCC',
    '<p>This email has CC and BCC recipients.</p>',
    true,
    null, // from_email (uses default)
    null, // from_name (uses default)
    ['cc@example.com'], // CC
    ['bcc@example.com'] // BCC
);

// Get token information
$tokenInfo = $emailService->getTokenInfo();
echo "Token expires in: " . $tokenInfo['expires_in'] . " seconds\n";

// Clear stored tokens (useful for logout)
$emailService->clearTokens();
