# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2024-10-02

### Added
- Initial release of Exchange Email Service
- Microsoft Graph API integration with OAuth 2.0 authentication
- Support for both Client Credentials and Authorization Code flows
- File-based token storage (no database required)
- Laravel service provider with auto-discovery
- Framework-agnostic design (works with any PHP framework)
- Comprehensive error handling and logging
- Built-in email templates and HTML support
- CC/BCC and attachment support
- Bulk email sending capabilities
- Complete documentation and examples

### Features
- **OAuth 2.0 Authentication** - Support for both Authorization Code and Client Credentials flows
- **File-based Token Storage** - No database required, tokens stored in JSON files
- **Automatic Token Refresh** - Built-in token management with automatic refresh
- **Framework Agnostic** - Works with Laravel, CodeIgniter, Yii, and vanilla PHP
- **Simple API** - Easy to use with minimal configuration
- **HTML & Text Support** - Send both HTML and plain text emails
- **Template Support** - Simple template rendering with variable substitution
- **CC/BCC Support** - Send emails with carbon copy and blind carbon copy
- **Error Handling** - Comprehensive error handling and logging
- **Debug Mode** - Built-in debugging and logging capabilities

### Laravel Integration
- Service provider auto-discovery
- Configuration publishing
- Migration publishing
- Dependency injection support
- Artisan command integration

### Documentation
- Complete README with examples
- Laravel integration guide
- Standalone usage examples
- Troubleshooting guide
- API documentation
