<?php

/**
 * Installation Test Script
 * 
 * This script tests if the package can be installed and used correctly.
 * Run this after publishing to Packagist to verify everything works.
 */

require_once 'vendor/autoload.php';

use SendMail\ExchangeEmailService\ExchangeEmailService;

echo "🧪 Testing SendMail ExchangeEmailService Installation\n";
echo "================================================\n\n";

// Test 1: Check if classes exist
echo "1. Checking if classes exist...\n";
$classes = [
    'SendMail\ExchangeEmailService\ExchangeEmailService',
    'SendMail\ExchangeEmailService\ExchangeOAuth',
    'SendMail\ExchangeEmailService\ExchangeEmailServiceProvider'
];

foreach ($classes as $class) {
    if (class_exists($class)) {
        echo "   ✅ $class\n";
    } else {
        echo "   ❌ $class\n";
    }
}

echo "\n";

// Test 2: Test service instantiation
echo "2. Testing service instantiation...\n";
try {
    $config = [
        'tenant_id' => 'test-tenant',
        'client_id' => 'test-client',
        'client_secret' => 'test-secret',
        'redirect_uri' => 'http://localhost/callback',
        'scope' => 'https://graph.microsoft.com/Mail.Send',
        'from_email' => 'test@example.com',
        'from_name' => 'Test Sender',
    ];
    
    $emailService = new ExchangeEmailService($config);
    echo "   ✅ ExchangeEmailService instantiated successfully\n";
} catch (Exception $e) {
    echo "   ❌ Failed to instantiate ExchangeEmailService: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 3: Check configuration
echo "3. Testing configuration...\n";
$requiredConfig = ['tenant_id', 'client_id', 'client_secret', 'redirect_uri', 'scope'];
$missingConfig = [];

foreach ($requiredConfig as $key) {
    if (!isset($config[$key])) {
        $missingConfig[] = $key;
    }
}

if (empty($missingConfig)) {
    echo "   ✅ All required configuration keys present\n";
} else {
    echo "   ❌ Missing configuration keys: " . implode(', ', $missingConfig) . "\n";
}

echo "\n";

// Test 4: Check dependencies
echo "4. Checking dependencies...\n";
$dependencies = [
    'vlucas/phpdotenv' => 'Dotenv\Dotenv',
    'guzzlehttp/guzzle' => 'GuzzleHttp\Client'
];

foreach ($dependencies as $package => $class) {
    if (class_exists($class)) {
        echo "   ✅ $package\n";
    } else {
        echo "   ❌ $package (class: $class)\n";
    }
}

echo "\n";

// Test 5: Check Laravel integration
echo "5. Testing Laravel integration...\n";
if (class_exists('Illuminate\Support\ServiceProvider')) {
    echo "   ✅ Laravel ServiceProvider class available\n";
} else {
    echo "   ⚠️  Laravel not detected (this is normal if not using Laravel)\n";
}

echo "\n";
echo "🎉 Installation test completed!\n";
echo "If all tests passed, your package is ready to use.\n";
echo "\n";
echo "Next steps:\n";
echo "1. Configure your environment variables\n";
echo "2. Set up OAuth credentials in Azure\n";
echo "3. Run the OAuth setup process\n";
echo "4. Start sending emails!\n";
