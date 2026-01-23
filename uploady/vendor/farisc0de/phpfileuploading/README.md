# PhpFileUploading

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/PHP-8.1%2B-blue.svg)](https://php.net)

Production-ready PHP library for secure file uploads with validation, virus scanning, rate limiting, and cloud storage support.

## Features

- **Security First**: MIME type validation, extension filtering, forbidden filename blocking, path traversal prevention
- **Validation System**: Chainable validators with detailed result objects
- **Rate Limiting**: Prevent abuse with configurable rate limiters (in-memory or file-based)
- **Virus Scanning**: ClamAV integration for malware detection
- **Storage Abstraction**: Local storage with Flysystem-compatible interface for cloud providers
- **Event System**: Hook into upload lifecycle with event listeners
- **Image Processing**: Resize, compress, watermark, and convert images
- **Logging**: PSR-3 compatible logging interface
- **Multi-File Upload**: Handle single and multiple file uploads

## Requirements

- PHP 8.1 or higher
- `fileinfo` extension
- `json` extension
- `gd` extension (for image processing)

## Installation

```bash
composer require farisc0de/phpfileuploading
```

## Quick Start

### Basic Upload

```php
<?php

use Farisc0de\PhpFileUploading\Upload;
use Farisc0de\PhpFileUploading\File;
use Farisc0de\PhpFileUploading\Utility;

$upload = new Upload(new Utility());

$upload->setUploadFolder([
    'folder_name' => 'uploads',
    'folder_path' => realpath('uploads')
]);

$upload->enableProtection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $file = new File($_FILES['file'], new Utility());
    $upload->setUpload($file);

    if ($upload->checkIfNotEmpty() && 
        $upload->checkExtension() && 
        $upload->checkMime() && 
        $upload->checkSize()) {
        
        $upload->hashName(); // Generate secure filename
        $upload->upload();
        
        echo "File uploaded successfully!";
    }
}
```

### Using the Validation Chain

```php
<?php

use Farisc0de\PhpFileUploading\File;
use Farisc0de\PhpFileUploading\Utility;
use Farisc0de\PhpFileUploading\Validation\ValidationChain;
use Farisc0de\PhpFileUploading\Validation\Validators\ExtensionValidator;
use Farisc0de\PhpFileUploading\Validation\Validators\MimeTypeValidator;
use Farisc0de\PhpFileUploading\Validation\Validators\SizeValidator;
use Farisc0de\PhpFileUploading\Validation\Validators\FilenameValidator;

$file = new File($_FILES['file'], new Utility());

$validator = new ValidationChain();
$validator
    ->addValidator(new ExtensionValidator(['jpg', 'png', 'gif', 'pdf']))
    ->addValidator(new MimeTypeValidator([
        'image/jpeg', 'image/png', 'image/gif', 'application/pdf'
    ]))
    ->addValidator(new SizeValidator(null, '10 MB'))
    ->addValidator(new FilenameValidator(['shell.php', 'backdoor.php']));

$result = $validator->validate($file);

if ($result->isValid()) {
    // Proceed with upload
} else {
    // Handle errors
    foreach ($result->getErrors() as $error) {
        echo $error->getMessage() . "\n";
    }
}
```

### Rate Limiting

```php
<?php

use Farisc0de\PhpFileUploading\RateLimiting\FileRateLimiter;

// Allow 10 uploads per minute per IP
$limiter = new FileRateLimiter('/path/to/rate-limit-storage', 10, 60);

$clientIp = $_SERVER['REMOTE_ADDR'];
$result = $limiter->check($clientIp);

if (!$result->isAllowed()) {
    http_response_code(429);
    header('Retry-After: ' . $result->getRetryAfter());
    die('Rate limit exceeded. Try again later.');
}

// Proceed with upload...
```

### Virus Scanning

```php
<?php

use Farisc0de\PhpFileUploading\Security\ClamAvScanner;

$scanner = new ClamAvScanner(
    socketPath: '/var/run/clamav/clamd.sock'
);

if ($scanner->isAvailable()) {
    $result = $scanner->scan($file->getTempName());
    
    if ($result->isInfected()) {
        die('Virus detected: ' . $result->getVirusName());
    }
}
```

### Event System

```php
<?php

use Farisc0de\PhpFileUploading\Events\EventDispatcher;
use Farisc0de\PhpFileUploading\Events\UploadEvents;
use Farisc0de\PhpFileUploading\Events\FileEvent;

$dispatcher = new EventDispatcher();

// Log all uploads
$dispatcher->addListener(UploadEvents::AFTER_UPLOAD, function (FileEvent $event) {
    $logger->info('File uploaded: ' . $event->getFilename());
});

// Block certain file types
$dispatcher->addListener(UploadEvents::BEFORE_VALIDATION, function (FileEvent $event) {
    $file = $event->getFile();
    if (str_ends_with($file->getName(), '.exe')) {
        $event->stopPropagation();
        throw new \Exception('EXE files are not allowed');
    }
});
```

### Storage Abstraction

```php
<?php

use Farisc0de\PhpFileUploading\Storage\LocalStorage;
use Farisc0de\PhpFileUploading\Storage\StorageManager;

// Single disk
$storage = new LocalStorage('/var/www/uploads', 0755, 0644, 'https://example.com/uploads');

$storage->write('path/to/file.jpg', $contents);
$url = $storage->publicUrl('path/to/file.jpg');

// Multiple disks
$manager = new StorageManager();
$manager->createLocalDisk('uploads', '/var/www/uploads', 'https://example.com/uploads');
$manager->createLocalDisk('temp', '/tmp/uploads');

$manager->disk('uploads')->write('file.jpg', $contents);
```

### Logging

```php
<?php

use Farisc0de\PhpFileUploading\Logging\FileLogger;
use Farisc0de\PhpFileUploading\Logging\LogLevel;

$logger = new FileLogger('/var/log/uploads.log', LogLevel::INFO);

$upload->setLogger($logger);
$validator->setLogger($logger);
```

### Image Processing

```php
<?php

use Farisc0de\PhpFileUploading\Image;

$image = new Image();

// Resize
$image->resize('source.jpg', 'thumb.jpg', 200, 200);

// Compress
$image->compress('source.jpg', 'compressed.jpg', 75);

// Add watermark
$image->addWatermark('source.jpg', 'watermark.png', 'output.jpg', 'bottom-right');

// Convert format
$image->convert('source.png', 'output.webp', 'webp', 80);
```

## Configuration

### Filter Configuration (filter.json)

The library uses a JSON configuration file for extension/MIME mappings and size limits:

```json
{
    "extensions": {
        "jpg": "image/jpeg",
        "png": "image/png",
        "pdf": "application/pdf"
    },
    "forbidden": [
        "shell.php",
        "backdoor.php",
        ".htaccess"
    ],
    "size_limits": {
        "image": "10 MB",
        "document": "20 MB",
        "video": "100 MB",
        "default": "50 MB"
    },
    "categories": {
        "images": ["image/jpeg", "image/png", "image/gif"],
        "documents": ["application/pdf", "application/msword"]
    }
}
```

## Exception Handling

The library provides specific exception types for different error scenarios:

```php
use Farisc0de\PhpFileUploading\Exception\ValidationException;
use Farisc0de\PhpFileUploading\Exception\StorageException;
use Farisc0de\PhpFileUploading\Exception\FileNotFoundException;
use Farisc0de\PhpFileUploading\Exception\ConfigurationException;
use Farisc0de\PhpFileUploading\Exception\ImageException;

try {
    $upload->upload();
} catch (ValidationException $e) {
    // Handle validation errors
    echo "Validation failed: " . $e->getMessage();
    echo "Type: " . $e->getValidationType();
    print_r($e->getContext());
} catch (StorageException $e) {
    // Handle storage errors
} catch (FileNotFoundException $e) {
    // Handle missing files
}
```

## Testing

```bash
# Run tests
composer test

# Run tests with coverage
composer test-coverage

# Static analysis
composer phpstan

# Code style check
composer cs-check
```

## Directory Structure

```
src/
├── Exception/          # Custom exception classes
├── Events/             # Event system
├── Logging/            # PSR-3 logging
├── RateLimiting/       # Rate limiting
├── Security/           # Virus scanning
├── Storage/            # Storage abstraction
├── Validation/         # Validation system
│   └── Validators/     # Individual validators
├── File.php            # File representation
├── Image.php           # Image processing
├── Upload.php          # Main upload handler
├── Utility.php         # Helper functions
└── filter.json         # Default filter config
```

## Security Best Practices

1. **Always validate file types** using both extension and MIME type checking
2. **Use hashed filenames** to prevent overwriting and path traversal
3. **Store uploads outside web root** when possible
4. **Enable virus scanning** in production environments
5. **Implement rate limiting** to prevent abuse
6. **Set appropriate file permissions** (0644 for files, 0755 for directories)
7. **Use HTTPS** for file uploads
8. **Validate image dimensions** to prevent resource exhaustion

## License

MIT License - see [LICENSE](LICENSE) file for details.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## Credits

Developed by [FarisCode](https://github.com/farisc0de)
