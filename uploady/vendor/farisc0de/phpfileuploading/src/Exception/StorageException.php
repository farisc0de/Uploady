<?php

namespace Farisc0de\PhpFileUploading\Exception;

/**
 * Exception thrown when storage operations fail
 *
 * @package PhpFileUploading
 */
class StorageException extends UploadException
{
    public const CODE_WRITE_FAILED = 2001;
    public const CODE_READ_FAILED = 2002;
    public const CODE_DELETE_FAILED = 2003;
    public const CODE_MOVE_FAILED = 2004;
    public const CODE_COPY_FAILED = 2005;
    public const CODE_DIRECTORY_CREATE_FAILED = 2006;
    public const CODE_PERMISSION_DENIED = 2007;
    public const CODE_DISK_FULL = 2008;
    public const CODE_FILE_EXISTS = 2009;

    /**
     * Create exception for write failure
     */
    public static function writeFailed(string $path, ?string $reason = null): self
    {
        $message = "Failed to write file: {$path}";
        if ($reason) {
            $message .= " ({$reason})";
        }

        return new self(
            $message,
            self::CODE_WRITE_FAILED,
            null,
            ['path' => $path, 'reason' => $reason]
        );
    }

    /**
     * Create exception for read failure
     */
    public static function readFailed(string $path, ?string $reason = null): self
    {
        $message = "Failed to read file: {$path}";
        if ($reason) {
            $message .= " ({$reason})";
        }

        return new self(
            $message,
            self::CODE_READ_FAILED,
            null,
            ['path' => $path, 'reason' => $reason]
        );
    }

    /**
     * Create exception for delete failure
     */
    public static function deleteFailed(string $path, ?string $reason = null): self
    {
        $message = "Failed to delete file: {$path}";
        if ($reason) {
            $message .= " ({$reason})";
        }

        return new self(
            $message,
            self::CODE_DELETE_FAILED,
            null,
            ['path' => $path, 'reason' => $reason]
        );
    }

    /**
     * Create exception for move failure
     */
    public static function moveFailed(string $source, string $destination, ?string $reason = null): self
    {
        $message = "Failed to move file from {$source} to {$destination}";
        if ($reason) {
            $message .= " ({$reason})";
        }

        return new self(
            $message,
            self::CODE_MOVE_FAILED,
            null,
            ['source' => $source, 'destination' => $destination, 'reason' => $reason]
        );
    }

    /**
     * Create exception for copy failure
     */
    public static function copyFailed(string $source, string $destination, ?string $reason = null): self
    {
        $message = "Failed to copy file from {$source} to {$destination}";
        if ($reason) {
            $message .= " ({$reason})";
        }

        return new self(
            $message,
            self::CODE_COPY_FAILED,
            null,
            ['source' => $source, 'destination' => $destination, 'reason' => $reason]
        );
    }

    /**
     * Create exception for directory creation failure
     */
    public static function directoryCreateFailed(string $path, ?string $reason = null): self
    {
        $message = "Failed to create directory: {$path}";
        if ($reason) {
            $message .= " ({$reason})";
        }

        return new self(
            $message,
            self::CODE_DIRECTORY_CREATE_FAILED,
            null,
            ['path' => $path, 'reason' => $reason]
        );
    }

    /**
     * Create exception for permission denied
     */
    public static function permissionDenied(string $path, string $operation): self
    {
        return new self(
            "Permission denied for {$operation} on: {$path}",
            self::CODE_PERMISSION_DENIED,
            null,
            ['path' => $path, 'operation' => $operation]
        );
    }

    /**
     * Create exception for disk full
     */
    public static function diskFull(string $path): self
    {
        return new self(
            "Disk is full, cannot write to: {$path}",
            self::CODE_DISK_FULL,
            null,
            ['path' => $path]
        );
    }

    /**
     * Create exception for file already exists
     */
    public static function fileExists(string $path): self
    {
        return new self(
            "File already exists: {$path}",
            self::CODE_FILE_EXISTS,
            null,
            ['path' => $path]
        );
    }
}
