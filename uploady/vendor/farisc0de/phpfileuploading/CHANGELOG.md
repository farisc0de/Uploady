# Changelog

## [3.0.0] - 2026-01-10

Major release introducing production-ready features including custom exceptions, PSR-3 logging, event system, storage abstraction, rate limiting, and virus scanning.

### Added

#### Exception Hierarchy
- `UploadException` - Base exception class with context support
- `ValidationException` - Validation errors with factory methods
- `StorageException` - Storage operation failures
- `FileNotFoundException` - Missing file errors
- `ConfigurationException` - Configuration issues
- `ImageException` - Image processing errors

#### PSR-3 Logging
- `LoggerInterface` - PSR-3 compatible logging interface
- `LogLevel` - Log level constants
- `FileLogger` - File-based logger with rotation support
- `NullLogger` - No-op logger for testing
- `LoggerAwareTrait` - Trait for adding logging to any class

#### Validation System
- `ValidationResult` - Rich validation result objects
- `ValidationError` - Individual error representation
- `ValidatorInterface` - Validator contract
- `ValidationChain` - Chain multiple validators together
- `ExtensionValidator` - Extension whitelist/blacklist validation
- `MimeTypeValidator` - MIME type validation with strict mode
- `SizeValidator` - File size validation with category limits
- `FilenameValidator` - Forbidden names, path traversal detection
- `ImageDimensionValidator` - Image dimension and aspect ratio validation

#### Storage Abstraction
- `StorageInterface` - Flysystem-compatible storage interface
- `LocalStorage` - Local filesystem storage adapter
- `FileInfo` - File/directory metadata representation
- `StorageManager` - Multi-disk storage management

#### Rate Limiting
- `RateLimiterInterface` - Rate limiter contract
- `RateLimitResult` - Rate limit check result with headers
- `InMemoryRateLimiter` - Memory-based rate limiter
- `FileRateLimiter` - Persistent file-based rate limiter

#### Virus Scanning
- `VirusScannerInterface` - Virus scanner contract
- `ScanResult` - Scan result representation
- `ClamAvScanner` - ClamAV integration (socket + CLI)
- `NullScanner` - No-op scanner for development

#### Event System
- `EventInterface` - Event contract
- `AbstractEvent` - Base event class
- `UploadEvents` - Event name constants
- `FileEvent` - File-related events
- `ValidationEvent` - Validation events with result
- `ScanEvent` - Virus scan events
- `EventDispatcher` - Event dispatcher with priorities
- `ListenerInterface` - Event listener contract

#### New Classes
- `UploadManager` - All-in-one upload manager combining all features
- `UploadResult` - Comprehensive upload result object

#### Testing
- PHPUnit test suite with unit tests for all new components
- `phpunit.xml` configuration file

### Changed

#### Upload Class
- Integrated `LoggerAwareTrait` for PSR-3 logging
- Added `EventDispatcher` integration for upload lifecycle events
- Replaced generic exceptions with custom exception hierarchy
- Added `enableExceptions()` method to throw exceptions instead of returning false
- Added `validate()` method returning `ValidationResult` object
- Added `setEventDispatcher()` and `getEventDispatcher()` methods
- Added `getFile()` and `getFileName()` accessor methods
- All validation methods now log warnings on failure
- Events dispatched for: before/after upload, before/after validation, validation failed, upload failed

#### composer.json
- Updated PHP requirement to 8.1+
- Added dev dependencies: phpunit, phpstan, phpcs
- Added autoload-dev for tests namespace
- Added scripts: test, test-coverage, phpstan, cs-check, cs-fix
- Added suggested packages: ext-gd, league/flysystem, monolog

#### Documentation
- Comprehensive README.md with all new features
- Usage examples for all components
- Security best practices guide

### Examples
- Updated `production.php` with multi-file upload support
- Demonstrates all new features: validation chain, rate limiting, virus scanning, events, logging

### Dependencies
- Requires PHP 8.1 or higher
- Requires `fileinfo` extension
- Requires `json` extension
- Optional: `gd` extension for image processing

### Migration Guide

Users upgrading from 2.x should note:

1. Update PHP version to 8.1 or higher
2. Exceptions are now specific types - update catch blocks
3. Consider using `UploadManager` for new projects
4. Enable logging with `setLogger()` method
5. Use `enableExceptions(true)` for exception-based flow
6. Use `validate()` method for detailed validation results

## [2.6.0] - 2025-05-03

Enhanced file filtering and validation with a more comprehensive approach to file type management.

### Added

- Enhanced filter.json structure with version tracking
- Added categorized MIME types for better file type validation
- Added category-specific file size limits
- Expanded the forbidden files list for improved security
- Added support for more file extensions and MIME types

### Changed

- Updated Upload class to use the enhanced filter.json structure
- Improved file category detection based on MIME types
- Enhanced size limit validation with category-specific limits
- Improved MIME type validation with better error handling
- Updated isImage() method to use the new categories structure

## [2.0.0] - 2025-01-14

A major update focusing on modernizing the codebase with enhanced type safety, improved error handling, and new features across all core classes.

### Breaking Changes

#### Upload Class

- Added strict type declarations for all properties and methods
- Enhanced constructor with proper dependency injection
- Improved error handling with specific exception types
- Added validation for constructor parameters
- Changed method signatures to include return types
- Improved file validation and security checks

#### File Class

- Added strict type declarations and return types
- Enhanced error handling with specific exceptions
- Improved file validation and type checking
- Added proper resource management
- Changed method signatures for better type safety

#### Image Class

- Complete rewrite with modern image manipulation features
- Added support for WebP format
- Enhanced watermarking capabilities with opacity control
- Added image resizing with aspect ratio preservation
- Added filter application support
- Improved resource management and memory handling
- Added comprehensive image information retrieval

#### Utility Class

- Renamed methods for clarity:
  - `fixintOverflow` → `fixIntOverflow`
  - `unitConvert` → `convertUnit`
  - `fixArray` → `normalizeFileArray`
  - `protectFolder` → `secureDirectory`
- Added strict type declarations
- Enhanced error handling
- Improved file permission management

### New Features

#### Upload Class

- Added support for chunk-based uploads
- Enhanced file type validation
- Added QR code generation for downloads
- Added comprehensive file metadata handling
- Added support for custom validation rules

#### Image Class

- Added `resize()` method for image resizing
- Added `addWatermark()` with position and opacity control
- Added `convert()` for format conversion
- Added `applyFilter()` for image filters
- Added `getInfo()` for detailed image information

#### Utility Class

- Added tracking of PHP INI changes
- Enhanced directory security features
- Added comprehensive size conversion utilities
- Added improved callback handling
- Added better sanitization with HTML5 support

### Improvements

#### Security

- Added proper input validation across all classes
- Enhanced file type checking
- Improved directory protection
- Added secure file handling practices
- Enhanced sanitization methods

#### Performance

- Improved memory management in image operations
- Enhanced file streaming capabilities
- Added proper resource cleanup
- Optimized file operations

#### Code Quality

- Added comprehensive PHPDoc documentation
- Improved code organization
- Added proper error messages
- Enhanced type safety
- Added consistent error handling

### Dependencies

- Requires PHP 7.4 or higher
- Added support for GD library features
- Added WebP support

### Documentation

- Added comprehensive method documentation
- Improved error message clarity
- Added usage examples
- Enhanced type information

### Migration Guide

Users upgrading from 1.x should note:

1. Update PHP version to 7.4 or higher
2. Review method signatures for type changes
3. Update exception handling for new specific exceptions
4. Review renamed utility methods
5. Update file security implementations

### Contributors

- fariscode <farisksa79@gmail.com>

[2.0.0]: https://github.com/farisc0de/PhpFileUploading/releases/tag/v2.0.0
