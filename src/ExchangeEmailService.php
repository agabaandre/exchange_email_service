<?php

namespace AgabaandreOffice365\ExchangeEmailService;

/**
 * Exchange Email Service
 * 
 * Main service class for sending emails via Microsoft Graph API
 * with OAuth 2.0 authentication and file-based token storage.
 * 
 * @author Andre Agaba
 * @version 1.0.0
 */
class ExchangeEmailService
{
    protected $oauth;
    protected $config;
    protected $isConfigured;

    public function __construct(array $config = [])
    {
        $this->config = $this->mergeWithDefaults($config);
        $this->oauth = new ExchangeOAuth($this->config);
        $this->isConfigured = $this->oauth->isConfigured();
    }

    /**
     * Merge configuration with defaults
     */
    protected function mergeWithDefaults(array $config)
    {
        $defaults = [
            'tenant_id' => '',
            'client_id' => '',
            'client_secret' => '',
            'redirect_uri' => '',
            'scope' => 'https://graph.microsoft.com/Mail.Send',
            'auth_method' => 'client_credentials',
            'from_email' => '',
            'from_name' => 'Exchange Email Service',
            'token_storage' => [
                'type' => 'file',
                'path' => 'tokens/oauth_tokens.json',
                'permissions' => 0644,
            ],
            'defaults' => [
                'is_html' => true,
                'timeout' => 30,
                'retry_attempts' => 3,
                'debug' => false,
            ]
        ];

        return array_merge($defaults, $config);
    }

    /**
     * Check if service is configured
     */
    public function isConfigured()
    {
        return $this->isConfigured;
    }

    /**
     * Send email
     */
    public function sendEmail($to, $subject, $body, $isHtml = null, $fromEmail = null, $fromName = null, $cc = [], $bcc = [], $attachments = [])
    {
        if (!$this->isConfigured) {
            throw new \Exception('Exchange Email Service is not configured. Please provide tenant_id, client_id, and client_secret.');
        }

        $isHtml = $isHtml ?? $this->config['defaults']['is_html'];
        $fromEmail = $fromEmail ?: $this->config['from_email'];
        $fromName = $fromName ?: $this->config['from_name'];

        try {
            $result = $this->oauth->sendEmail($to, $subject, $body, $isHtml, $fromEmail, $fromName, $cc, $bcc, $attachments);
            
            if ($this->config['defaults']['debug']) {
                error_log("Exchange email sent successfully to: " . (is_array($to) ? implode(', ', $to) : $to));
            }
            
            return $result;
        } catch (\Exception $e) {
            if ($this->config['defaults']['debug']) {
                error_log("Exchange email error: " . $e->getMessage());
            }
            throw $e;
        }
    }

    /**
     * Send HTML email
     */
    public function sendHtmlEmail($to, $subject, $htmlBody, $fromEmail = null, $fromName = null, $cc = [], $bcc = [], $attachments = [])
    {
        return $this->sendEmail($to, $subject, $htmlBody, true, $fromEmail, $fromName, $cc, $bcc, $attachments);
    }

    /**
     * Send text email
     */
    public function sendTextEmail($to, $subject, $textBody, $fromEmail = null, $fromName = null, $cc = [], $bcc = [], $attachments = [])
    {
        return $this->sendEmail($to, $subject, $textBody, false, $fromEmail, $fromName, $cc, $bcc, $attachments);
    }

    /**
     * Send email with template
     */
    public function sendTemplateEmail($to, $subject, $template, $data = [], $fromEmail = null, $fromName = null, $cc = [], $bcc = [], $attachments = [])
    {
        $htmlBody = $this->renderTemplate($template, $data);
        return $this->sendHtmlEmail($to, $subject, $htmlBody, $fromEmail, $fromName, $cc, $bcc, $attachments);
    }

    /**
     * Render email template
     */
    protected function renderTemplate($template, $data = [])
    {
        // Simple template rendering - can be extended
        $html = $template;
        
        foreach ($data as $key => $value) {
            $html = str_replace('{{' . $key . '}}', $value, $html);
        }
        
        return $html;
    }

    /**
     * Get OAuth handler
     */
    public function getOAuth()
    {
        return $this->oauth;
    }

    /**
     * Get authorization URL (for authorization_code flow)
     */
    public function getAuthorizationUrl($state = null)
    {
        return $this->oauth->getAuthorizationUrl($state);
    }

    /**
     * Exchange authorization code for tokens (for authorization_code flow)
     */
    public function exchangeCodeForToken($code, $state = null)
    {
        return $this->oauth->exchangeCodeForToken($code, $state);
    }

    /**
     * Get token information
     */
    public function getTokenInfo()
    {
        return $this->oauth->getTokenInfo();
    }

    /**
     * Clear stored tokens
     */
    public function clearTokens()
    {
        return $this->oauth->clearTokens();
    }

    /**
     * Get configuration
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Update configuration
     */
    public function updateConfig(array $config)
    {
        $this->config = $this->mergeWithDefaults(array_merge($this->config, $config));
        $this->oauth = new ExchangeOAuth($this->config);
        $this->isConfigured = $this->oauth->isConfigured();
    }
}
