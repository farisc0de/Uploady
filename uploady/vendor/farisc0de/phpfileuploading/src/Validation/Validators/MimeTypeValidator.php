<?php

namespace Farisc0de\PhpFileUploading\Validation\Validators;

use Farisc0de\PhpFileUploading\File;
use Farisc0de\PhpFileUploading\Validation\ValidatorInterface;
use Farisc0de\PhpFileUploading\Validation\ValidationResult;

/**
 * Validates file MIME types
 *
 * @package PhpFileUploading
 */
class MimeTypeValidator implements ValidatorInterface
{
    private array $allowedMimes;
    private array $extensionMimeMap;
    private bool $strictMode;

    public function __construct(
        array $allowedMimes = [],
        array $extensionMimeMap = [],
        bool $strictMode = true
    ) {
        $this->allowedMimes = $allowedMimes;
        $this->extensionMimeMap = $extensionMimeMap;
        $this->strictMode = $strictMode;
    }

    public function validate(File $file): ValidationResult
    {
        try {
            $mime = $file->getMime();
        } catch (\Exception $e) {
            return ValidationResult::failure(
                'Failed to determine MIME type',
                'MIME_DETECTION_FAILED',
                ['error' => $e->getMessage()]
            );
        }

        // Check against allowed MIME types
        if (!empty($this->allowedMimes) && !in_array($mime, $this->allowedMimes, true)) {
            return ValidationResult::failure(
                "MIME type '{$mime}' is not allowed",
                'INVALID_MIME',
                ['mime' => $mime, 'allowed' => $this->allowedMimes]
            );
        }

        // In strict mode, verify MIME matches expected for extension
        if ($this->strictMode && !empty($this->extensionMimeMap)) {
            $extension = strtolower($file->getExtension());
            $expectedMime = $this->extensionMimeMap[$extension] ?? null;

            if ($expectedMime !== null && $expectedMime !== $mime) {
                return ValidationResult::failure(
                    "MIME type '{$mime}' does not match expected type '{$expectedMime}' for extension '{$extension}'",
                    'MIME_MISMATCH',
                    ['mime' => $mime, 'expected' => $expectedMime, 'extension' => $extension]
                );
            }
        }

        return ValidationResult::success(['mime' => $mime]);
    }

    public function getName(): string
    {
        return 'mime_type';
    }

    /**
     * Set allowed MIME types
     */
    public function setAllowedMimes(array $mimes): self
    {
        $this->allowedMimes = $mimes;
        return $this;
    }

    /**
     * Set extension to MIME type mapping
     */
    public function setExtensionMimeMap(array $map): self
    {
        $this->extensionMimeMap = $map;
        return $this;
    }

    /**
     * Enable or disable strict mode
     */
    public function setStrictMode(bool $strict): self
    {
        $this->strictMode = $strict;
        return $this;
    }
}
