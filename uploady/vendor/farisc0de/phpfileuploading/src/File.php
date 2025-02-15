<?php

namespace Farisc0de\PhpFileUploading;

use RuntimeException;
use InvalidArgumentException;
use finfo;

/**
 * File Representation Class
 *
 * This class provides a clean interface for handling file uploads in PHP.
 * It wraps the $_FILES array and provides methods for accessing file information
 * and performing common file operations.
 *
 * @version 1.5.3
 * @category File_Upload
 * @package PhpFileUploading
 * @author fariscode <farisksa79@gmail.com>
 * @license MIT
 * @link https://github.com/farisc0de/PhpFileUploading
 */
class File
{
    /** @var array<string, mixed> */
    private array $file;

    private Utility $utility;

    /**
     * List of valid upload error codes and their descriptions
     */
    private const UPLOAD_ERRORS = [
        UPLOAD_ERR_OK => 'File uploaded successfully',
        UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize directive in php.ini',
        UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE directive specified in the HTML form',
        UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
        UPLOAD_ERR_NO_FILE => 'No file was uploaded',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
        UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload'
    ];

    /**
     * Class Constructor to initialize attributes
     *
     * @param array<string, mixed> $file An array of the upload file information from $_FILES
     * @param Utility $utility Utility class instance for helper functions
     * @throws InvalidArgumentException If the file array is invalid or missing required keys
     */
    public function __construct(array $file, Utility $utility)
    {
        $this->validateFileArray($file);
        $this->file = $file;
        $this->utility = $utility;
    }

    /**
     * Validates the file array structure
     *
     * @param array<string, mixed> $file The file array to validate
     * @throws InvalidArgumentException If the file array is invalid
     */
    private function validateFileArray(array $file): void
    {
        $required_keys = ['name', 'type', 'tmp_name', 'error', 'size'];
        
        foreach ($required_keys as $key) {
            if (!isset($file[$key])) {
                throw new InvalidArgumentException("Missing required key '{$key}' in file array");
            }
        }

        if (!is_string($file['name']) || !is_string($file['type']) || !is_string($file['tmp_name'])) {
            throw new InvalidArgumentException('Invalid file array structure');
        }

        if (!is_int($file['error'])) {
            throw new InvalidArgumentException('Invalid error code in file array');
        }

        if ($file['error'] !== UPLOAD_ERR_OK && $file['error'] !== UPLOAD_ERR_NO_FILE) {
            throw new RuntimeException(self::UPLOAD_ERRORS[$file['error']] ?? 'Unknown upload error');
        }
    }

    /**
     * Return the size of the uploaded file in bytes
     *
     * @throws RuntimeException If the file size cannot be determined
     * @return int The file size in bytes
     */
    public function getSize(): int
    {
        if ($this->isEmpty()) {
            throw new RuntimeException('Cannot get size of empty file');
        }

        return $this->utility->fixintOverflow($this->file['size']);
    }

    /**
     * Get the file extension
     *
     * @return string The file extension in lowercase
     */
    public function getExtension(): string
    {
        return strtolower(pathinfo($this->getName(), PATHINFO_EXTENSION));
    }

    /**
     * Get the MIME type using fileinfo
     *
     * @throws RuntimeException If MIME type cannot be determined
     * @return string The file's MIME type
     */
    public function getMime(): string
    {
        if ($this->isEmpty()) {
            throw new RuntimeException('Cannot get MIME type of empty file');
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime_type = $finfo->file($this->getTempName());

        if ($mime_type === false) {
            throw new RuntimeException('Failed to determine MIME type');
        }

        return $mime_type;
    }

    /**
     * Get the client-provided MIME type
     *
     * Note: This value comes from the client and should not be trusted for security purposes.
     * Use getMime() instead for a server-side determined MIME type.
     *
     * @return string The client-provided MIME type
     */
    public function getFileType(): string
    {
        return $this->file['type'];
    }

    /**
     * Get the original filename
     *
     * @return string The original filename
     */
    public function getName(): string
    {
        return $this->file['name'];
    }

    /**
     * Get the temporary filename
     *
     * @throws RuntimeException If the temporary file does not exist
     * @return string The temporary filename
     */
    public function getTempName(): string
    {
        $temp_name = $this->file['tmp_name'];
        
        if (!file_exists($temp_name)) {
            throw new RuntimeException('Temporary file does not exist');
        }

        return $temp_name;
    }

    /**
     * Check if the file upload is empty
     *
     * @return bool True if no file was uploaded, false otherwise
     */
    public function isEmpty(): bool
    {
        return $this->file['error'] === UPLOAD_ERR_NO_FILE;
    }

    /**
     * Get the file's last modification time
     *
     * @throws RuntimeException If the modification time cannot be determined
     * @return int Unix timestamp of the file's last modification time
     */
    public function getDate(): int
    {
        if ($this->isEmpty()) {
            throw new RuntimeException('Cannot get date of empty file');
        }

        $mtime = filemtime($this->getTempName());
        
        if ($mtime === false) {
            throw new RuntimeException('Failed to get file modification time');
        }

        return $mtime;
    }

    /**
     * Get the file's SHA-256 hash
     *
     * @throws RuntimeException If the file hash cannot be calculated
     * @return string The file's SHA-256 hash
     */
    public function getFileHash(): string
    {
        if ($this->isEmpty()) {
            throw new RuntimeException('Cannot get hash of empty file');
        }

        $hash = hash_file('sha256', $this->getTempName());
        
        if ($hash === false) {
            throw new RuntimeException('Failed to calculate file hash');
        }

        return $hash;
    }

    /**
     * Get the upload error message if any
     *
     * @return string|null The error message or null if no error
     */
    public function getError(): ?string
    {
        return $this->file['error'] !== UPLOAD_ERR_OK 
            ? (self::UPLOAD_ERRORS[$this->file['error']] ?? 'Unknown upload error')
            : null;
    }

    /**
     * Verify the integrity of the uploaded file
     *
     * @throws RuntimeException If the file fails integrity checks
     */
    public function verifyIntegrity(): void
    {
        if ($this->isEmpty()) {
            throw new RuntimeException('Cannot verify integrity of empty file');
        }

        if (!is_uploaded_file($this->getTempName())) {
            throw new RuntimeException('File was not uploaded via HTTP POST');
        }

        if (!is_readable($this->getTempName())) {
            throw new RuntimeException('Uploaded file is not readable');
        }
    }
}
