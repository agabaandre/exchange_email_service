# SendMail ExchangeEmailService

**General-purpose email service using Microsoft Graph API with OAuth 2.0 authentication**

[![Latest Version](https://img.shields.io/packagist/v/sendmail/exchange-email-service.svg)](https://packagist.org/packages/sendmail/exchange-email-service)
[![License](https://img.shields.io/packagist/l/sendmail/exchange-email-service.svg)](https://packagist.org/packages/sendmail/exchange-email-service)
[![PHP Version](https://img.shields.io/packagist/php-v/sendmail/exchange-email-service.svg)](https://packagist.org/packages/sendmail/exchange-email-service)

## 🚀 Features

- ✅ **Microsoft Graph API** - Most reliable email method
- ✅ **OAuth 2.0 Security** - No password storage required
- ✅ **Automatic Token Refresh** - No user interaction needed
- ✅ **Laravel Compatible** - Easy integration
- ✅ **General Purpose** - Works for any email sending needs
- ✅ **Production Ready** - Tested and reliable
- ✅ **Multiple Recipients** - Support for CC, BCC, bulk emails
- ✅ **File Attachments** - Send files with emails
- ✅ **Email Templates** - Built-in responsive templates
- ✅ **Flexible Configuration** - Works with any Microsoft 365/Azure AD

## 📦 Installation

### Via Composer

```bash
composer require sendmail/exchange-email-service
```

### Manual Installation

1. Download the package
2. Copy to your project directory
3. Run `composer install`

## 🔧 Configuration

### 1. Environment Variables

Add to your `.env` file:

```env
# Microsoft Graph OAuth Configuration
EXCHANGE_TENANT_ID=your_tenant_id
EXCHANGE_CLIENT_ID=your_client_id
EXCHANGE_CLIENT_SECRET=your_client_secret
EXCHANGE_REDIRECT_URI=http://your-domain.com/oauth/callback
EXCHANGE_SCOPE=https://graph.microsoft.com/Mail.Send

# Email Configuration
MAIL_FROM_ADDRESS=noreply@yourcompany.com
MAIL_FROM_NAME=Your Company Name

# Database Configuration (for token storage)
DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 2. Laravel Integration

#### Register Service Provider

Add to `config/app.php`:

```php
'providers' => [
    // ... other providers
    SendMail\ExchangeEmailService\ExchangeEmailServiceProvider::class,
],
```

#### Publish Configuration

```bash
php artisan vendor:publish --provider="SendMail\ExchangeEmailService\ExchangeEmailServiceProvider"
```

#### Run Migrations

```bash
php artisan migrate
```

## 🔐 OAuth Setup (One-Time Only)

### 1. Azure App Registration

1. Go to [Azure Portal](https://portal.azure.com)
2. Navigate to "Azure Active Directory" > "App registrations"
3. Click "New registration"
4. Fill in details:
   - **Name**: Your Email Service
   - **Redirect URI**: `http://your-domain.com/oauth/callback`
5. Note down **Application (client) ID** and **Directory (tenant) ID**

### 2. Generate Client Secret

1. Go to "Certificates & secrets"
2. Click "New client secret"
3. Add description: "Email Service Secret"
4. Copy the **secret value**

### 3. API Permissions

1. Go to "API permissions"
2. Click "Add a permission"
3. Select "Microsoft Graph"
4. Choose "Application permissions"
5. Add: `Mail.Send`
6. Click "Grant admin consent"

### 4. Complete OAuth Setup

Create a route for OAuth callback:

```php
// routes/web.php
Route::get('/oauth/callback', function () {
    $code = request('code');
    $state = request('state');
    
    $emailService = app(ExchangeEmailService::class);
    $success = $emailService->processOAuthCallback($code, $state);
    
    if ($success) {
        return 'OAuth setup completed successfully!';
    } else {
        return 'OAuth setup failed. Please try again.';
    }
});
```

## 📧 Usage

### Basic Email Sending

```php
use SendMail\ExchangeEmailService\ExchangeEmailService;

$emailService = new ExchangeEmailService();

// Send simple email
$emailService->sendEmail(
    'user@example.com',
    'Welcome!',
    '<h1>Hello World!</h1>',
    true // is HTML
);
```

### Laravel Integration

```php
// In your controller
use SendMail\ExchangeEmailService\ExchangeEmailService;

public function sendWelcomeEmail(Request $request)
{
    $emailService = app(ExchangeEmailService::class);
    
    $emailService->sendEmail(
        $request->email,
        'Welcome to Our Service!',
        '<h1>Thank you for joining us!</h1>'
    );
}
```

### Using Templates

```php
$emailService = new ExchangeEmailService();

// Send welcome email with template
$emailService->sendTemplateEmail(
    'user@example.com',
    'Welcome!',
    'welcome',
    [
        'name' => 'John Doe',
        'app_name' => 'My Awesome App'
    ]
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

### Email with CC and BCC

```php
$emailService->sendEmail(
    'user@example.com',
    'Important Update',
    '<h1>System Maintenance</h1>',
    true, // is HTML
    'noreply@company.com', // from email
    'Company Name', // from name
    ['manager@company.com'], // CC
    ['admin@company.com'] // BCC
);
```

### Email with Attachments

```php
$attachments = [
    [
        'name' => 'document.pdf',
        'content' => file_get_contents('path/to/document.pdf'),
        'content_type' => 'application/pdf'
    ]
];

$emailService->sendEmail(
    'user@example.com',
    'Document Attached',
    '<p>Please find the attached document.</p>',
    true,
    'noreply@company.com',
    'Company Name',
    [],
    [],
    $attachments
);
```

## 🧪 Testing

### Test Email Service

```php
use SendMail\ExchangeEmailService\ExchangeEmailService;

$emailService = new ExchangeEmailService();

// Test connection
$result = $emailService->testConnection();
if ($result['status'] === 'ready') {
    echo "Email service is ready!";
} else {
    echo "Error: " . $result['error'];
}

// Send test email
$emailService->sendTestEmail('test@example.com');
```

### Laravel Testing

```php
// In your test
use SendMail\ExchangeEmailService\ExchangeEmailService;

public function test_email_sending()
{
    $emailService = app(ExchangeEmailService::class);
    
    $result = $emailService->sendTestEmail('test@example.com');
    $this->assertTrue($result);
}
```

## 📧 Built-in Templates

### Welcome Template

```php
$emailService->sendTemplateEmail(
    'user@example.com',
    'Welcome!',
    'welcome',
    [
        'name' => 'John Doe',
        'app_name' => 'My App'
    ]
);
```

### Notification Template

```php
$emailService->sendTemplateEmail(
    'user@example.com',
    'Important Notification',
    'notification',
    [
        'name' => 'John Doe',
        'title' => 'System Update',
        'message' => 'Your account has been updated.',
        'details' => 'All settings have been synchronized.',
        'app_name' => 'My App'
    ]
);
```

### Confirmation Template

```php
$emailService->sendTemplateEmail(
    'user@example.com',
    'Action Confirmed',
    'confirmation',
    [
        'name' => 'John Doe',
        'title' => 'Registration Confirmed',
        'message' => 'Your registration has been successfully confirmed.',
        'reference_id' => 'REG-12345',
        'date' => date('Y-m-d H:i:s'),
        'status' => 'Active',
        'app_name' => 'My App'
    ]
);
```

## 🔄 Laravel Integration

### Service Container

The service is automatically registered in Laravel's service container:

```php
// In your controller
public function sendEmail(Request $request)
{
    $emailService = app(ExchangeEmailService::class);
    
    $emailService->sendEmail(
        $request->email,
        'Welcome!',
        '<h1>Thank you for registering!</h1>'
    );
}
```

### Queue Integration

```php
// In your job
use SendMail\ExchangeEmailService\ExchangeEmailService;

class SendWelcomeEmail implements ShouldQueue
{
    public function handle()
    {
        $emailService = new ExchangeEmailService();
        $emailService->sendTemplateEmail(
            $this->email,
            'Welcome!',
            'welcome',
            ['name' => $this->name, 'app_name' => 'My App']
        );
    }
}
```

### Artisan Commands

```php
// Create custom command
php artisan make:command TestEmailService

// In the command
use SendMail\ExchangeEmailService\ExchangeEmailService;

public function handle()
{
    $emailService = new ExchangeEmailService();
    $emailService->sendTestEmail('admin@example.com');
    $this->info('Test email sent!');
}
```

## 🛠️ Troubleshooting

### Common Issues

1. **"Email service not configured"**
   - Check your `.env` file has all required variables
   - Ensure OAuth credentials are correct

2. **"No valid OAuth tokens"**
   - Complete the one-time OAuth setup
   - Visit `/oauth/callback` to authenticate

3. **"Failed to send email"**
   - Check recipient email address
   - Verify OAuth permissions are granted
   - Check Azure app registration settings

### Debug Mode

Enable debug mode in your `.env`:

```env
EXCHANGE_DEBUG=true
```

## 📋 Requirements

- PHP 7.4+
- Laravel 6.0+ (optional)
- Microsoft 365/Azure AD account
- Valid OAuth app registration

## 🎯 Production Checklist

- [ ] OAuth app registered in Azure
- [ ] Environment variables configured
- [ ] OAuth setup completed (one-time)
- [ ] Test email sent successfully
- [ ] Database migrations run
- [ ] Queue jobs configured (if using queues)
- [ ] Error handling implemented
- [ ] Logging configured

## 📞 Support

For issues or questions:

- **Email**: support@sendmail.com
- **Documentation**: [GitHub Wiki](https://github.com/sendmail/exchange-email-service/wiki)
- **Issues**: [GitHub Issues](https://github.com/sendmail/exchange-email-service/issues)

## 📄 License

This package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

**SendMail ExchangeEmailService** - *Reliable email sending for any application*
