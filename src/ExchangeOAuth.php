<?php

namespace AgabaandreOffice365\ExchangeEmailService;

/**
 * Exchange OAuth Handler
 * 
 * Handles OAuth 2.0 flows with Microsoft Graph API:
 * - Authorization Code Flow (user-based)
 * - Client Credentials Flow (application-based)
 * - Automatic token refresh
 * - File-based token storage
 * 
 * @author Andre Agaba
 * @version 1.0.0
 */
class ExchangeOAuth
{
    protected $tenantId;
    protected $clientId;
    protected $clientSecret;
    protected $redirectUri;
    protected $scope;
    protected $accessToken;
    protected $refreshToken;
    protected $tokenExpiresAt;
    protected $authMethod;
    protected $fromEmail;
    protected $fromName;
    protected $tokenFile;
    protected $config;

    // Supported authentication methods
    const AUTH_AUTHORIZATION_CODE = 'authorization_code';
    const AUTH_CLIENT_CREDENTIALS = 'client_credentials';

    public function __construct(array $config = [])
    {
        $this->config = $config;
        $this->tenantId = $config['tenant_id'] ?? getenv('EXCHANGE_TENANT_ID');
        $this->clientId = $config['client_id'] ?? getenv('EXCHANGE_CLIENT_ID');
        $this->clientSecret = $config['client_secret'] ?? getenv('EXCHANGE_CLIENT_SECRET');
        $this->redirectUri = $config['redirect_uri'] ?? getenv('EXCHANGE_REDIRECT_URI');
        $this->scope = $config['scope'] ?? getenv('EXCHANGE_SCOPE') ?: 'https://graph.microsoft.com/Mail.Send';
        $this->authMethod = $config['auth_method'] ?? getenv('EXCHANGE_AUTH_METHOD') ?: self::AUTH_AUTHORIZATION_CODE;
        $this->fromEmail = $config['from_email'] ?? getenv('MAIL_FROM_ADDRESS');
        $this->fromName = $config['from_name'] ?? getenv('MAIL_FROM_NAME');
        
        // Set token file path
        $tokenPath = $config['token_storage']['path'] ?? 'tokens/oauth_tokens.json';
        $this->tokenFile = $this->resolveTokenPath($tokenPath);
        
        $this->loadStoredTokens();
    }

    /**
     * Resolve token file path
     */
    protected function resolveTokenPath($path)
    {
        // If absolute path, use as is
        if (strpos($path, '/') === 0) {
            return $path;
        }
        
        // If relative path, try to resolve to a writable location
        $resolvedPath = $this->findWritablePath($path);
        
        return $resolvedPath;
    }
    
    /**
     * Find a writable path for token storage
     */
    protected function findWritablePath($relativePath)
    {
        $candidates = [
            // Try project root (outside vendor)
            getcwd() . '/' . ltrim($relativePath, '/'),
            // Try system temp directory
            sys_get_temp_dir() . '/exchange-email-tokens/' . basename($relativePath),
            // Try user home directory
            (getenv('HOME') ?: getenv('USERPROFILE')) . '/.exchange-email-tokens/' . basename($relativePath),
            // Try current directory as fallback
            './' . ltrim($relativePath, '/')
        ];
        
        foreach ($candidates as $candidate) {
            $dir = dirname($candidate);
            
            // Create directory if it doesn't exist
            if (!is_dir($dir)) {
                try {
                    mkdir($dir, 0755, true);
                } catch (\Exception $e) {
                    continue;
                }
            }
            
            // Check if directory is writable
            if (is_writable($dir)) {
                return $candidate;
            }
        }
        
        // Fallback to current working directory (original behavior)
        return getcwd() . '/' . ltrim($relativePath, '/');
    }

    /**
     * Check if OAuth is configured
     */
    public function isConfigured()
    {
        return !empty($this->tenantId) && 
               !empty($this->clientId) && 
               !empty($this->clientSecret);
    }

    /**
     * Get authorization URL for Authorization Code Flow
     */
    public function getAuthorizationUrl($state = null)
    {
        if ($this->authMethod !== self::AUTH_AUTHORIZATION_CODE) {
            throw new \Exception('Authorization URL only available for authorization_code flow');
        }

        $state = $state ?: bin2hex(random_bytes(16));
        $_SESSION['oauth_state'] = $state;

        $params = [
            'client_id' => $this->clientId,
            'response_type' => 'code',
            'redirect_uri' => $this->redirectUri,
            'scope' => $this->scope,
            'state' => $state,
            'response_mode' => 'query'
        ];

        return 'https://login.microsoftonline.com/' . $this->tenantId . '/oauth2/v2.0/authorize?' . http_build_query($params);
    }

    /**
     * Exchange authorization code for tokens
     */
    public function exchangeCodeForToken($code, $state = null)
    {
        if ($this->authMethod !== self::AUTH_AUTHORIZATION_CODE) {
            throw new \Exception('Code exchange only available for authorization_code flow');
        }

        // Verify state
        if ($state && (!isset($_SESSION['oauth_state']) || $_SESSION['oauth_state'] !== $state)) {
            throw new \Exception('Invalid state parameter');
        }

        $url = 'https://login.microsoftonline.com/' . $this->tenantId . '/oauth2/v2.0/token';
        $data = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'code' => $code,
            'redirect_uri' => $this->redirectUri,
            'grant_type' => 'authorization_code'
        ];

        $response = $this->makeHttpRequest($url, 'POST', $data);

        if (isset($response['access_token'])) {
            $this->accessToken = $response['access_token'];
            $this->refreshToken = $response['refresh_token'] ?? null;
            $this->tokenExpiresAt = time() + ($response['expires_in'] ?? 3600);
            $this->storeTokens();
            unset($_SESSION['oauth_state']);
            
            return true;
        }

        $errorDescription = $response['error_description'] ?? null;
        $error = $response['error'] ?? null;

        $errorMsg = 'Unknown error';
        if (is_string($errorDescription)) {
            $errorMsg = $errorDescription;
        } elseif (is_array($errorDescription)) {
            $errorMsg = json_encode($errorDescription);
        } elseif (is_string($error)) {
            $errorMsg = $error;
        } elseif (is_array($error)) {
            $errorMsg = json_encode($error);
        }
        throw new \Exception('Failed to exchange code for token: ' . $errorMsg);
    }

    /**
     * Get access token using Client Credentials Flow
     */
    public function getClientCredentialsToken()
    {
        if ($this->authMethod !== self::AUTH_CLIENT_CREDENTIALS) {
            throw new \Exception('Client credentials only available for client_credentials flow');
        }

        $url = 'https://login.microsoftonline.com/' . $this->tenantId . '/oauth2/v2.0/token';
        $data = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'scope' => $this->scope,
            'grant_type' => 'client_credentials'
        ];

        $response = $this->makeHttpRequest($url, 'POST', $data);

        if (isset($response['access_token'])) {
            $this->accessToken = $response['access_token'];
            $this->refreshToken = null; // Client credentials don't have refresh tokens
            $this->tokenExpiresAt = time() + ($response['expires_in'] ?? 3600);
            $this->storeTokens();
            return true;
        }

        $errorDescription = $response['error_description'] ?? null;
        $error = $response['error'] ?? null;

        $errorMsg = 'Unknown error';
        if (is_string($errorDescription)) {
            $errorMsg = $errorDescription;
        } elseif (is_array($errorDescription)) {
            $errorMsg = json_encode($errorDescription);
        } elseif (is_string($error)) {
            $errorMsg = $error;
        } elseif (is_array($error)) {
            $errorMsg = json_encode($error);
        }
        throw new \Exception('Failed to get client credentials token: ' . $errorMsg);
    }

    /**
     * Refresh access token
     */
    public function refreshAccessToken()
    {
        // For client credentials, get a new token
        if ($this->authMethod === self::AUTH_CLIENT_CREDENTIALS) {
            return $this->getClientCredentialsToken();
        }

        if (!$this->refreshToken) {
            return false;
        }

        $url = 'https://login.microsoftonline.com/' . $this->tenantId . '/oauth2/v2.0/token';
        $data = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'refresh_token' => $this->refreshToken,
            'grant_type' => 'refresh_token'
        ];

        $response = $this->makeHttpRequest($url, 'POST', $data);

        if (isset($response['access_token'])) {
            $this->accessToken = $response['access_token'];
            $this->refreshToken = $response['refresh_token'] ?? $this->refreshToken;
            $this->tokenExpiresAt = time() + ($response['expires_in'] ?? 3600);
            $this->storeTokens();
            return true;
        }

        return false;
    }

    /**
     * Get access token with automatic refresh
     */
    public function getAccessToken()
    {
        // Check if token needs refresh (5 minutes buffer)
        if ($this->accessToken && $this->tokenExpiresAt && time() < ($this->tokenExpiresAt - 300)) {
            return $this->accessToken;
        }

        // Try to refresh token
        if ($this->refreshAccessToken()) {
            return $this->accessToken;
        }

        // If client credentials, try to get new token
        if ($this->authMethod === self::AUTH_CLIENT_CREDENTIALS) {
            if ($this->getClientCredentialsToken()) {
                return $this->accessToken;
            }
        }

        throw new \Exception('Unable to obtain valid access token');
    }

    /**
     * Send email via Microsoft Graph API
     */
    public function sendEmail($to, $subject, $body, $isHtml = true, $fromEmail = null, $fromName = null, $cc = [], $bcc = [], $attachments = [])
    {
        // Get valid access token
        $accessToken = $this->getAccessToken();

        $fromEmail = $fromEmail ?: $this->fromEmail;
        $fromName = $fromName ?: $this->fromName;

        if (!$fromEmail) {
            throw new \Exception('From email address is required');
        }

        // Prepare email data
        $emailData = [
            'message' => [
                'subject' => $subject,
                'body' => [
                    'contentType' => $isHtml ? 'HTML' : 'Text',
                    'content' => $body
                ],
                'toRecipients' => array_map(function($email) {
                    return ['emailAddress' => ['address' => $email]];
                }, (array)$to),
                'from' => [
                    'emailAddress' => [
                        'address' => $fromEmail,
                        'name' => $fromName
                    ]
                ]
            ]
        ];

        // Add CC recipients
        if (!empty($cc)) {
            $emailData['message']['ccRecipients'] = array_map(function($email) {
                return ['emailAddress' => ['address' => $email]];
            }, (array)$cc);
        }

        // Add BCC recipients
        if (!empty($bcc)) {
            $emailData['message']['bccRecipients'] = array_map(function($email) {
                return ['emailAddress' => ['address' => $email]];
            }, (array)$bcc);
        }

        // Add attachments
        if (!empty($attachments)) {
            $emailData['message']['attachments'] = $attachments;
        }

        $url = 'https://graph.microsoft.com/v1.0/me/sendMail';
        $headers = [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json'
        ];

        $response = $this->makeHttpRequest($url, 'POST', $emailData, $headers);

        return $response !== false;
    }

    /**
     * Load stored tokens from file
     */
    protected function loadStoredTokens()
    {
        try {
            if (file_exists($this->tokenFile)) {
                $tokenData = json_decode(file_get_contents($this->tokenFile), true);
                
                if ($tokenData && isset($tokenData[$this->clientId])) {
                    $tokens = $tokenData[$this->clientId];
                    $this->accessToken = $tokens['access_token'] ?? null;
                    $this->refreshToken = $tokens['refresh_token'] ?? null;
                    $this->tokenExpiresAt = $tokens['expires_at'] ?? null;
                    $this->authMethod = $tokens['auth_method'] ?? $this->authMethod;
                }
            }
        } catch (\Exception $e) {
            // Ignore file errors, continue without stored tokens
        }
    }

    /**
     * Store tokens in file
     */
    protected function storeTokens()
    {
        try {
            // Create tokens directory if it doesn't exist
            $tokenDir = dirname($this->tokenFile);
            if (!is_dir($tokenDir)) {
                mkdir($tokenDir, 0755, true);
            }
            
            // Load existing tokens
            $tokenData = [];
            if (file_exists($this->tokenFile)) {
                $tokenData = json_decode(file_get_contents($this->tokenFile), true) ?: [];
            }
            
            // Update tokens for this client
            $tokenData[$this->clientId] = [
                'access_token' => $this->accessToken,
                'refresh_token' => $this->refreshToken,
                'expires_at' => $this->tokenExpiresAt,
                'auth_method' => $this->authMethod,
                'updated_at' => time()
            ];
            
            // Save to file
            file_put_contents($this->tokenFile, json_encode($tokenData, JSON_PRETTY_PRINT));
            
        } catch (\Exception $e) {
            // Ignore file errors
        }
    }

    /**
     * Make HTTP request with enhanced error handling
     */
    protected function makeHttpRequest($url, $method = 'GET', $data = null, $headers = [])
    {
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ]);

        if ($data) {
            if (in_array('Content-Type: application/json', $headers)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            }
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new \Exception('cURL error: ' . $error);
        }

        $decodedResponse = json_decode($response, true);

        if ($httpCode >= 400) {
            $errorDescription = $decodedResponse['error_description'] ?? null;
            $error = $decodedResponse['error'] ?? null;

            $errorMessage = 'HTTP ' . $httpCode;
            if (is_string($errorDescription)) {
                $errorMessage = $errorDescription;
            } elseif (is_array($errorDescription)) {
                $errorMessage = json_encode($errorDescription);
            } elseif (is_string($error)) {
                $errorMessage = $error;
            } elseif (is_array($error)) {
                $errorMessage = json_encode($error);
            }
            
            throw new \Exception('API error: ' . $errorMessage);
        }

        return $decodedResponse ?: $response;
    }

    /**
     * Clear stored tokens
     */
    public function clearTokens()
    {
        $this->accessToken = null;
        $this->refreshToken = null;
        $this->tokenExpiresAt = null;
        
        try {
            if (file_exists($this->tokenFile)) {
                $tokenData = json_decode(file_get_contents($this->tokenFile), true) ?: [];
                unset($tokenData[$this->clientId]);
                file_put_contents($this->tokenFile, json_encode($tokenData, JSON_PRETTY_PRINT));
            }
        } catch (\Exception $e) {
            // Ignore file errors
        }
    }

    /**
     * Get token information
     */
    public function getTokenInfo()
    {
        return [
            'has_access_token' => !empty($this->accessToken),
            'has_refresh_token' => !empty($this->refreshToken),
            'expires_at' => $this->tokenExpiresAt,
            'expires_in' => $this->tokenExpiresAt ? max(0, $this->tokenExpiresAt - time()) : 0,
            'auth_method' => $this->authMethod,
            'is_expired' => $this->tokenExpiresAt ? time() >= $this->tokenExpiresAt : true
        ];
    }
}
