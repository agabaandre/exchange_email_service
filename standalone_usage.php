<?php
/**
 * Standalone Usage Example
 * 
 * This shows how to use the ExchangeEmailService without Laravel
 * or any service provider. Perfect for any PHP application!
 */

require_once 'vendor/autoload.php';

use AgabaandreOffice365\ExchangeEmailService\ExchangeEmailService;

echo "🚀 ExchangeEmailService - Standalone Usage\n";
echo "==========================================\n\n";

// Step 1: Create the email service instance
echo "1. Creating email service instance...\n";

$emailService = new ExchangeEmailService([
    'tenant_id' => 'your-tenant-id-here',
    'client_id' => 'your-client-id-here', 
    'client_secret' => 'your-client-secret-here',
    'redirect_uri' => 'http://your-domain.com/oauth/callback',
    'from_email' => 'noreply@yourcompany.com',
    'from_name' => 'Your Company Name'
]);

echo "   ✅ Email service created\n\n";

// Step 2: Check configuration
echo "2. Checking configuration...\n";
if ($emailService->isConfigured()) {
    echo "   ✅ Service is properly configured\n";
} else {
    echo "   ❌ Service not configured. Please check your settings.\n";
    echo "   Required: tenant_id, client_id, client_secret, redirect_uri\n\n";
    echo "   You can also use environment variables:\n";
    echo "   - EXCHANGE_TENANT_ID\n";
    echo "   - EXCHANGE_CLIENT_ID\n";
    echo "   - EXCHANGE_CLIENT_SECRET\n";
    echo "   - EXCHANGE_REDIRECT_URI\n";
    echo "   - MAIL_FROM_ADDRESS\n";
    echo "   - MAIL_FROM_NAME\n\n";
    exit;
}

// Step 3: Check OAuth tokens
echo "3. Checking OAuth tokens...\n";
if ($emailService->hasValidTokens()) {
    echo "   ✅ Valid OAuth tokens available\n";
} else {
    echo "   ⚠️  No valid OAuth tokens found\n";
    echo "   🔗 OAuth URL: " . $emailService->getOAuthUrl() . "\n";
    echo "   Please complete OAuth setup first.\n\n";
    exit;
}

// Step 4: Send a simple email
echo "4. Sending test email...\n";
try {
    $result = $emailService->sendEmail(
        'test@example.com',
        'Test Email from ExchangeEmailService',
        '<h1>Hello World!</h1><p>This is a test email sent using the standalone ExchangeEmailService.</p>',
        true // is HTML
    );
    
    if ($result) {
        echo "   ✅ Test email sent successfully!\n";
    } else {
        echo "   ❌ Failed to send test email\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

echo "\n🎉 Standalone usage example completed!\n";
echo "\n📚 More Examples:\n";
echo "- See example_usage.php for comprehensive examples\n";
echo "- Check README.md for full documentation\n";
echo "- Use in any PHP application without Laravel!\n";
