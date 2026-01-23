<?php

namespace Farisc0de\PhpFileUploading\Exception;

/**
 * Exception thrown when image processing fails
 *
 * @package PhpFileUploading
 */
class ImageException extends UploadException
{
    public const CODE_PROCESSING_FAILED = 4001;
    public const CODE_UNSUPPORTED_FORMAT = 4002;
    public const CODE_CORRUPT_IMAGE = 4003;
    public const CODE_GD_NOT_AVAILABLE = 4004;
    public const CODE_MEMORY_EXCEEDED = 4005;

    /**
     * Create exception for processing failure
     */
    public static function processingFailed(string $operation, ?string $reason = null): self
    {
        $message = "Image processing failed during: {$operation}";
        if ($reason) {
            $message .= " ({$reason})";
        }

        return new self(
            $message,
            self::CODE_PROCESSING_FAILED,
            null,
            ['operation' => $operation, 'reason' => $reason]
        );
    }

    /**
     * Create exception for unsupported format
     */
    public static function unsupportedFormat(string $format, array $supported = []): self
    {
        return new self(
            "Unsupported image format: {$format}",
            self::CODE_UNSUPPORTED_FORMAT,
            null,
            ['format' => $format, 'supported' => $supported]
        );
    }

    /**
     * Create exception for corrupt image
     */
    public static function corruptImage(string $path): self
    {
        return new self(
            "Image file is corrupt or invalid: {$path}",
            self::CODE_CORRUPT_IMAGE,
            null,
            ['path' => $path]
        );
    }

    /**
     * Create exception for GD not available
     */
    public static function gdNotAvailable(): self
    {
        return new self(
            "GD extension is not available. Please install or enable it.",
            self::CODE_GD_NOT_AVAILABLE,
            null,
            []
        );
    }

    /**
     * Create exception for memory exceeded
     */
    public static function memoryExceeded(int $required, int $available): self
    {
        return new self(
            "Insufficient memory for image processing. Required: {$required} bytes, Available: {$available} bytes",
            self::CODE_MEMORY_EXCEEDED,
            null,
            ['required' => $required, 'available' => $available]
        );
    }
}
