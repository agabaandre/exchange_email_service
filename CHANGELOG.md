# Changelog

All notable changes to this project will be documented in this file.

## [1.1.3] - 2024-10-02

### Fixed
- Enhanced error handling for both string and array error responses
- Improved error message extraction from Microsoft Graph API responses
- More robust error handling in OAuth token exchange and client credentials flow

### Improved
- Better error message formatting and debugging capabilities
- Consistent error handling across all API interaction methods
- More reliable error reporting for troubleshooting

## [1.1.2] - 2024-10-02

### Fixed
- Fixed GitHub Actions workflow Docker pull error
- Replaced problematic Docker action with reliable curl-based Packagist update
- Improved CI/CD pipeline stability and reliability

### Improved
- GitHub Actions workflow now uses standard Ubuntu runner
- Better error handling and logging in CI/CD pipeline
- More reliable Packagist update mechanism

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.0] - 2024-10-02

### Added
- Comprehensive README with detailed examples and documentation
- Enhanced configuration options and environment variable support
- Improved error handling and debugging capabilities
- Better Laravel integration examples and best practices
- Advanced usage examples for bulk emails, attachments, and templates
- Troubleshooting guide with common issues and solutions
- Azure AD setup guide with step-by-step instructions
- Token management examples and utilities
- Debug mode configuration and logging
- Production-ready examples and optimization tips

### Improved
- Documentation structure and readability
- Code examples with better formatting
- Configuration flexibility and ease of use
- Error messages and debugging information
- Laravel integration examples
- Package metadata and discoverability

### Fixed
- Minor documentation inconsistencies
- Configuration examples and clarity
- Environment variable handling

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
