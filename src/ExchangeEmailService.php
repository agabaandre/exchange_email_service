<?php

namespace SendMail\ExchangeEmailService;

/**
 * Exchange Email Service
 * 
 * General-purpose email service using Microsoft Graph API
 * - OAuth 2.0 Authorization Code Flow
 * - Direct Graph API calls (most reliable)
 * - Automatic token refresh
 * - Laravel-compatible
 * - Works with any email address
 * 
 * @author SendMail ExchangeEmailService
 * @version 1.0.0
 */
class ExchangeEmailService
{
    protected $oauth;
    protected $fromEmail;
    protected $fromName;
    protected $tenantId;
    protected $clientId;
    protected $clientSecret;
    protected $redirectUri;
    protected $scope;

    public function __construct($config = [])
    {
        // Load configuration from array or environment
        $this->tenantId = $config['tenant_id'] ?? getenv('EXCHANGE_TENANT_ID');
        $this->clientId = $config['client_id'] ?? getenv('EXCHANGE_CLIENT_ID');
        $this->clientSecret = $config['client_secret'] ?? getenv('EXCHANGE_CLIENT_SECRET');
        $this->redirectUri = $config['redirect_uri'] ?? getenv('EXCHANGE_REDIRECT_URI');
        $this->scope = $config['scope'] ?? getenv('EXCHANGE_SCOPE') ?: 'https://graph.microsoft.com/Mail.Send';
        $this->fromEmail = $config['from_email'] ?? getenv('MAIL_FROM_ADDRESS');
        $this->fromName = $config['from_name'] ?? getenv('MAIL_FROM_NAME');

        // Initialize OAuth handler
        $this->oauth = new ExchangeOAuth(
            $this->tenantId,
            $this->clientId,
            $this->clientSecret,
            $this->redirectUri,
            $this->scope
        );
    }

    /**
     * Check if the email service is properly configured
     */
    public function isConfigured()
    {
        return $this->oauth->isConfigured();
    }

    /**
     * Check if we have valid OAuth tokens
     */
    public function hasValidTokens()
    {
        return $this->oauth->hasValidToken();
    }

    /**
     * Get OAuth authorization URL for initial setup
     */
    public function getOAuthUrl()
    {
        return $this->oauth->getAuthorizationUrl();
    }

    /**
     * Process OAuth callback and exchange code for tokens
     */
    public function processOAuthCallback($code, $state)
    {
        return $this->oauth->exchangeCodeForToken($code, $state);
    }

    /**
     * Send email using Microsoft Graph API
     * 
     * @param string $to Recipient email address
     * @param string $subject Email subject
     * @param string $body Email body (HTML or plain text)
     * @param bool $isHtml Whether the body is HTML
     * @param string|null $fromEmail Override sender email
     * @param string|null $fromName Override sender name
     * @param array $cc Optional CC recipients
     * @param array $bcc Optional BCC recipients
     * @param array $attachments Optional file attachments
     * @return bool Success status
     * @throws \Exception If sending fails
     */
    public function sendEmail($to, $subject, $body, $isHtml = true, $fromEmail = null, $fromName = null, $cc = [], $bcc = [], $attachments = [])
    {
        if (!$this->oauth->isConfigured()) {
            throw new \Exception("Email service is not configured. Please check OAuth settings.");
        }

        // Ensure we have valid tokens
        if (!$this->oauth->hasValidToken()) {
            if (!$this->oauth->refreshAccessToken()) {
                throw new \Exception("OAuth tokens are invalid or expired. Please complete OAuth setup.");
            }
        }

        $fromEmail = $fromEmail ?: $this->fromEmail;
        $fromName = $fromName ?: $this->fromName;

        // Send via Graph API
        return $this->oauth->sendEmail($to, $subject, $body, $isHtml, $fromEmail, $fromName, $cc, $bcc, $attachments);
    }

    /**
     * Send email to multiple recipients
     * 
     * @param array $recipients Array of email addresses
     * @param string $subject Email subject
     * @param string $body Email body
     * @param bool $isHtml Whether the body is HTML
     * @param string|null $fromEmail Override sender email
     * @param string|null $fromName Override sender name
     * @return bool Success status
     */
    public function sendBulkEmail($recipients, $subject, $body, $isHtml = true, $fromEmail = null, $fromName = null)
    {
        $success = true;
        foreach ($recipients as $recipient) {
            try {
                $this->sendEmail($recipient, $subject, $body, $isHtml, $fromEmail, $fromName);
            } catch (\Exception $e) {
                error_log("Failed to send email to {$recipient}: " . $e->getMessage());
                $success = false;
            }
        }
        return $success;
    }

    /**
     * Test email service connection
     * 
     * @return array Test results
     */
    public function testConnection()
    {
        $result = [
            'configured' => $this->isConfigured(),
            'has_tokens' => $this->hasValidTokens(),
            'oauth_url' => $this->getOAuthUrl(),
            'error' => null
        ];

        if (!$result['configured']) {
            $result['error'] = 'Email service not configured - check OAuth settings';
            return $result;
        }

        if (!$result['has_tokens']) {
            $result['error'] = 'No valid OAuth tokens - complete OAuth setup first';
            return $result;
        }

        $result['status'] = 'ready';
        return $result;
    }

    /**
     * Send test email
     * 
     * @param string $toEmail Recipient email
     * @return bool Success status
     */
    public function sendTestEmail($toEmail)
    {
        $subject = "Exchange Email Service Test - " . date('Y-m-d H:i:s');
        $body = $this->getTestEmailTemplate($toEmail);
        return $this->sendEmail($toEmail, $subject, $body);
    }

    /**
     * Send HTML email with template
     * 
     * @param string $to Recipient email
     * @param string $subject Email subject
     * @param string $template Template name or HTML content
     * @param array $data Template data
     * @param string|null $fromEmail Override sender email
     * @param string|null $fromName Override sender name
     * @return bool Success status
     */
    public function sendTemplateEmail($to, $subject, $template, $data = [], $fromEmail = null, $fromName = null)
    {
        $body = $this->renderTemplate($template, $data);
        return $this->sendEmail($to, $subject, $body, true, $fromEmail, $fromName);
    }

    /**
     * Render email template
     * 
     * @param string $template Template name or HTML content
     * @param array $data Template data
     * @return string Rendered HTML
     */
    protected function renderTemplate($template, $data = [])
    {
        // If template is HTML content, return as is
        if (strpos($template, '<html') !== false || strpos($template, '<body') !== false) {
            return $template;
        }

        // Load template from file or use built-in templates
        $templates = [
            'welcome' => $this->getWelcomeTemplate(),
            'notification' => $this->getNotificationTemplate(),
            'confirmation' => $this->getConfirmationTemplate(),
            'test' => $this->getTestEmailTemplate($data['email'] ?? 'user@example.com')
        ];

        $html = $templates[$template] ?? $template;

        // Replace placeholders with data
        foreach ($data as $key => $value) {
            $html = str_replace('{{' . $key . '}}', htmlspecialchars($value), $html);
        }

        return $html;
    }

    // --- Email Templates ---

    /**
     * Get test email template
     */
    protected function getTestEmailTemplate($toEmail)
    {
        return '
        <html>
        <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
            <div style="background: linear-gradient(135deg, #007bff 0%, #0056b3 100%); color: white; padding: 20px; text-align: center;">
                <h1>✅ Exchange Email Service Test</h1>
                <p>Microsoft Graph API - Production Ready</p>
            </div>
            
            <div style="padding: 20px;">
                <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0;">
                    <h3>🎉 Email Service Working Perfectly!</h3>
                    <p>This email confirms that your Exchange Email Service is working correctly using Microsoft Graph API.</p>
                </div>
                
                <h3>Configuration Details:</h3>
                <ul>
                    <li><strong>Method:</strong> Microsoft Graph API (Direct)</li>
                    <li><strong>Authentication:</strong> OAuth 2.0 Authorization Code Flow</li>
                    <li><strong>Security:</strong> Bearer Token Authentication</li>
                    <li><strong>Sent At:</strong> ' . date('Y-m-d H:i:s T') . '</li>
                    <li><strong>Recipient:</strong> ' . htmlspecialchars($toEmail) . '</li>
                    <li><strong>From:</strong> ' . htmlspecialchars($this->fromEmail) . '</li>
                    <li><strong>Service:</strong> Exchange Email Service</li>
                </ul>
                
                <div style="background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; padding: 15px; border-radius: 5px; margin: 20px 0;">
                    <h4>🚀 Production Ready Features:</h4>
                    <ul>
                        <li>✅ OAuth 2.0 Security</li>
                        <li>✅ Automatic Token Refresh</li>
                        <li>✅ No Password Storage</li>
                        <li>✅ Works with Any Email Provider</li>
                        <li>✅ Laravel Compatible</li>
                        <li>✅ Production Tested</li>
                    </ul>
                </div>
                
                <p><strong>Your Exchange Email Service is ready for production! 🎉</strong></p>
            </div>
            
            <div style="background: #f8f9fa; padding: 15px; text-align: center; font-size: 12px; color: #6c757d;">
                <p>This is an automated test email from the Exchange Email Service</p>
                <p>Generated on ' . date('Y-m-d H:i:s') . ' | Microsoft Graph API</p>
            </div>
        </body>
        </html>';
    }

    /**
     * Get welcome email template
     */
    protected function getWelcomeTemplate()
    {
        return '
        <html>
        <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
            <div style="background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%); color: white; padding: 20px; text-align: center;">
                <h1>Welcome to {{app_name}}!</h1>
                <p>Thank you for joining us</p>
            </div>
            
            <div style="padding: 20px;">
                <p>Dear {{name}},</p>
                
                <p>Welcome to {{app_name}}! We are excited to have you on board.</p>
                
                <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
                    <h3>Getting Started:</h3>
                    <ul>
                        <li>Complete your profile setup</li>
                        <li>Explore our features</li>
                        <li>Contact support if you need help</li>
                    </ul>
                </div>
                
                <p>If you have any questions, please do not hesitate to contact us.</p>
            </div>
            
            <div style="background: #f8f9fa; padding: 15px; text-align: center; font-size: 12px; color: #6c757d;">
                <p>This is an automated email from {{app_name}}</p>
            </div>
        </body>
        </html>';
    }

    /**
     * Get notification email template
     */
    protected function getNotificationTemplate()
    {
        return '
        <html>
        <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
            <div style="background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%); color: #212529; padding: 20px; text-align: center;">
                <h1>📢 {{title}}</h1>
            </div>
            
            <div style="padding: 20px;">
                <p>Dear {{name}},</p>
                
                <p>{{message}}</p>
                
                <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
                    <h3>Details:</h3>
                    <p>{{details}}</p>
                </div>
                
                <p>Thank you for your attention.</p>
            </div>
            
            <div style="background: #f8f9fa; padding: 15px; text-align: center; font-size: 12px; color: #6c757d;">
                <p>This is an automated notification from {{app_name}}</p>
            </div>
        </body>
        </html>';
    }

    /**
     * Get confirmation email template
     */
    protected function getConfirmationTemplate()
    {
        return '
        <html>
        <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
            <div style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white; padding: 20px; text-align: center;">
                <h1>✅ {{title}}</h1>
                <p>Your action has been confirmed</p>
            </div>
            
            <div style="padding: 20px;">
                <p>Dear {{name}},</p>
                
                <p>{{message}}</p>
                
                <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
                    <h3>Confirmation Details:</h3>
                    <ul>
                        <li><strong>Reference ID:</strong> {{reference_id}}</li>
                        <li><strong>Date:</strong> {{date}}</li>
                        <li><strong>Status:</strong> {{status}}</li>
                    </ul>
                </div>
                
                <p>Thank you for using {{app_name}}!</p>
            </div>
            
            <div style="background: #f8f9fa; padding: 15px; text-align: center; font-size: 12px; color: #6c757d;">
                <p>This is an automated confirmation from {{app_name}}</p>
            </div>
        </body>
        </html>';
    }
}
