# Exchange Email Service

A simple, powerful PHP package for sending emails via Microsoft Graph API with OAuth 2.0 authentication. Works with any PHP application including Laravel, CodeIgniter, Yii, and vanilla PHP projects.

## Features

- ✅ **OAuth 2.0 Authentication** - Support for both Authorization Code and Client Credentials flows
- ✅ **File-based Token Storage** - No database required, tokens stored in JSON files
- ✅ **Automatic Token Refresh** - Built-in token management with automatic refresh
- ✅ **Framework Agnostic** - Works with Laravel, CodeIgniter, Yii, and vanilla PHP
- ✅ **Simple API** - Easy to use with minimal configuration
- ✅ **HTML & Text Support** - Send both HTML and plain text emails
- ✅ **Template Support** - Simple template rendering with variable substitution
- ✅ **CC/BCC Support** - Send emails with carbon copy and blind carbon copy
- ✅ **Error Handling** - Comprehensive error handling and logging
- ✅ **Debug Mode** - Built-in debugging and logging capabilities

## Installation

```bash
composer require agabaandre-office365/exchange-email-service
```

## Quick Start

### Vanilla PHP

```php
<?php
require_once 'vendor/autoload.php';

use AgabaandreOffice365\ExchangeEmailService\ExchangeEmailFactory;

// Quick setup
$emailService = ExchangeEmailFactory::quickSetup(
    'your-tenant-id',
    'your-client-id',
    'your-client-secret',
    'noreply@yourdomain.com',
    'Your App Name'
);

// Send email
$emailService->sendEmail(
    'recipient@example.com',
    'Hello World!',
    '<h1>Hello!</h1><p>This is a test email.</p>',
    true // HTML email
);
```

### Laravel

1. **Register the service provider** in `config/app.php`:

```php
'providers' => [
    // ... other providers
    AgabaandreOffice365\ExchangeEmailService\ExchangeEmailServiceProvider::class,
],
```

2. **Publish the configuration**:

```bash
php artisan vendor:publish --provider="AgabaandreOffice365\ExchangeEmailService\ExchangeEmailServiceProvider" --tag="config"
```

3. **Add to your `.env` file**:

```env
EXCHANGE_TENANT_ID=your-tenant-id
EXCHANGE_CLIENT_ID=your-client-id
EXCHANGE_CLIENT_SECRET=your-client-secret
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME=Your App Name
```

4. **Use in your application**:

```php
use AgabaandreOffice365\ExchangeEmailService\ExchangeEmailService;

class EmailController extends Controller
{
    public function sendEmail(ExchangeEmailService $emailService)
    {
        $emailService->sendEmail(
            'recipient@example.com',
            'Hello from Laravel!',
            '<h1>Hello!</h1><p>This email was sent from Laravel.</p>'
        );
    }
}
```

## Configuration

### Environment Variables

| Variable | Description | Default |
|----------|-------------|---------|
| `EXCHANGE_TENANT_ID` | Azure AD Tenant ID | Required |
| `EXCHANGE_CLIENT_ID` | Azure AD Application ID | Required |
| `EXCHANGE_CLIENT_SECRET` | Azure AD Application Secret | Required |
| `EXCHANGE_REDIRECT_URI` | OAuth Redirect URI | `http://localhost:8000/oauth/callback` |
| `EXCHANGE_SCOPE` | OAuth Scope | `https://graph.microsoft.com/Mail.Send` |
| `EXCHANGE_AUTH_METHOD` | Authentication Method | `client_credentials` |
| `MAIL_FROM_ADDRESS` | Default From Email | Required |
| `MAIL_FROM_NAME` | Default From Name | `Exchange Email Service` |

### Configuration Array

```php
$config = [
    'tenant_id' => 'your-tenant-id',
    'client_id' => 'your-client-id',
    'client_secret' => 'your-client-secret',
    'from_email' => 'noreply@yourdomain.com',
    'from_name' => 'Your App Name',
    'auth_method' => 'client_credentials', // or 'authorization_code'
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
```

## Usage Examples

### Basic Email Sending

```php
// Send HTML email
$emailService->sendHtmlEmail(
    'recipient@example.com',
    'HTML Email',
    '<h1>Hello!</h1><p>This is an HTML email.</p>'
);

// Send text email
$emailService->sendTextEmail(
    'recipient@example.com',
    'Text Email',
    'Hello! This is a plain text email.'
);

// Send email with CC and BCC
$emailService->sendEmail(
    'recipient@example.com',
    'Email with CC/BCC',
    '<p>This email has CC and BCC recipients.</p>',
    true,
    null, // from_email (uses default)
    null, // from_name (uses default)
    ['cc@example.com'], // CC
    ['bcc@example.com'] // BCC
);
```

### Template Emails

```php
$template = '<h1>Welcome {{name}}!</h1><p>Your order {{order_id}} is confirmed.</p>';
$data = [
    'name' => 'John Doe',
    'order_id' => '12345'
];

$emailService->sendTemplateEmail(
    'recipient@example.com',
    'Order Confirmation',
    $template,
    $data
);
```

### Token Management

```php
// Get token information
$tokenInfo = $emailService->getTokenInfo();
echo "Token expires in: " . $tokenInfo['expires_in'] . " seconds\n";

// Clear stored tokens (useful for logout)
$emailService->clearTokens();

// Check if service is configured
if ($emailService->isConfigured()) {
    echo "Email service is ready!";
} else {
    echo "Email service needs configuration.";
}
```

## Azure AD Setup

1. **Create an Azure AD Application**:
   - Go to Azure Portal > Azure Active Directory > App registrations
   - Click "New registration"
   - Enter application name and redirect URI
   - Note down the Application (client) ID and Directory (tenant) ID

2. **Create a Client Secret**:
   - Go to your app > Certificates & secrets
   - Click "New client secret"
   - Note down the secret value

3. **Grant Permissions**:
   - Go to your app > API permissions
   - Add permission > Microsoft Graph > Application permissions
   - Add "Mail.Send" permission
   - Click "Grant admin consent"

4. **Configure Redirect URI** (for Authorization Code flow):
   - Go to your app > Authentication
   - Add your redirect URI

## Authentication Methods

### Client Credentials Flow (Recommended for Server Applications)

```php
$config = [
    'tenant_id' => 'your-tenant-id',
    'client_id' => 'your-client-id',
    'client_secret' => 'your-client-secret',
    'auth_method' => 'client_credentials',
    'from_email' => 'noreply@yourdomain.com',
    'from_name' => 'Your App Name'
];
```

### Authorization Code Flow (For User-based Applications)

```php
$config = [
    'tenant_id' => 'your-tenant-id',
    'client_id' => 'your-client-id',
    'client_secret' => 'your-client-secret',
    'redirect_uri' => 'https://yourdomain.com/oauth/callback',
    'auth_method' => 'authorization_code',
    'from_email' => 'noreply@yourdomain.com',
    'from_name' => 'Your App Name'
];

// Get authorization URL
$authUrl = $emailService->getAuthorizationUrl();

// After user authorizes, exchange code for token
$emailService->exchangeCodeForToken($code, $state);
```

## Error Handling

```php
try {
    $result = $emailService->sendEmail(
        'recipient@example.com',
        'Test Email',
        '<p>This is a test email.</p>'
    );
    
    if ($result) {
        echo "Email sent successfully!";
    } else {
        echo "Failed to send email.";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

## Debugging

Enable debug mode to see detailed logs:

```php
$config = [
    // ... other config
    'defaults' => [
        'debug' => true,
    ]
];
```

## Requirements

- PHP 7.4 or higher
- cURL extension
- JSON extension
- Guzzle HTTP client (installed via Composer)

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).

## Support

For issues and questions, please open an issue on GitHub or contact the author.

## Changelog

### 1.0.0
- Initial release
- OAuth 2.0 authentication support
- File-based token storage
- Framework agnostic design
- Laravel integration
- Vanilla PHP support
