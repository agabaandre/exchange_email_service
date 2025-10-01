<?php
/**
 * Simple Usage Example - No Laravel Required
 * 
 * This is the simplest way to use the ExchangeEmailService
 * in any PHP application.
 */

require_once 'vendor/autoload.php';

use AgabaandreOffice365\ExchangeEmailService\ExchangeEmailService;

// Method 1: Using array configuration
$emailService = new ExchangeEmailService([
    'tenant_id' => 'your-tenant-id',
    'client_id' => 'your-client-id',
    'client_secret' => 'your-client-secret',
    'redirect_uri' => 'http://your-domain.com/oauth/callback',
    'from_email' => 'noreply@yourcompany.com',
    'from_name' => 'Your Company'
]);

// Method 2: Using environment variables (recommended)
// Set these in your .env file or system environment
$emailService = new ExchangeEmailService(); // Will read from environment

// Method 3: Using configuration file
$config = include 'config_standalone.php';
$emailService = new ExchangeEmailService($config);

// Send an email
try {
    $result = $emailService->sendEmail(
        'recipient@example.com',
        'Subject Line',
        '<h1>Hello!</h1><p>This is the email body.</p>',
        true // is HTML
    );
    
    if ($result) {
        echo "Email sent successfully!";
    } else {
        echo "Failed to send email.";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

// Send with CC and BCC
$emailService->sendEmail(
    'recipient@example.com',
    'Subject',
    'Message',
    true, // is HTML
    'noreply@company.com', // from email
    'Company Name', // from name
    ['cc@example.com'], // CC
    ['bcc@example.com'] // BCC
);

// Send bulk email
$recipients = ['user1@example.com', 'user2@example.com', 'user3@example.com'];
$emailService->sendBulkEmail($recipients, 'Bulk Subject', 'Bulk message content');

// Send with attachments
$attachments = [
    [
        'name' => 'document.pdf',
        'content' => file_get_contents('path/to/document.pdf'),
        'content_type' => 'application/pdf'
    ]
];

$emailService->sendEmail(
    'recipient@example.com',
    'Email with Attachment',
    'Please find the attached document.',
    true,
    'noreply@company.com',
    'Company Name',
    [],
    [],
    $attachments
);
