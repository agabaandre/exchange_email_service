<?php
/**
 * Laravel Usage Example
 * 
 * This example shows how to use the Exchange Email Service
 * in a Laravel application.
 */

// After installing the package via Composer, register the service provider
// in config/app.php:

/*
'providers' => [
    // ... other providers
    AgabaandreOffice365\ExchangeEmailService\ExchangeEmailServiceProvider::class,
],
*/

// Publish the configuration file:
// php artisan vendor:publish --provider="AgabaandreOffice365\ExchangeEmailService\ExchangeEmailServiceProvider" --tag="config"

// Add to your .env file:
/*
EXCHANGE_TENANT_ID=your-tenant-id
EXCHANGE_CLIENT_ID=your-client-id
EXCHANGE_CLIENT_SECRET=your-client-secret
EXCHANGE_REDIRECT_URI=http://yourdomain.com/oauth/callback
EXCHANGE_SCOPE=https://graph.microsoft.com/Mail.Send
EXCHANGE_AUTH_METHOD=client_credentials
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME=Your App Name
*/

// Usage in Controllers, Jobs, etc.

use AgabaandreOffice365\ExchangeEmailService\ExchangeEmailService;

class EmailController extends Controller
{
    protected $emailService;

    public function __construct(ExchangeEmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function sendWelcomeEmail(Request $request)
    {
        try {
            $result = $this->emailService->sendEmail(
                $request->email,
                'Welcome to Our App!',
                '<h1>Welcome!</h1><p>Thank you for joining us.</p>',
                true
            );

            return response()->json(['success' => $result]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}

// Usage in Jobs
class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $email;
    protected $subject;
    protected $body;

    public function __construct($email, $subject, $body)
    {
        $this->email = $email;
        $this->subject = $subject;
        $this->body = $body;
    }

    public function handle(ExchangeEmailService $emailService)
    {
        $emailService->sendEmail($this->email, $this->subject, $this->body);
    }
}

// Usage in Blade templates (via Facade)
// In config/app.php, add the alias:
/*
'aliases' => [
    // ... other aliases
    'ExchangeEmail' => AgabaandreOffice365\ExchangeEmailService\ExchangeEmailService::class,
],
*/

// Then in your Blade template or anywhere in your app:
/*
@if(ExchangeEmail::isConfigured())
    <p>Email service is ready!</p>
@else
    <p>Email service needs configuration.</p>
@endif
*/

// Usage in Artisan Commands
class SendTestEmailCommand extends Command
{
    protected $signature = 'email:test {email}';
    protected $description = 'Send a test email';

    public function handle(ExchangeEmailService $emailService)
    {
        $email = $this->argument('email');
        
        try {
            $result = $emailService->sendEmail(
                $email,
                'Test Email from Laravel',
                '<h1>Test Email</h1><p>This is a test email from Laravel.</p>'
            );

            $this->info($result ? 'Email sent successfully!' : 'Failed to send email.');
        } catch (Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }
}

// Usage in Service Classes
class NotificationService
{
    protected $emailService;

    public function __construct(ExchangeEmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function sendUserNotification($user, $type, $data = [])
    {
        $templates = [
            'welcome' => '<h1>Welcome {{name}}!</h1><p>Thank you for joining us.</p>',
            'password_reset' => '<h1>Password Reset</h1><p>Click here to reset your password: {{link}}</p>',
            'order_confirmation' => '<h1>Order Confirmed</h1><p>Your order {{order_id}} has been confirmed.</p>'
        ];

        $template = $templates[$type] ?? $templates['welcome'];
        $data['name'] = $user->name;

        return $this->emailService->sendTemplateEmail(
            $user->email,
            ucfirst($type) . ' Notification',
            $template,
            $data
        );
    }
}
