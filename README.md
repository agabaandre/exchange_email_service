# AgabaandreOffice365 ExchangeEmailService

**General-purpose email service using Microsoft Graph API with OAuth 2.0 authentication**

[![Latest Version](https://img.shields.io/packagist/v/agabaandre-office365/exchange-email-service.svg)](https://packagist.org/packages/agabaandre-office365/exchange-email-service)
[![License](https://img.shields.io/packagist/l/agabaandre-office365/exchange-email-service.svg)](https://packagist.org/packages/agabaandre-office365/exchange-email-service)
[![PHP Version](https://img.shields.io/packagist/php-v/agabaandre-office365/exchange-email-service.svg)](https://packagist.org/packages/agabaandre-office365/exchange-email-service)

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
composer require agabaandre-office365/exchange-email-service
```

### Development Installation

```bash
git clone https://github.com/agabaandre/exchange_email_service.git
cd exchange-email-service
composer install
```

### Manual Installation

1. Download the package
2. Copy to your project directory
3. Run `composer install`

## 🚀 Quick Start (Standalone Usage)

**No Laravel required!** This package works in any PHP application:

```php
<?php
require_once 'vendor/autoload.php';

use AgabaandreOffice365\ExchangeEmailService\ExchangeEmailService;

// Create email service
$emailService = new ExchangeEmailService([
    'tenant_id' => 'your-tenant-id',
    'client_id' => 'your-client-id',
    'client_secret' => 'your-client-secret',
    'redirect_uri' => 'http://your-domain.com/oauth/callback',
    'from_email' => 'noreply@yourcompany.com',
    'from_name' => 'Your Company'
]);

// Send email
$emailService->sendEmail(
    'recipient@example.com',
    'Hello World!',
    '<h1>This is a test email</h1>',
    true // is HTML
);
```

**See `simple_example.php` for more examples!**

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

**For Laravel 11+ (including Laravel 12):**

Add to `bootstrap/providers.php`:

```php
<?php

return [
    // ... other providers
    AgabaandreOffice365\ExchangeEmailService\ExchangeEmailServiceProvider::class,
];
```

**For Laravel 10 and earlier:**

Add to `config/app.php`:

```php
'providers' => [
    // ... other providers
    AgabaandreOffice365\ExchangeEmailService\ExchangeEmailServiceProvider::class,
],
```

#### Publish Configuration

**Important**: Make sure the service provider is registered first (see step above).

```bash
# Publish all resources
php artisan vendor:publish --provider="AgabaandreOffice365\ExchangeEmailService\ExchangeEmailServiceProvider"

# Or publish specific resources
php artisan vendor:publish --tag=exchange-email-config
php artisan vendor:publish --tag=exchange-email-migrations

# If you get "No publishable resources" error, try:
php artisan config:clear
php artisan cache:clear
php artisan vendor:publish --provider="AgabaandreOffice365\ExchangeEmailService\ExchangeEmailServiceProvider"
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

### Standalone Usage (No Laravel)

The package works perfectly without Laravel in any PHP application:

```php
<?php
require_once 'vendor/autoload.php';

use AgabaandreOffice365\ExchangeEmailService\ExchangeEmailService;

// Method 1: Direct configuration
$emailService = new ExchangeEmailService([
    'tenant_id' => 'your-tenant-id',
    'client_id' => 'your-client-id',
    'client_secret' => 'your-client-secret',
    'redirect_uri' => 'http://your-domain.com/oauth/callback',
    'from_email' => 'noreply@yourcompany.com',
    'from_name' => 'Your Company'
]);

// Method 2: Environment variables (recommended)
$emailService = new ExchangeEmailService(); // Reads from .env file

// Method 3: Configuration file
$config = include 'config_standalone.php';
$emailService = new ExchangeEmailService($config);

// Send email
$emailService->sendEmail(
    'recipient@example.com',
    'Subject',
    '<h1>Hello World!</h1>',
    true // is HTML
);
```

**Examples:**
- `simple_example.php` - Basic usage examples
- `standalone_usage.php` - Complete standalone example
- `example_usage.php` - Comprehensive examples

### Laravel Integration

**Quick Setup:**
1. Install: `composer require agabaandre-office365/exchange-email-service`
2. Add configuration to `config/services.php` (see below)
3. Add environment variables to `.env` (see below)
4. Use directly in your code - no service provider needed!

**Configuration:**

Add to your `config/services.php`:
```php
'exchange' => [
    'tenant_id' => env('EXCHANGE_TENANT_ID'),
    'client_id' => env('EXCHANGE_CLIENT_ID'),
    'client_secret' => env('EXCHANGE_CLIENT_SECRET'),
    'redirect_uri' => env('EXCHANGE_REDIRECT_URI'),
],
```

Add to your `.env`:
```env
EXCHANGE_TENANT_ID=your_tenant_id
EXCHANGE_CLIENT_ID=your_client_id
EXCHANGE_CLIENT_SECRET=your_client_secret
EXCHANGE_REDIRECT_URI=http://your-domain.com/oauth/callback
```

**Usage in Controllers:**
```php
<?php
// In your Laravel controller

use AgabaandreOffice365\ExchangeEmailService\ExchangeEmailService;

class EmailController extends Controller
{
    public function sendEmail()
    {
        $emailService = new ExchangeEmailService([
            'tenant_id' => config('services.exchange.tenant_id'),
            'client_id' => config('services.exchange.client_id'),
            'client_secret' => config('services.exchange.client_secret'),
            'redirect_uri' => config('services.exchange.redirect_uri'),
            'from_email' => config('mail.from.address'),
            'from_name' => config('mail.from.name')
        ]);

        $emailService->sendEmail(
            'user@example.com',
            'Welcome!',
            '<h1>Thank you for joining us!</h1>',
            true
        );
    }
}
```

**Usage in Laravel Jobs:**
```php
<?php
// app/Jobs/SendWelcomeEmailJob.php

use AgabaandreOffice365\ExchangeEmailService\ExchangeEmailService;

class SendWelcomeEmailJob implements ShouldQueue
{
    public function handle()
    {
        $emailService = new ExchangeEmailService([
            'tenant_id' => config('services.exchange.tenant_id'),
            'client_id' => config('services.exchange.client_id'),
            'client_secret' => config('services.exchange.client_secret'),
            'redirect_uri' => config('services.exchange.redirect_uri'),
            'from_email' => config('mail.from.address'),
            'from_name' => config('mail.from.name')
        ]);
        
        $emailService->sendTemplateEmail(
            $this->email,
            'Welcome!',
            'welcome',
            ['name' => $this->name, 'app_name' => config('app.name')]
        );
    }
}
```

**Usage in Laravel Commands:**
```php
<?php
// app/Console/Commands/SendTestEmailCommand.php

use AgabaandreOffice365\ExchangeEmailService\ExchangeEmailService;

class SendTestEmailCommand extends Command
{
    protected $signature = 'email:test {email}';
    
    public function handle()
    {
        $emailService = new ExchangeEmailService([
            'tenant_id' => config('services.exchange.tenant_id'),
            'client_id' => config('services.exchange.client_id'),
            'client_secret' => config('services.exchange.client_secret'),
            'redirect_uri' => config('services.exchange.redirect_uri'),
            'from_email' => config('mail.from.address'),
            'from_name' => config('mail.from.name')
        ]);
        
        $result = $emailService->sendTestEmail($this->argument('email'));
        $this->info($result ? 'Email sent!' : 'Failed to send email.');
    }
}
```

**Optional: Publish Configuration and Migrations**
```bash
# Publish configuration and migration files (optional)
php artisan vendor:publish --all
```

**See `laravel_standalone_example.php` for complete examples!**

### Basic Email Sending

```php
use AgabaandreOffice365\ExchangeEmailService\ExchangeEmailService;

$emailService = new ExchangeEmailService([
    'tenant_id' => 'your-tenant-id',
    'client_id' => 'your-client-id',
    'client_secret' => 'your-client-secret',
    'redirect_uri' => 'http://your-domain.com/oauth/callback',
    'from_email' => 'noreply@yourcompany.com',
    'from_name' => 'Your Company'
]);

// Send simple email
$emailService->sendEmail(
    'user@example.com',
    'Welcome!',
    '<h1>Hello World!</h1>',
    true // is HTML
);
```

### Using Templates

```php
$emailService = new ExchangeEmailService([
    'tenant_id' => 'your-tenant-id',
    'client_id' => 'your-client-id',
    'client_secret' => 'your-client-secret',
    'redirect_uri' => 'http://your-domain.com/oauth/callback',
    'from_email' => 'noreply@yourcompany.com',
    'from_name' => 'Your Company'
]);

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
$emailService = new ExchangeEmailService([
    'tenant_id' => 'your-tenant-id',
    'client_id' => 'your-client-id',
    'client_secret' => 'your-client-secret',
    'redirect_uri' => 'http://your-domain.com/oauth/callback',
    'from_email' => 'noreply@yourcompany.com',
    'from_name' => 'Your Company'
]);

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
$emailService = new ExchangeEmailService([
    'tenant_id' => 'your-tenant-id',
    'client_id' => 'your-client-id',
    'client_secret' => 'your-client-secret',
    'redirect_uri' => 'http://your-domain.com/oauth/callback',
    'from_email' => 'noreply@yourcompany.com',
    'from_name' => 'Your Company'
]);

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
$emailService = new ExchangeEmailService([
    'tenant_id' => 'your-tenant-id',
    'client_id' => 'your-client-id',
    'client_secret' => 'your-client-secret',
    'redirect_uri' => 'http://your-domain.com/oauth/callback',
    'from_email' => 'noreply@yourcompany.com',
    'from_name' => 'Your Company'
]);

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
use AgabaandreOffice365\ExchangeEmailService\ExchangeEmailService;

$emailService = new ExchangeEmailService([
    'tenant_id' => 'your-tenant-id',
    'client_id' => 'your-client-id',
    'client_secret' => 'your-client-secret',
    'redirect_uri' => 'http://your-domain.com/oauth/callback',
    'from_email' => 'noreply@yourcompany.com',
    'from_name' => 'Your Company'
]);

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

## 📧 Built-in Templates

### Welcome Template

```php
$emailService = new ExchangeEmailService([
    'tenant_id' => 'your-tenant-id',
    'client_id' => 'your-client-id',
    'client_secret' => 'your-client-secret',
    'redirect_uri' => 'http://your-domain.com/oauth/callback',
    'from_email' => 'noreply@yourcompany.com',
    'from_name' => 'Your Company'
]);

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
$emailService = new ExchangeEmailService([
    'tenant_id' => 'your-tenant-id',
    'client_id' => 'your-client-id',
    'client_secret' => 'your-client-secret',
    'redirect_uri' => 'http://your-domain.com/oauth/callback',
    'from_email' => 'noreply@yourcompany.com',
    'from_name' => 'Your Company'
]);

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
$emailService = new ExchangeEmailService([
    'tenant_id' => 'your-tenant-id',
    'client_id' => 'your-client-id',
    'client_secret' => 'your-client-secret',
    'redirect_uri' => 'http://your-domain.com/oauth/callback',
    'from_email' => 'noreply@yourcompany.com',
    'from_name' => 'Your Company'
]);

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

4. **"No publishable resources for tag []"**
   - This is normal - the package works without publishing
   - Use `php artisan vendor:publish --all` if you want to publish config/migrations
   - Or just use the package directly without publishing

### Debug Mode

Enable debug mode in your `.env`:

```env
EXCHANGE_DEBUG=true
```

## 📋 Requirements

- PHP 7.4+
- Microsoft 365/Azure AD account
- Valid OAuth app registration
- Laravel 6.0+ (optional - works with any PHP framework)

## 🎯 Production Checklist

- [ ] OAuth app registered in Azure
- [ ] Environment variables configured
- [ ] OAuth setup completed (one-time)
- [ ] Test email sent successfully
- [ ] Database migrations run (if using database)
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
