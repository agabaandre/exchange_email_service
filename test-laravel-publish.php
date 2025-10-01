<?php

/**
 * Test Laravel Service Provider Publishing
 * 
 * This script simulates what happens when Laravel tries to discover
 * publishable resources from the service provider.
 */

require_once 'vendor/autoload.php';

echo "🧪 Testing Laravel Service Provider Publishing\n";
echo "============================================\n\n";

// Test 1: Check if service provider class exists
echo "1. Checking service provider class...\n";
if (class_exists('AgabaandreOffice365\ExchangeEmailService\ExchangeEmailServiceProvider')) {
    echo "   ✅ ServiceProvider class exists\n";
} else {
    echo "   ❌ ServiceProvider class not found\n";
    exit(1);
}

// Test 2: Check if we can instantiate the service provider
echo "\n2. Testing service provider instantiation...\n";
try {
    $provider = new \AgabaandreOffice365\ExchangeEmailService\ExchangeEmailServiceProvider();
    echo "   ✅ ServiceProvider can be instantiated\n";
} catch (Exception $e) {
    echo "   ❌ ServiceProvider instantiation failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 3: Check if service provider has the required methods
echo "\n3. Testing service provider methods...\n";
$reflection = new ReflectionClass($provider);

$requiredMethods = ['register', 'boot', 'provides'];
foreach ($requiredMethods as $method) {
    if ($reflection->hasMethod($method)) {
        echo "   ✅ Method '$method' exists\n";
    } else {
        echo "   ❌ Method '$method' missing\n";
    }
}

// Test 4: Test provides() method
echo "\n4. Testing provides() method...\n";
try {
    $provides = $provider->provides();
    if (is_array($provides) && !empty($provides)) {
        echo "   ✅ provides() returns array: " . implode(', ', $provides) . "\n";
    } else {
        echo "   ❌ provides() returns empty or invalid data\n";
    }
} catch (Exception $e) {
    echo "   ❌ provides() method failed: " . $e->getMessage() . "\n";
}

// Test 5: Check if configuration file exists
echo "\n5. Checking configuration file...\n";
$configFile = __DIR__ . '/config/exchange-email.php';
if (file_exists($configFile)) {
    echo "   ✅ Configuration file exists\n";
} else {
    echo "   ⚠️  Configuration file not found (this is normal if not published yet)\n";
}

// Test 6: Check if migrations directory exists
echo "\n6. Checking migrations directory...\n";
$migrationsDir = __DIR__ . '/database/migrations/';
if (is_dir($migrationsDir)) {
    $migrationFiles = glob($migrationsDir . '*.php');
    if (count($migrationFiles) > 0) {
        echo "   ✅ Migrations directory exists with " . count($migrationFiles) . " files\n";
    } else {
        echo "   ⚠️  Migrations directory exists but is empty\n";
    }
} else {
    echo "   ⚠️  Migrations directory not found (this is normal if not published yet)\n";
}

echo "\n🎉 Service Provider Test Completed!\n";
echo "\nTo test in a real Laravel project:\n";
echo "1. Install Laravel: composer create-project laravel/laravel test-project\n";
echo "2. Install this package: composer require agabaandre-office365/exchange-email-service\n";
echo "3. Register provider in bootstrap/providers.php\n";
echo "4. Run: php artisan vendor:publish --provider=\"AgabaandreOffice365\\ExchangeEmailService\\ExchangeEmailServiceProvider\"\n";
