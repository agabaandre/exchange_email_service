<?php

namespace SendMail\ExchangeEmailService;

/**
 * Exchange OAuth Handler
 * 
 * Handles OAuth 2.0 authentication with Microsoft Graph API
 * - Authorization Code Flow
 * - Token refresh
 * - Token storage
 * - Email sending via Graph API
 * 
 * @author SendMail ExchangeEmailService
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

    public function __construct($tenantId = null, $clientId = null, $clientSecret = null, $redirectUri = null, $scope = null)
    {
        $this->tenantId = $tenantId ?: getenv('EXCHANGE_TENANT_ID');
        $this->clientId = $clientId ?: getenv('EXCHANGE_CLIENT_ID');
        $this->clientSecret = $clientSecret ?: getenv('EXCHANGE_CLIENT_SECRET');
        $this->redirectUri = $redirectUri ?: getenv('EXCHANGE_REDIRECT_URI');
        $this->scope = $scope ?: getenv('EXCHANGE_SCOPE') ?: 'https://graph.microsoft.com/Mail.Send';
        
        $this->loadStoredTokens();
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
     * Check if we have valid tokens
     */
    public function hasValidToken()
    {
        return !empty($this->accessToken) && 
               $this->tokenExpiresAt && 
               time() < $this->tokenExpiresAt;
    }

    /**
     * Get authorization URL
     */
    public function getAuthorizationUrl()
    {
        if (!$this->isConfigured()) {
            throw new \Exception('OAuth not configured');
        }

        $state = bin2hex(random_bytes(16));
        $_SESSION['oauth_state'] = $state;

        $params = [
            'client_id' => $this->clientId,
            'response_type' => 'code',
            'redirect_uri' => $this->redirectUri,
            'scope' => $this->scope,
            'response_mode' => 'query',
            'state' => $state
        ];

        return 'https://login.microsoftonline.com/' . $this->tenantId . '/oauth2/v2.0/authorize?' . http_build_query($params);
    }

    /**
     * Exchange authorization code for tokens
     */
    public function exchangeCodeForToken($code, $state)
    {
        if (!$this->isConfigured()) {
            throw new \Exception('OAuth not configured');
        }

        // Verify state parameter
        if (!isset($_SESSION['oauth_state']) || $_SESSION['oauth_state'] !== $state) {
            throw new \Exception('Invalid state parameter');
        }

        $tokenUrl = 'https://login.microsoftonline.com/' . $this->tenantId . '/oauth2/v2.0/token';
        
        $data = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'code' => $code,
            'redirect_uri' => $this->redirectUri,
            'grant_type' => 'authorization_code',
            'scope' => $this->scope
        ];

        $response = $this->makeHttpRequest($tokenUrl, 'POST', $data);
        
        if (isset($response['access_token'])) {
            $this->accessToken = $response['access_token'];
            $this->refreshToken = $response['refresh_token'] ?? null;
            $this->tokenExpiresAt = time() + ($response['expires_in'] ?? 3600);
            
            $this->storeTokens();
            unset($_SESSION['oauth_state']);
            
            return true;
        }

        throw new \Exception('Failed to exchange code for token: ' . ($response['error_description'] ?? 'Unknown error'));
    }

    /**
     * Refresh access token
     */
    public function refreshAccessToken()
    {
        if (!$this->refreshToken) {
            return false;
        }

        $tokenUrl = 'https://login.microsoftonline.com/' . $this->tenantId . '/oauth2/v2.0/token';
        
        $data = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'refresh_token' => $this->refreshToken,
            'grant_type' => 'refresh_token',
            'scope' => $this->scope
        ];

        $response = $this->makeHttpRequest($tokenUrl, 'POST', $data);
        
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
     * Send email via Microsoft Graph API
     */
    public function sendEmail($to, $subject, $body, $isHtml = true, $fromEmail = null, $fromName = null, $cc = [], $bcc = [], $attachments = [])
    {
        if (!$this->hasValidToken()) {
            throw new \Exception('No valid access token');
        }

        $fromEmail = $fromEmail ?: getenv('MAIL_FROM_ADDRESS');
        $fromName = $fromName ?: getenv('MAIL_FROM_NAME');

        $emailData = [
            'message' => [
                'subject' => $subject,
                'body' => [
                    'contentType' => $isHtml ? 'HTML' : 'Text',
                    'content' => $body
                ],
                'toRecipients' => array_map(function($email) {
                    return ['emailAddress' => ['address' => $email]];
                }, is_array($to) ? $to : [$to]),
                'from' => [
                    'emailAddress' => [
                        'address' => $fromEmail,
                        'name' => $fromName
                    ]
                ]
            ]
        ];

        // Add CC recipients if provided
        if (!empty($cc)) {
            $emailData['message']['ccRecipients'] = array_map(function($email) {
                return ['emailAddress' => ['address' => $email]];
            }, $cc);
        }

        // Add BCC recipients if provided
        if (!empty($bcc)) {
            $emailData['message']['bccRecipients'] = array_map(function($email) {
                return ['emailAddress' => ['address' => $email]];
            }, $bcc);
        }

        // Add attachments if provided
        if (!empty($attachments)) {
            $emailData['message']['attachments'] = array_map(function($attachment) {
                return [
                    '@odata.type' => '#microsoft.graph.fileAttachment',
                    'name' => $attachment['name'],
                    'contentType' => $attachment['content_type'] ?? 'application/octet-stream',
                    'contentBytes' => base64_encode($attachment['content'])
                ];
            }, $attachments);
        }

        $url = 'https://graph.microsoft.com/v1.0/me/sendMail';
        $response = $this->makeHttpRequest($url, 'POST', $emailData, [
            'Authorization: Bearer ' . $this->accessToken
        ]);

        return !isset($response['error']);
    }

    /**
     * Load stored tokens from database
     */
    protected function loadStoredTokens()
    {
        try {
            // Try to load from database if available
            if (class_exists('PDO')) {
                $pdo = $this->getDatabaseConnection();
                if ($pdo) {
                    $stmt = $pdo->prepare("
                        SELECT access_token, refresh_token, expires_at 
                        FROM oauth_tokens 
                        WHERE service = 'exchange' AND client_id = ? 
                        ORDER BY created_at DESC 
                        LIMIT 1
                    ");
                    $stmt->execute([$this->clientId]);
                    $row = $stmt->fetch(\PDO::FETCH_ASSOC);
                    
                    if ($row) {
                        $this->accessToken = $row['access_token'];
                        $this->refreshToken = $row['refresh_token'];
                        $this->tokenExpiresAt = strtotime($row['expires_at']);
                    }
                }
            }
        } catch (\Exception $e) {
            // Ignore database errors, continue without stored tokens
        }
    }

    /**
     * Store tokens in database
     */
    protected function storeTokens()
    {
        try {
            $pdo = $this->getDatabaseConnection();
            if ($pdo) {
                // Create table if it doesn't exist
                $pdo->exec("
                    CREATE TABLE IF NOT EXISTS oauth_tokens (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        service VARCHAR(50) NOT NULL,
                        client_id VARCHAR(255) NOT NULL,
                        access_token TEXT NOT NULL,
                        refresh_token TEXT,
                        expires_at TIMESTAMP NOT NULL,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        INDEX idx_service (service),
                        INDEX idx_client (client_id)
                    )
                ");

                // Insert or update tokens
                $stmt = $pdo->prepare("
                    INSERT INTO oauth_tokens (service, client_id, access_token, refresh_token, expires_at) 
                    VALUES ('exchange', ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE 
                    access_token = VALUES(access_token),
                    refresh_token = VALUES(refresh_token),
                    expires_at = VALUES(expires_at)
                ");

                $stmt->execute([
                    $this->clientId,
                    $this->accessToken,
                    $this->refreshToken,
                    date('Y-m-d H:i:s', $this->tokenExpiresAt)
                ]);
            }
        } catch (\Exception $e) {
            // Ignore database errors, tokens will be lost on restart
        }
    }

    /**
     * Get database connection
     */
    protected function getDatabaseConnection()
    {
        try {
            $host = getenv('DB_HOST') ?: 'localhost';
            $dbname = getenv('DB_DATABASE') ?: 'exchange_email';
            $username = getenv('DB_USERNAME') ?: 'root';
            $password = getenv('DB_PASSWORD') ?: '';

            return new \PDO("mysql:host={$host};dbname={$dbname}", $username, $password);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Make HTTP request
     */
    protected function makeHttpRequest($url, $method = 'GET', $data = null, $headers = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($data) {
                if (is_array($data)) {
                    // For OAuth token requests, use form-encoded data
                    if (strpos($url, '/oauth2/v2.0/token') !== false) {
                        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
                        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
                    } else {
                        // For other requests, use JSON
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                        $headers[] = 'Content-Type: application/json';
                    }
                } else {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                }
            }
        }

        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
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
            throw new \Exception('HTTP error ' . $httpCode . ': ' . ($decodedResponse['error_description'] ?? $response));
        }

        return $decodedResponse ?: $response;
    }

    /**
     * Get access token
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * Get refresh token
     */
    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    /**
     * Get token expiration time
     */
    public function getTokenExpiresAt()
    {
        return $this->tokenExpiresAt;
    }
}
