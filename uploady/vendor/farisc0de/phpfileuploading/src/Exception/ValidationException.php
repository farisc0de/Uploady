<?php

namespace Farisc0de\PhpFileUploading\Exception;

/**
 * Exception thrown when file validation fails
 *
 * @package PhpFileUploading
 */
class ValidationException extends UploadException
{
    public const CODE_INVALID_EXTENSION = 1001;
    public const CODE_INVALID_MIME = 1002;
    public const CODE_FILE_TOO_LARGE = 1003;
    public const CODE_FILE_TOO_SMALL = 1004;
    public const CODE_FORBIDDEN_NAME = 1005;
    public const CODE_EMPTY_FILE = 1006;
    public const CODE_INVALID_DIMENSIONS = 1007;
    public const CODE_NOT_AN_IMAGE = 1008;
    public const CODE_VIRUS_DETECTED = 1009;
    public const CODE_RATE_LIMIT_EXCEEDED = 1010;

    protected string $validationType;

    public function __construct(
        string $message,
        int $code,
        string $validationType,
        array $context = [],
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous, $context);
        $this->validationType = $validationType;
    }

    /**
     * Get the type of validation that failed
     */
    public function getValidationType(): string
    {
        return $this->validationType;
    }

    /**
     * Create exception for invalid extension
     */
    public static function invalidExtension(string $extension, array $allowed = []): self
    {
        return new self(
            "Invalid file extension: {$extension}",
            self::CODE_INVALID_EXTENSION,
            'extension',
            ['extension' => $extension, 'allowed' => $allowed]
        );
    }

    /**
     * Create exception for invalid MIME type
     */
    public static function invalidMime(string $mime, ?string $expected = null): self
    {
        $message = "Invalid MIME type: {$mime}";
        if ($expected) {
            $message .= " (expected: {$expected})";
        }

        return new self(
            $message,
            self::CODE_INVALID_MIME,
            'mime',
            ['mime' => $mime, 'expected' => $expected]
        );
    }

    /**
     * Create exception for file too large
     */
    public static function fileTooLarge(int $size, int $maxSize): self
    {
        return new self(
            "File size ({$size} bytes) exceeds maximum allowed size ({$maxSize} bytes)",
            self::CODE_FILE_TOO_LARGE,
            'size',
            ['size' => $size, 'max_size' => $maxSize]
        );
    }

    /**
     * Create exception for file too small
     */
    public static function fileTooSmall(int $size, int $minSize): self
    {
        return new self(
            "File size ({$size} bytes) is below minimum required size ({$minSize} bytes)",
            self::CODE_FILE_TOO_SMALL,
            'size',
            ['size' => $size, 'min_size' => $minSize]
        );
    }

    /**
     * Create exception for forbidden filename
     */
    public static function forbiddenName(string $filename): self
    {
        return new self(
            "Forbidden filename: {$filename}",
            self::CODE_FORBIDDEN_NAME,
            'forbidden',
            ['filename' => $filename]
        );
    }

    /**
     * Create exception for empty file
     */
    public static function emptyFile(): self
    {
        return new self(
            "No file was uploaded or file is empty",
            self::CODE_EMPTY_FILE,
            'empty',
            []
        );
    }

    /**
     * Create exception for invalid image dimensions
     */
    public static function invalidDimensions(
        int $width,
        int $height,
        ?int $maxWidth = null,
        ?int $maxHeight = null,
        ?int $minWidth = null,
        ?int $minHeight = null
    ): self {
        return new self(
            "Invalid image dimensions: {$width}x{$height}",
            self::CODE_INVALID_DIMENSIONS,
            'dimensions',
            [
                'width' => $width,
                'height' => $height,
                'max_width' => $maxWidth,
                'max_height' => $maxHeight,
                'min_width' => $minWidth,
                'min_height' => $minHeight
            ]
        );
    }

    /**
     * Create exception for non-image file
     */
    public static function notAnImage(string $mime): self
    {
        return new self(
            "File is not a valid image (MIME: {$mime})",
            self::CODE_NOT_AN_IMAGE,
            'image',
            ['mime' => $mime]
        );
    }

    /**
     * Create exception for virus detected
     */
    public static function virusDetected(string $filename, ?string $virusName = null): self
    {
        $message = "Virus detected in file: {$filename}";
        if ($virusName) {
            $message .= " ({$virusName})";
        }

        return new self(
            $message,
            self::CODE_VIRUS_DETECTED,
            'virus',
            ['filename' => $filename, 'virus_name' => $virusName]
        );
    }

    /**
     * Create exception for rate limit exceeded
     */
    public static function rateLimitExceeded(string $identifier, int $limit, int $window): self
    {
        return new self(
            "Rate limit exceeded for {$identifier}: {$limit} uploads per {$window} seconds",
            self::CODE_RATE_LIMIT_EXCEEDED,
            'rate_limit',
            ['identifier' => $identifier, 'limit' => $limit, 'window' => $window]
        );
    }
}
