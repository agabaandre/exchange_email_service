<?php
/**
 * Package Test Script
 * 
 * This script tests the Exchange Email Service package
 * in a vanilla PHP environment.
 */

require_once 'vendor/autoload.php';

use AgabaandreOffice365\ExchangeEmailService\ExchangeEmailFactory;

echo "🧪 Testing Exchange Email Service Package\n";
echo "=========================================\n\n";

try {
    // Test 1: Factory with configuration
    echo "1️⃣  Testing Factory with configuration...\n";
    $emailService = ExchangeEmailFactory::create([
        'tenant_id' => 'test-tenant-id',
        'client_id' => 'test-client-id',
        'client_secret' => 'test-client-secret',
        'from_email' => 'test@example.com',
        'from_name' => 'Test Service',
        'auth_method' => 'client_credentials'
    ]);
    
    echo "   ✅ Service created successfully\n";
    echo "   📧 From Email: " . $emailService->getConfig()['from_email'] . "\n";
    echo "   🔐 Auth Method: " . $emailService->getConfig()['auth_method'] . "\n\n";
    
    // Test 2: Factory with environment variables
    echo "2️⃣  Testing Factory with environment variables...\n";
    putenv('EXCHANGE_TENANT_ID=env-tenant-id');
    putenv('EXCHANGE_CLIENT_ID=env-client-id');
    putenv('EXCHANGE_CLIENT_SECRET=env-client-secret');
    putenv('MAIL_FROM_ADDRESS=env@example.com');
    putenv('MAIL_FROM_NAME=Env Service');
    
    $emailService2 = ExchangeEmailFactory::createFromEnv();
    echo "   ✅ Service created from environment\n";
    echo "   📧 From Email: " . $emailService2->getConfig()['from_email'] . "\n\n";
    
    // Test 3: Quick setup
    echo "3️⃣  Testing Quick setup...\n";
    $emailService3 = ExchangeEmailFactory::quickSetup(
        'quick-tenant-id',
        'quick-client-id',
        'quick-client-secret',
        'quick@example.com',
        'Quick Service'
    );
    
    echo "   ✅ Quick setup successful\n";
    echo "   📧 From Email: " . $emailService3->getConfig()['from_email'] . "\n\n";
    
    // Test 4: Configuration methods
    echo "4️⃣  Testing configuration methods...\n";
    $config = $emailService->getConfig();
    echo "   📊 Config keys: " . implode(', ', array_keys($config)) . "\n";
    
    $emailService->updateConfig(['from_name' => 'Updated Service']);
    echo "   ✅ Config updated successfully\n";
    echo "   📧 New From Name: " . $emailService->getConfig()['from_name'] . "\n\n";
    
    // Test 5: Service status
    echo "5️⃣  Testing service status...\n";
    echo "   🔧 Is Configured: " . ($emailService->isConfigured() ? "Yes" : "No") . "\n";
    
    $tokenInfo = $emailService->getTokenInfo();
    echo "   🔑 Token Info: " . json_encode($tokenInfo, JSON_PRETTY_PRINT) . "\n\n";
    
    // Test 6: OAuth methods
    echo "6️⃣  Testing OAuth methods...\n";
    $oauth = $emailService->getOAuth();
    echo "   🔐 OAuth configured: " . ($oauth->isConfigured() ? "Yes" : "No") . "\n";
    
    try {
        $authUrl = $emailService->getAuthorizationUrl();
        echo "   🔗 Auth URL generated: " . (strlen($authUrl) > 0 ? "Yes" : "No") . "\n";
    } catch (Exception $e) {
        echo "   ⚠️  Auth URL (expected error): " . $e->getMessage() . "\n";
    }
    
    echo "\n🎉 All tests completed successfully!\n";
    echo "📦 Package is working correctly!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
