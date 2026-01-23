<?php

namespace Farisc0de\PhpFileUploading\Exception;

/**
 * Exception thrown when configuration is invalid or missing
 *
 * @package PhpFileUploading
 */
class ConfigurationException extends UploadException
{
    public const CODE_MISSING_CONFIG = 3001;
    public const CODE_INVALID_CONFIG = 3002;
    public const CODE_MISSING_DEPENDENCY = 3003;

    /**
     * Create exception for missing configuration
     */
    public static function missingConfig(string $key): self
    {
        return new self(
            "Missing required configuration: {$key}",
            self::CODE_MISSING_CONFIG,
            null,
            ['key' => $key]
        );
    }

    /**
     * Create exception for invalid configuration
     */
    public static function invalidConfig(string $key, mixed $value, ?string $expected = null): self
    {
        $message = "Invalid configuration value for '{$key}'";
        if ($expected) {
            $message .= " (expected: {$expected})";
        }

        return new self(
            $message,
            self::CODE_INVALID_CONFIG,
            null,
            ['key' => $key, 'value' => $value, 'expected' => $expected]
        );
    }

    /**
     * Create exception for missing dependency
     */
    public static function missingDependency(string $dependency, ?string $purpose = null): self
    {
        $message = "Missing required dependency: {$dependency}";
        if ($purpose) {
            $message .= " (required for: {$purpose})";
        }

        return new self(
            $message,
            self::CODE_MISSING_DEPENDENCY,
            null,
            ['dependency' => $dependency, 'purpose' => $purpose]
        );
    }
}
