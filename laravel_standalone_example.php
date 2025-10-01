<?php
/**
 * Laravel Standalone Usage Example
 * 
 * This shows how to use the ExchangeEmailService in Laravel
 * without the service provider or publishing configuration.
 * 
 * This is the RECOMMENDED approach for Laravel projects.
 */

require_once 'vendor/autoload.php';

// This would be in your Laravel controller, job, or service class
// No service provider registration needed!

use AgabaandreOffice365\ExchangeEmailService\ExchangeEmailService;

echo "🚀 Laravel Standalone Usage Example\n";
echo "==================================\n\n";

// Method 1: Using Laravel's config() helper
echo "Method 1: Using Laravel's config() helper\n";
echo "----------------------------------------\n";

// In a real Laravel app, you'd add this to config/services.php:
/*
'exchange' => [
    'tenant_id' => env('EXCHANGE_TENANT_ID'),
    'client_id' => env('EXCHANGE_CLIENT_ID'),
    'client_secret' => env('EXCHANGE_CLIENT_SECRET'),
    'redirect_uri' => env('EXCHANGE_REDIRECT_URI'),
],
*/

// Simulate Laravel config (in real app, use config() helper)
$config = [
    'services' => [
        'exchange' => [
            'tenant_id' => 'your-tenant-id',
            'client_id' => 'your-client-id',
            'client_secret' => 'your-client-secret',
            'redirect_uri' => 'http://your-domain.com/oauth/callback',
        ],
        'mail' => [
            'from' => [
                'address' => 'noreply@yourcompany.com',
                'name' => 'Your Company'
            ]
        ]
    ]
];

// Create email service using Laravel config
$emailService = new ExchangeEmailService([
    'tenant_id' => $config['services']['exchange']['tenant_id'],
    'client_id' => $config['services']['exchange']['client_id'],
    'client_secret' => $config['services']['exchange']['client_secret'],
    'redirect_uri' => $config['services']['exchange']['redirect_uri'],
    'from_email' => $config['services']['mail']['from']['address'],
    'from_name' => $config['services']['mail']['from']['name']
]);

echo "✅ Email service created using Laravel config\n\n";

// Method 2: Using environment variables directly
echo "Method 2: Using environment variables\n";
echo "------------------------------------\n";

// In Laravel, you can use env() helper or just pass array
$emailService2 = new ExchangeEmailService([
    'tenant_id' => env('EXCHANGE_TENANT_ID', 'your-tenant-id'),
    'client_id' => env('EXCHANGE_CLIENT_ID', 'your-client-id'),
    'client_secret' => env('EXCHANGE_CLIENT_SECRET', 'your-client-secret'),
    'redirect_uri' => env('EXCHANGE_REDIRECT_URI', 'http://your-domain.com/oauth/callback'),
    'from_email' => env('MAIL_FROM_ADDRESS', 'noreply@yourcompany.com'),
    'from_name' => env('MAIL_FROM_NAME', 'Your Company')
]);

echo "✅ Email service created using environment variables\n\n";

// Method 3: Using a dedicated service class
echo "Method 3: Using a dedicated service class\n";
echo "----------------------------------------\n";

class ExchangeEmailServiceWrapper
{
    private $emailService;
    
    public function __construct()
    {
        $this->emailService = new ExchangeEmailService([
            'tenant_id' => config('services.exchange.tenant_id'),
            'client_id' => config('services.exchange.client_id'),
            'client_secret' => config('services.exchange.client_secret'),
            'redirect_uri' => config('services.exchange.redirect_uri'),
            'from_email' => config('mail.from.address'),
            'from_name' => config('mail.from.name')
        ]);
    }
    
    public function sendWelcomeEmail($email, $name)
    {
        return $this->emailService->sendTemplateEmail(
            $email,
            'Welcome!',
            'welcome',
            ['name' => $name, 'app_name' => config('app.name')]
        );
    }
    
    public function sendNotification($email, $title, $message)
    {
        return $this->emailService->sendTemplateEmail(
            $email,
            $title,
            'notification',
            [
                'name' => 'User',
                'title' => $title,
                'message' => $message,
                'app_name' => config('app.name')
            ]
        );
    }
}

echo "✅ Service wrapper class created\n\n";

// Method 4: Using in Laravel Jobs
echo "Method 4: Using in Laravel Jobs\n";
echo "-------------------------------\n";

/*
// In your Laravel Job class
class SendWelcomeEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    protected $email;
    protected $name;
    
    public function __construct($email, $name)
    {
        $this->email = $email;
        $this->name = $name;
    }
    
    public function handle()
    {
        $emailService = new ExchangeEmailService([
            'tenant_id' => config('services.exchange.tenant_id'),
            'client_id' => config('services.exchange.client_id'),
            'client_secret' => config('services.exchange.client_secret'),
            'redirect_uri' => config('services.exchange.redirect_uri'),
            'from_email' => config('mail.from.address'),
            'from_name' => config('mail.from.name')
        ]);
        
        $emailService->sendTemplateEmail(
            $this->email,
            'Welcome!',
            'welcome',
            ['name' => $this->name, 'app_name' => config('app.name')]
        );
    }
}
*/

echo "✅ Job class example provided\n\n";

// Method 5: Using in Laravel Commands
echo "Method 5: Using in Laravel Commands\n";
echo "----------------------------------\n";

/*
// In your Laravel Command class
class SendTestEmailCommand extends Command
{
    protected $signature = 'email:test {email}';
    protected $description = 'Send a test email';
    
    public function handle()
    {
        $email = $this->argument('email');
        
        $emailService = new ExchangeEmailService([
            'tenant_id' => config('services.exchange.tenant_id'),
            'client_id' => config('services.exchange.client_id'),
            'client_secret' => config('services.exchange.client_secret'),
            'redirect_uri' => config('services.exchange.redirect_uri'),
            'from_email' => config('mail.from.address'),
            'from_name' => config('mail.from.name')
        ]);
        
        $result = $emailService->sendTestEmail($email);
        
        if ($result) {
            $this->info('Test email sent successfully!');
        } else {
            $this->error('Failed to send test email.');
        }
    }
}
*/

echo "✅ Command class example provided\n\n";

echo "🎉 Laravel Standalone Usage Examples Complete!\n\n";
echo "📋 Summary:\n";
echo "- No service provider registration needed\n";
echo "- No vendor:publish commands needed\n";
echo "- Works in controllers, jobs, commands, services\n";
echo "- Uses Laravel's config() and env() helpers\n";
echo "- Clean and simple integration\n";
echo "- Full control over configuration\n\n";
echo "💡 This is the RECOMMENDED approach for Laravel projects!\n";
