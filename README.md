# Exchange Email Service

[![Latest Version](https://img.shields.io/packagist/v/agabaandre-office365/exchange-email-service.svg)](https://packagist.org/packages/agabaandre-office365/exchange-email-service)
[![License](https://img.shields.io/packagist/l/agabaandre-office365/exchange-email-service.svg)](https://packagist.org/packages/agabaandre-office365/exchange-email-service)
[![PHP Version](https://img.shields.io/packagist/php-v/agabaandre-office365/exchange-email-service.svg)](https://packagist.org/packages/agabaandre-office365/exchange-email-service)
[![Total Downloads](https://img.shields.io/packagist/dt/agabaandre-office365/exchange-email-service.svg)](https://packagist.org/packages/agabaandre-office365/exchange-email-service)

A powerful, framework-agnostic PHP package for sending emails via Microsoft Graph API with OAuth 2.0 authentication. Works seamlessly with Laravel, CodeIgniter, Yii, and vanilla PHP projects.

## ✨ Features

- 🚀 **Microsoft Graph API** - Most reliable email delivery method
- 🔐 **OAuth 2.0 Security** - No password storage required
- 🔄 **Automatic Token Refresh** - Seamless token management
- 🏗️ **Framework Agnostic** - Works with any PHP framework
- 📧 **Rich Email Support** - HTML, text, templates, attachments
- 🎯 **Laravel Integration** - Service provider with auto-discovery
- 📁 **File-based Storage** - No database required for tokens
- 🛠️ **Easy Configuration** - Simple setup with environment variables
- 🐛 **Debug Mode** - Comprehensive logging and error handling
- 📦 **Production Ready** - Tested and optimized for production use

## 📦 Installation

### Via Composer

```bash
composer require agabaandre-office365/exchange-email-service
```

### Manual Installation

1. Download the package
2. Extract to your project directory
3. Run `composer install`

## 🚀 Quick Start

### Vanilla PHP

```php
<?php
require_once 'vendor/autoload.php';

use AgabaandreOffice365\ExchangeEmailService\ExchangeEmailService;

// Quick setup with configuration
$emailService = new ExchangeEmailService([
    'tenant_id' => 'your-tenant-id',
    'client_id' => 'your-client-id',
    'client_secret' => 'your-client-secret',
    'from_email' => 'noreply@yourdomain.com',
    'from_name' => 'Your App Name'
]);

// Send email
$emailService->sendEmail(
    'recipient@example.com',
    'Hello World!',
    '<h1>Hello!</h1><p>This is a test email.</p>',
    true // HTML email
);
```

### Laravel Integration

1. **Install the package:**
   ```bash
   composer require agabaandre-office365/exchange-email-service
   ```

2. **Publish configuration:**
   ```bash
   php artisan vendor:publish --provider="AgabaandreOffice365\ExchangeEmailService\ExchangeEmailServiceProvider"
   ```

3. **Configure environment variables in `.env`:**
   ```env
   EXCHANGE_TENANT_ID=your-tenant-id
   EXCHANGE_CLIENT_ID=your-client-id
   EXCHANGE_CLIENT_SECRET=your-client-secret
   MAIL_FROM_ADDRESS=noreply@yourdomain.com
   MAIL_FROM_NAME="Your App Name"
   ```

4. **Use in your application:**
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

## ⚙️ Configuration

### Environment Variables

| Variable | Description | Required | Default |
|----------|-------------|----------|---------|
| `EXCHANGE_TENANT_ID` | Azure AD Tenant ID | ✅ | - |
| `EXCHANGE_CLIENT_ID` | Azure AD Application ID | ✅ | - |
| `EXCHANGE_CLIENT_SECRET` | Azure AD Application Secret | ✅ | - |
| `EXCHANGE_REDIRECT_URI` | OAuth Redirect URI | ❌ | `http://localhost:8000/oauth/callback` |
| `EXCHANGE_SCOPE` | OAuth Scope | ❌ | `https://graph.microsoft.com/.default` |
| `EXCHANGE_AUTH_METHOD` | Authentication Method | ❌ | `client_credentials` |
| `MAIL_FROM_ADDRESS` | Default From Email | ✅ | - |
| `MAIL_FROM_NAME` | Default From Name | ❌ | `Exchange Email Service` |

### Configuration Array

```php
$config = [
    'tenant_id' => 'your-tenant-id',
    'client_id' => 'your-client-id',
    'client_secret' => 'your-client-secret',
    'redirect_uri' => 'https://yourdomain.com/oauth/callback',
    'scope' => 'https://graph.microsoft.com/.default',
    'auth_method' => 'client_credentials', // or 'authorization_code'
    'from_email' => 'noreply@yourdomain.com',
    'from_name' => 'Your App Name',
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

## 📧 Usage Examples

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

// Send email with custom from address
$emailService->sendEmail(
    'recipient@example.com',
    'Custom From Email',
    '<p>This email has a custom from address.</p>',
    true,
    'custom@yourdomain.com',
    'Custom Sender'
);
```

### Email with CC and BCC

```php
$emailService->sendEmail(
    'recipient@example.com',
    'Email with CC/BCC',
    '<p>This email has CC and BCC recipients.</p>',
    true,
    null, // from_email (uses default)
    null, // from_name (uses default)
    ['cc@example.com'], // CC recipients
    ['bcc@example.com'] // BCC recipients
);
```

### Email with Attachments

```php
$attachments = [
    [
        'name' => 'document.pdf',
        'content' => file_get_contents('path/to/document.pdf'),
        'content_type' => 'application/pdf'
    ],
    [
        'name' => 'image.jpg',
        'content' => file_get_contents('path/to/image.jpg'),
        'content_type' => 'image/jpeg'
    ]
];

$emailService->sendEmail(
    'recipient@example.com',
    'Email with Attachments',
    '<p>Please find the attached files.</p>',
    true,
    null, // from_email
    null, // from_name
    [], // CC
    [], // BCC
    $attachments
);
```

### Template Emails

```php
$template = '
    <h1>Welcome {{name}}!</h1>
    <p>Your order #{{order_id}} is confirmed.</p>
    <p>Total: ${{total}}</p>
    <p>Thank you for choosing {{app_name}}!</p>
';

$data = [
    'name' => 'John Doe',
    'order_id' => '12345',
    'total' => '99.99',
    'app_name' => 'My Store'
];

$emailService->sendTemplateEmail(
    'recipient@example.com',
    'Order Confirmation',
    $template,
    $data
);
```

### Bulk Email Sending

```php
$recipients = [
    'user1@example.com',
    'user2@example.com',
    'user3@example.com'
];

$emailService->sendBulkEmail(
    $recipients,
    'Newsletter',
    '<h1>Monthly Newsletter</h1><p>Check out our latest updates!</p>'
);
```

## 🔐 Azure AD Setup

### 1. Create Azure AD Application

1. Go to [Azure Portal](https://portal.azure.com)
2. Navigate to **Azure Active Directory** > **App registrations**
3. Click **New registration**
4. Fill in details:
   - **Name**: Your Email Service
   - **Redirect URI**: `https://yourdomain.com/oauth/callback` (for authorization code flow)
5. Note down **Application (client) ID** and **Directory (tenant) ID**

### 2. Create Client Secret

1. Go to your app > **Certificates & secrets**
2. Click **New client secret**
3. Add description: "Email Service Secret"
4. Copy the **secret value** (you won't see it again!)

### 3. Grant Permissions

1. Go to your app > **API permissions**
2. Click **Add a permission**
3. Select **Microsoft Graph**
4. Choose **Application permissions**
5. Add **Mail.Send** permission
6. Click **Grant admin consent**

### 4. Configure Redirect URI (for Authorization Code flow)

1. Go to your app > **Authentication**
2. Add your redirect URI: `https://yourdomain.com/oauth/callback`

## 🔄 Authentication Methods

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

## 🛠️ Advanced Usage

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

### Error Handling

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
    // Log the error for debugging
    error_log($e->getTraceAsString());
}
```

### Debug Mode

```php
$config = [
    // ... other config
    'defaults' => [
        'debug' => true,
    ]
];

$emailService = new ExchangeEmailService($config);
// Debug information will be logged
```

## 🧪 Testing

### Test Email Service

```php
use AgabaandreOffice365\ExchangeEmailService\ExchangeEmailService;

$emailService = new ExchangeEmailService([
    'tenant_id' => 'your-tenant-id',
    'client_id' => 'your-client-id',
    'client_secret' => 'your-client-secret',
    'from_email' => 'noreply@yourdomain.com',
    'from_name' => 'Your App Name'
]);

// Test connection
$tokenInfo = $emailService->getTokenInfo();
if (!empty($tokenInfo)) {
    echo "✅ Service is ready!";
} else {
    echo "❌ Service needs configuration.";
}

// Send test email
$emailService->sendEmail(
    'test@example.com',
    'Test Email',
    '<h1>Test</h1><p>This is a test email.</p>'
);
```

## 🚀 Laravel Integration

### Service Provider Registration

The package automatically registers itself with Laravel's service container. You can use it in several ways:

#### Method 1: Dependency Injection (Recommended)

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

#### Method 2: Service Container

```php
$emailService = app(ExchangeEmailService::class);
$emailService->sendEmail(/* ... */);
```

#### Method 3: Facade (if registered)

```php
use ExchangeEmail;

ExchangeEmail::sendEmail(/* ... */);
```

### Laravel Jobs

```php
use AgabaandreOffice365\ExchangeEmailService\ExchangeEmailService;

class SendWelcomeEmail implements ShouldQueue
{
    public function handle(ExchangeEmailService $emailService)
    {
        $emailService->sendEmail(
            $this->email,
            'Welcome!',
            '<h1>Welcome to our platform!</h1>'
        );
    }
}
```

### Laravel Commands

```php
use AgabaandreOffice365\ExchangeEmailService\ExchangeEmailService;

class SendTestEmailCommand extends Command
{
    protected $signature = 'email:test {email}';
    
    public function handle(ExchangeEmailService $emailService)
    {
        $result = $emailService->sendEmail(
            $this->argument('email'),
            'Test Email',
            '<h1>Test Email</h1><p>This is a test email from Laravel.</p>'
        );
        
        $this->info($result ? 'Email sent!' : 'Failed to send email.');
    }
}
```

## 📋 Requirements

- **PHP**: 7.4 or higher
- **Extensions**: cURL, JSON
- **Dependencies**: Guzzle HTTP client (installed via Composer)
- **Azure AD**: Valid app registration with Mail.Send permission

## 🐛 Troubleshooting

### Common Issues

1. **"Email service not configured"**
   - Check your `.env` file has all required variables
   - Ensure OAuth credentials are correct

2. **"AADSTS1002012: The provided value for scope is not valid"**
   - Use `https://graph.microsoft.com/.default` for client credentials flow
   - Use `https://graph.microsoft.com/Mail.Send` for authorization code flow

3. **"AADSTS900023: Specified tenant identifier is not valid"**
   - Check your `EXCHANGE_TENANT_ID` is correct
   - Ensure it's a valid GUID or domain name

4. **"Failed to send email"**
   - Check recipient email address
   - Verify OAuth permissions are granted
   - Check Azure app registration settings

### Debug Mode

Enable debug mode to see detailed logs:

```php
$config = [
    // ... other config
    'defaults' => [
        'debug' => true,
    ]
];
```

## 📄 License

This package is open-sourced software licensed under the [MIT license](LICENSE).

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📞 Support

- **Issues**: [GitHub Issues](https://github.com/agabaandre/exchange_email_service/issues)
- **Email**: agabaandre@gmail.com
- **Documentation**: [GitHub Wiki](https://github.com/agabaandre/exchange_email_service/wiki)

## 🎯 Roadmap

- [ ] Database token storage option
- [ ] Email queue support
- [ ] Advanced template engine
- [ ] Webhook support
- [ ] Rate limiting
- [ ] Email analytics

---

**Made with ❤️ by [Andre Agaba](https://github.com/agabaandre)**