<?php
/**
 * SendMail ExchangeEmailService - Example Usage
 * 
 * This file demonstrates how to use the ExchangeEmailService
 * for various email sending scenarios.
 */

require_once 'vendor/autoload.php';

use AgabaandreOffice365\ExchangeEmailService\ExchangeEmailService;

// Example 1: Basic Configuration
echo "<h2>Example 1: Basic Email Sending</h2>";

$emailService = new ExchangeEmailService([
    'tenant_id' => 'your-tenant-id',
    'client_id' => 'your-client-id',
    'client_secret' => 'your-client-secret',
    'redirect_uri' => 'http://your-domain.com/oauth/callback',
    'from_email' => 'noreply@yourcompany.com',
    'from_name' => 'Your Company'
]);

// Check if configured
if ($emailService->isConfigured()) {
    echo "<p>✅ Email service is configured</p>";
} else {
    echo "<p>❌ Email service not configured. Please check your settings.</p>";
    exit;
}

// Check if we have valid tokens
if ($emailService->hasValidTokens()) {
    echo "<p>✅ Valid OAuth tokens available</p>";
    
    // Send a test email
    try {
        $emailService->sendTestEmail('test@example.com');
        echo "<p>✅ Test email sent successfully!</p>";
    } catch (Exception $e) {
        echo "<p>❌ Failed to send test email: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p>⚠️ No valid OAuth tokens. Please complete OAuth setup first.</p>";
    echo "<p><a href='" . $emailService->getOAuthUrl() . "'>Complete OAuth Setup</a></p>";
}

// Example 2: Using Templates
echo "<h2>Example 2: Using Email Templates</h2>";

try {
    // Send welcome email
    $emailService->sendTemplateEmail(
        'newuser@example.com',
        'Welcome to Our Service!',
        'welcome',
        [
            'name' => 'John Doe',
            'app_name' => 'My Awesome App'
        ]
    );
    echo "<p>✅ Welcome email sent using template</p>";
} catch (Exception $e) {
    echo "<p>❌ Failed to send welcome email: " . $e->getMessage() . "</p>";
}

// Example 3: Bulk Email Sending
echo "<h2>Example 3: Bulk Email Sending</h2>";

$recipients = [
    'user1@example.com',
    'user2@example.com',
    'user3@example.com'
];

try {
    $emailService->sendBulkEmail(
        $recipients,
        'Newsletter - Important Update',
        '<h1>Monthly Newsletter</h1><p>Check out our latest updates and features!</p>'
    );
    echo "<p>✅ Newsletter sent to " . count($recipients) . " recipients</p>";
} catch (Exception $e) {
    echo "<p>❌ Failed to send newsletter: " . $e->getMessage() . "</p>";
}

// Example 4: Email with CC and BCC
echo "<h2>Example 4: Email with CC and BCC</h2>";

try {
    $emailService->sendEmail(
        'customer@example.com',
        'Order Confirmation',
        '<h1>Your Order Has Been Confirmed</h1><p>Thank you for your purchase!</p>',
        true, // is HTML
        'orders@yourcompany.com', // from email
        'Your Company Orders', // from name
        ['manager@yourcompany.com'], // CC
        ['admin@yourcompany.com'] // BCC
    );
    echo "<p>✅ Order confirmation sent with CC and BCC</p>";
} catch (Exception $e) {
    echo "<p>❌ Failed to send order confirmation: " . $e->getMessage() . "</p>";
}

// Example 5: Email with Attachments
echo "<h2>Example 5: Email with Attachments</h2>";

try {
    $attachments = [
        [
            'name' => 'invoice.pdf',
            'content' => file_get_contents('path/to/invoice.pdf'),
            'content_type' => 'application/pdf'
        ],
        [
            'name' => 'receipt.txt',
            'content' => 'Thank you for your purchase!',
            'content_type' => 'text/plain'
        ]
    ];

    $emailService->sendEmail(
        'customer@example.com',
        'Invoice Attached',
        '<p>Please find your invoice attached.</p>',
        true,
        'billing@yourcompany.com',
        'Your Company Billing',
        [],
        [],
        $attachments
    );
    echo "<p>✅ Invoice email sent with attachments</p>";
} catch (Exception $e) {
    echo "<p>❌ Failed to send invoice email: " . $e->getMessage() . "</p>";
}

// Example 6: Laravel Integration
echo "<h2>Example 6: Laravel Integration</h2>";

echo "<h3>In your Laravel Controller:</h3>";
echo "<pre>";
echo htmlspecialchars('
use AgabaandreOffice365\ExchangeEmailService\ExchangeEmailService;

class EmailController extends Controller
{
    public function sendWelcomeEmail(Request $request)
    {
        $emailService = app(ExchangeEmailService::class);
        
        $emailService->sendTemplateEmail(
            $request->email,
            "Welcome to " . config("app.name") . "!",
            "welcome",
            [
                "name" => $request->name,
                "app_name" => config("app.name")
            ]
        );
        
        return response()->json(["message" => "Welcome email sent!"]);
    }
}
');
echo "</pre>";

echo "<h3>In your Laravel Job:</h3>";
echo "<pre>";
echo htmlspecialchars('
use AgabaandreOffice365\ExchangeEmailService\ExchangeEmailService;

class SendNewsletterJob implements ShouldQueue
{
    public function handle()
    {
        $emailService = new ExchangeEmailService();
        
        $subscribers = User::where("newsletter_subscribed", true)->get();
        
        foreach ($subscribers as $subscriber) {
            $emailService->sendTemplateEmail(
                $subscriber->email,
                "Weekly Newsletter",
                "newsletter",
                [
                    "name" => $subscriber->name,
                    "app_name" => config("app.name")
                ]
            );
        }
    }
}
');
echo "</pre>";

// Example 7: Error Handling
echo "<h2>Example 7: Error Handling</h2>";

try {
    $emailService->sendEmail(
        'invalid-email',
        'Test Subject',
        'Test Body'
    );
} catch (Exception $e) {
    echo "<p>❌ Caught expected error: " . $e->getMessage() . "</p>";
}

// Example 8: Testing Connection
echo "<h2>Example 8: Testing Connection</h2>";

$connectionTest = $emailService->testConnection();

echo "<h3>Connection Test Results:</h3>";
echo "<ul>";
echo "<li><strong>Configured:</strong> " . ($connectionTest['configured'] ? 'Yes' : 'No') . "</li>";
echo "<li><strong>Has Tokens:</strong> " . ($connectionTest['has_tokens'] ? 'Yes' : 'No') . "</li>";
echo "<li><strong>Status:</strong> " . ($connectionTest['status'] ?? 'Not Ready') . "</li>";
if (isset($connectionTest['error'])) {
    echo "<li><strong>Error:</strong> " . $connectionTest['error'] . "</li>";
}
echo "</ul>";

echo "<h2>🎉 Examples Complete!</h2>";
echo "<p>You now have a comprehensive understanding of how to use the SendMail ExchangeEmailService.</p>";
echo "<p>For more information, check the <a href='README.md'>README.md</a> file.</p>";

?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
h2, h3 { color: #007bff; }
h4 { color: #6c757d; }
ul { margin: 10px 0; }
li { margin: 5px 0; }
pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
</style>
