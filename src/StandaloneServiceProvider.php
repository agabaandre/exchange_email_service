<?php

namespace AgabaandreOffice365\ExchangeEmailService;

/**
 * Standalone Service Provider
 * 
 * This is a fallback service provider for non-Laravel environments.
 * It provides basic functionality without Laravel dependencies.
 */
class StandaloneServiceProvider
{
    public function __construct()
    {
        throw new \Exception('Laravel framework is required to use ExchangeEmailServiceProvider. Use ExchangeEmailService directly for non-Laravel applications.');
    }
}
