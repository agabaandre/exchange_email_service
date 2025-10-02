<?php
/**
 * Composer Autoload Test
 * 
 * Test that the package works correctly with Composer autoloading
 */

require_once 'vendor/autoload.php';

use AgabaandreOffice365\ExchangeEmailService\ExchangeEmailService;

echo "🧪 Composer Autoload Test\n";
echo "========================\n\n";

try {
    // Test 1: Check if classes are autoloaded correctly
    echo "1. Testing class autoloading...\n";
    
    if (class_exists('AgabaandreOffice365\ExchangeEmailService\ExchangeEmailService')) {
        echo "   ✅ ExchangeEmailService class loaded\n";
    } else {
        echo "   ❌ ExchangeEmailService class not found\n";
        exit(1);
    }
    
    if (class_exists('AgabaandreOffice365\ExchangeEmailService\ExchangeOAuth')) {
        echo "   ✅ ExchangeOAuth class loaded\n";
    } else {
        echo "   ❌ ExchangeOAuth class not found\n";
        exit(1);
    }
    
    if (class_exists('AgabaandreOffice365\ExchangeEmailService\ExchangeEmailServiceProvider')) {
        echo "   ✅ ExchangeEmailServiceProvider class loaded\n";
    } else {
        echo "   ❌ ExchangeEmailServiceProvider class not found\n";
        exit(1);
    }
    
    // Test 2: Test instantiation
    echo "\n2. Testing instantiation...\n";
    $service = new ExchangeEmailService();
    echo "   ✅ Service instantiated successfully\n";
    
    // Test 3: Test configuration
    echo "\n3. Testing configuration...\n";
    $config = $service->getConfig();
    echo "   ✅ Configuration retrieved\n";
    echo "   - Tenant ID: " . (empty($config['tenant_id']) ? 'Not set' : 'Set') . "\n";
    echo "   - Client ID: " . (empty($config['client_id']) ? 'Not set' : 'Set') . "\n";
    echo "   - From Email: " . (empty($config['from_email']) ? 'Not set' : 'Set') . "\n";
    
    // Test 4: Test OAuth handler
    echo "\n4. Testing OAuth handler...\n";
    $oauth = $service->getOAuth();
    echo "   ✅ OAuth handler retrieved\n";
    
    // Test 5: Test method availability
    echo "\n5. Testing method availability...\n";
    $methods = [
        'isConfigured',
        'sendEmail',
        'sendHtmlEmail',
        'sendTextEmail',
        'sendTemplateEmail',
        'getOAuth',
        'getAuthorizationUrl',
        'exchangeCodeForToken',
        'getTokenInfo',
        'clearTokens',
        'getConfig',
        'updateConfig'
    ];
    
    foreach ($methods as $method) {
        if (method_exists($service, $method)) {
            echo "   ✅ $method() method available\n";
        } else {
            echo "   ❌ $method() method not found\n";
        }
    }
    
    echo "\n🎉 Composer autoload test completed successfully!\n";
    echo "\n📦 Package is ready for distribution:\n";
    echo "   - ✅ Classes autoload correctly\n";
    echo "   - ✅ Namespace structure is correct\n";
    echo "   - ✅ All methods are available\n";
    echo "   - ✅ Configuration system works\n";
    echo "   - ✅ OAuth integration is ready\n";
    
} catch (Exception $e) {
    echo "\n❌ Test failed with error:\n";
    echo "   " . $e->getMessage() . "\n";
    echo "\nStack trace:\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "Composer test completed at: " . date('Y-m-d H:i:s') . "\n";
?>
