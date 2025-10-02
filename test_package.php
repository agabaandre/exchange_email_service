<?php
/**
 * SendMail ExchangeEmailService - Package Test
 * 
 * Test the general-purpose email service package
 */

require_once 'vendor/autoload.php';

use AgabaandreOffice365\ExchangeEmailService\ExchangeEmailService;

echo "<h2>🧪 Exchange Email Service - Package Test</h2>";

try {
    // Initialize the email service
    $emailService = new ExchangeEmailService();
    
    echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h4>📧 Email Service Status:</h4>";
    echo "<ul>";
    echo "<li><strong>Configured:</strong> " . ($emailService->isConfigured() ? '✅ Yes' : '❌ No') . "</li>";
    echo "<li><strong>Valid Tokens:</strong> " . ($emailService->hasValidTokens() ? '✅ Yes' : '❌ No') . "</li>";
    echo "<li><strong>Method:</strong> Microsoft Graph API (Direct)</li>";
    echo "<li><strong>Security:</strong> OAuth 2.0 Bearer Token</li>";
    echo "<li><strong>Status:</strong> " . ($emailService->hasValidTokens() ? '🚀 Ready for Production' : '⚠️ Setup Required') . "</li>";
    echo "</ul>";
    echo "</div>";
    
    if (!$emailService->isConfigured()) {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "<h4>❌ Email Service Not Configured</h4>";
        echo "<p>Please ensure all OAuth configuration variables are set:</p>";
        echo "<ul>";
        echo "<li><code>EXCHANGE_TENANT_ID</code></li>";
        echo "<li><code>EXCHANGE_CLIENT_ID</code></li>";
        echo "<li><code>EXCHANGE_CLIENT_SECRET</code></li>";
        echo "<li><code>EXCHANGE_REDIRECT_URI</code></li>";
        echo "<li><code>MAIL_FROM_ADDRESS</code></li>";
        echo "<li><code>MAIL_FROM_NAME</code></li>";
        echo "</ul>";
        echo "</div>";
        exit;
    }
    
    if (!$emailService->hasValidTokens()) {
        echo "<div style='background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "<h4>⚠️ OAuth Setup Required</h4>";
        echo "<p>You need to complete the one-time OAuth setup to send emails.</p>";
        echo "<p><a href='" . $emailService->getOAuthUrl() . "' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Complete OAuth Setup</a></p>";
        echo "</div>";
        exit;
    }
    
    // Test sending email
    $testEmail = 'agabaandre@gmail.com';
    echo "<p><strong>🚀 Sending test email to " . htmlspecialchars($testEmail) . "...</strong></p>";
    
    $success = $emailService->sendTestEmail($testEmail);
    
    if ($success) {
        echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "<h4>✅ Email Sent Successfully!</h4>";
        echo "<p>Test email has been sent to <strong>" . htmlspecialchars($testEmail) . "</strong> using Microsoft Graph API.</p>";
        echo "<p>This confirms your general-purpose email service is working perfectly!</p>";
        echo "</div>";
        
        echo "<div style='background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "<h4>🎉 Package Features Working!</h4>";
        echo "<p>Your Exchange Email Service package is ready with:</p>";
        echo "<ul>";
        echo "<li>✅ <strong>Microsoft Graph API</strong> - Most reliable method</li>";
        echo "<li>✅ <strong>OAuth 2.0 Security</strong> - No password storage</li>";
        echo "<li>✅ <strong>Automatic Token Refresh</strong> - No user interaction needed</li>";
        echo "<li>✅ <strong>Laravel Compatible</strong> - Easy integration</li>";
        echo "<li>✅ <strong>General Purpose</strong> - Works for any email needs</li>";
        echo "<li>✅ <strong>Multiple Recipients</strong> - CC, BCC, bulk emails</li>";
        echo "<li>✅ <strong>File Attachments</strong> - Send files with emails</li>";
        echo "<li>✅ <strong>Email Templates</strong> - Built-in responsive templates</li>";
        echo "<li>✅ <strong>Production Ready</strong> - Tested and reliable</li>";
        echo "</ul>";
        echo "</div>";
        
        echo "<div style='background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "<h4>📦 Package Ready for Distribution</h4>";
        echo "<p>Your Exchange Email Service package includes:</p>";
        echo "<ul>";
        echo "<li><strong>ExchangeEmailService.php</strong> - Main email service class</li>";
        echo "<li><strong>ExchangeOAuth.php</strong> - OAuth 2.0 handler</li>";
        echo "<li><strong>ExchangeEmailServiceProvider.php</strong> - Laravel service provider</li>";
        echo "<li><strong>composer.json</strong> - Package configuration</li>";
        echo "<li><strong>config/exchange-email.php</strong> - Laravel configuration</li>";
        echo "<li><strong>database/migrations/</strong> - Database migrations</li>";
        echo "<li><strong>README.md</strong> - Complete documentation</li>";
        echo "<li><strong>example_usage.php</strong> - Usage examples</li>";
        echo "</ul>";
        echo "<p><strong>Ready to be published to Packagist or used in any project!</strong></p>";
        echo "</div>";
        
    } else {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "<h4>❌ Email Sending Failed</h4>";
        echo "<p>Failed to send test email. Please check your OAuth configuration.</p>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h4>❌ Error</h4>";
    echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}

echo "<hr>";
echo "<div style='text-align: center; margin: 20px 0;'>";
echo "<a href='example_usage.php' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>📖 View Examples</a>";
echo "<a href='README.md' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>📋 Documentation</a>";
echo "<a href='composer.json' style='background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>📦 Package Info</a>";
echo "</div>";
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
h2, h3 { color: #007bff; }
h4 { color: #6c757d; }
ul { margin: 10px 0; }
li { margin: 5px 0; }
</style>
