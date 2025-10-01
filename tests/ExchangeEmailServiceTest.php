<?php

namespace SendMail\ExchangeEmailService\Tests;

use PHPUnit\Framework\TestCase;
use SendMail\ExchangeEmailService\ExchangeEmailService;
use SendMail\ExchangeEmailService\ExchangeOAuth;

class ExchangeEmailServiceTest extends TestCase
{
    private $emailService;
    private $config;

    protected function setUp(): void
    {
        $this->config = [
            'tenant_id' => 'test-tenant-id',
            'client_id' => 'test-client-id',
            'client_secret' => 'test-client-secret',
            'redirect_uri' => 'http://localhost/oauth/callback',
            'scope' => 'https://graph.microsoft.com/Mail.Send',
            'from_email' => 'test@example.com',
            'from_name' => 'Test Sender',
        ];

        $this->emailService = new ExchangeEmailService($this->config);
    }

    public function testCanInstantiateService()
    {
        $this->assertInstanceOf(ExchangeEmailService::class, $this->emailService);
    }

    public function testConfigurationIsSet()
    {
        $this->assertEquals('test-tenant-id', $this->config['tenant_id']);
        $this->assertEquals('test-client-id', $this->config['client_id']);
        $this->assertEquals('test@example.com', $this->config['from_email']);
    }

    public function testOAuthClassExists()
    {
        $this->assertTrue(class_exists(ExchangeOAuth::class));
    }

    public function testServiceProviderClassExists()
    {
        $this->assertTrue(class_exists('SendMail\ExchangeEmailService\ExchangeEmailServiceProvider'));
    }

    public function testConfigurationValidation()
    {
        // Test with missing required configuration
        $invalidConfig = [
            'tenant_id' => 'test-tenant-id',
            // Missing other required fields
        ];

        $this->expectException(\Exception::class);
        new ExchangeEmailService($invalidConfig);
    }
}
