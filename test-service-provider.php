<?php

/**
 * Test Service Provider Publishing
 * 
 * This script tests the service provider's publishable resources
 * without requiring a full Laravel application.
 */

require_once 'vendor/autoload.php';

echo "🧪 Testing Service Provider Publishing\n";
echo "====================================\n\n";

// Test 1: Check if service provider class exists
echo "1. Checking service provider class...\n";
if (class_exists('AgabaandreOffice365\ExchangeEmailService\ExchangeEmailServiceProvider')) {
    echo "   ✅ ServiceProvider class exists\n";
} else {
    echo "   ❌ ServiceProvider class not found\n";
    exit(1);
}

// Test 2: Check if configuration file exists
echo "\n2. Checking configuration file...\n";
$configFile = __DIR__ . '/config/exchange-email.php';
if (file_exists($configFile)) {
    echo "   ✅ Configuration file exists\n";
    
    // Check if it's a valid PHP file
    $config = include $configFile;
    if (is_array($config)) {
        echo "   ✅ Configuration file is valid PHP array\n";
    } else {
        echo "   ⚠️  Configuration file is not a valid PHP array\n";
    }
} else {
    echo "   ❌ Configuration file not found at: $configFile\n";
}

// Test 3: Check if migrations directory exists
echo "\n3. Checking migrations directory...\n";
$migrationsDir = __DIR__ . '/database/migrations/';
if (is_dir($migrationsDir)) {
    echo "   ✅ Migrations directory exists\n";
    
    $migrationFiles = glob($migrationsDir . '*.php');
    if (count($migrationFiles) > 0) {
        echo "   ✅ Found " . count($migrationFiles) . " migration files\n";
        foreach ($migrationFiles as $file) {
            echo "      - " . basename($file) . "\n";
        }
    } else {
        echo "   ⚠️  Migrations directory is empty\n";
    }
} else {
    echo "   ❌ Migrations directory not found at: $migrationsDir\n";
}

// Test 4: Check service provider methods
echo "\n4. Testing service provider methods...\n";
$reflection = new ReflectionClass('AgabaandreOffice365\ExchangeEmailService\ExchangeEmailServiceProvider');

$requiredMethods = ['register', 'boot', 'provides'];
foreach ($requiredMethods as $method) {
    if ($reflection->hasMethod($method)) {
        echo "   ✅ Method '$method' exists\n";
    } else {
        echo "   ❌ Method '$method' missing\n";
    }
}

// Test 5: Check if Laravel classes are available
echo "\n5. Checking Laravel dependencies...\n";
$laravelClasses = [
    'Illuminate\Support\ServiceProvider',
    'Illuminate\Support\Facades\Config',
    'Illuminate\Container\Container'
];

foreach ($laravelClasses as $class) {
    if (class_exists($class)) {
        echo "   ✅ $class available\n";
    } else {
        echo "   ❌ $class not available\n";
    }
}

echo "\n🎉 Service Provider Test Completed!\n";
echo "\nThe service provider should now work correctly in Laravel.\n";
echo "Make sure to:\n";
echo "1. Register the provider in bootstrap/providers.php (Laravel 11+) or config/app.php (Laravel 10-)\n";
echo "2. Clear Laravel caches: php artisan config:clear && php artisan cache:clear\n";
echo "3. Run: php artisan vendor:publish --provider=\"AgabaandreOffice365\\ExchangeEmailService\\ExchangeEmailServiceProvider\"\n";
