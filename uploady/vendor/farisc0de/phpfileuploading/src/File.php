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

        // Validate data types for each key
        if (!is_string($file['name'])) {
            throw new InvalidArgumentException('File name must be a string');
        }
        
        if (!is_string($file['type'])) {
            throw new InvalidArgumentException('File type must be a string');
        }
        
        if (!is_string($file['tmp_name'])) {
            throw new InvalidArgumentException('Temporary file name must be a string');
        }
        
        if (!is_numeric($file['size'])) {
            throw new InvalidArgumentException('File size must be numeric');
        }

        if (!is_int($file['error'])) {
            throw new InvalidArgumentException('Invalid error code in file array');
        }

        // Check if error code is valid
        if (!array_key_exists($file['error'], self::UPLOAD_ERRORS)) {
            throw new InvalidArgumentException('Unknown upload error code');
        }

        // Throw exception for upload errors except UPLOAD_ERR_OK and UPLOAD_ERR_NO_FILE
        if ($file['error'] !== UPLOAD_ERR_OK && $file['error'] !== UPLOAD_ERR_NO_FILE) {
            throw new RuntimeException(self::UPLOAD_ERRORS[$file['error']]);
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

        $size = $this->utility->fixintOverflow($this->file['size']);
        
        // Double-check size by reading the file directly if possible
        if (file_exists($this->file['tmp_name']) && is_readable($this->file['tmp_name'])) {
            $filesize = filesize($this->file['tmp_name']);
            if ($filesize !== false && $filesize !== $size) {
                // Log the discrepancy but use the filesize result as it's more reliable
                error_log("Size discrepancy detected: {$size} vs {$filesize}");
                return $filesize;
            }
        }
        
        return $size;
    }

    /**
     * Get the file extension
     *
     * @return string The file extension in lowercase
     */
    public function getExtension(): string
    {
        $extension = pathinfo($this->getName(), PATHINFO_EXTENSION);
        return $extension !== '' ? strtolower($extension) : '';
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

        $tempName = $this->getTempName();
        
        // Try using finfo extension
        if (class_exists('finfo')) {
            try {
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mime_type = $finfo->file($tempName);

                if ($mime_type !== false) {
                    return $mime_type;
                }
            } catch (\Exception $e) {
                // Fall through to alternative methods
            }
        }
        
        // Try mime_content_type function
        if (function_exists('mime_content_type')) {
            $mime_type = mime_content_type($tempName);
            if ($mime_type !== false) {
                return $mime_type;
            }
        }
        
        // Last resort: use a mapping of extensions to MIME types
        $extension = $this->getExtension();
        $mime_map = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'zip' => 'application/zip',
            'txt' => 'text/plain',
            'html' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
        ];
        
        if (isset($mime_map[$extension])) {
            return $mime_map[$extension];
        }
        
        throw new RuntimeException('Failed to determine MIME type');
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
        if ($this->isEmpty()) {
            throw new RuntimeException('Cannot get temporary name of empty file');
        }
        
        $temp_name = $this->file['tmp_name'];
        
        if (!file_exists($temp_name)) {
            throw new RuntimeException('Temporary file does not exist');
        }
        
        if (!is_readable($temp_name)) {
            throw new RuntimeException('Temporary file is not readable');
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
        return $this->file['error'] === UPLOAD_ERR_NO_FILE || 
               empty($this->file['name']) || 
               empty($this->file['tmp_name']) || 
               $this->file['size'] === 0;
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
        
        $tempName = $this->getTempName();
        
        // Check file size before hashing to prevent memory issues with large files
        $fileSize = filesize($tempName);
        if ($fileSize === false) {
            throw new RuntimeException('Failed to determine file size');
        }
        
        // For very large files, use a streaming approach
        if ($fileSize > 10 * 1024 * 1024) { // 10MB threshold
            $context = hash_init('sha256');
            $handle = fopen($tempName, 'rb');
            
            if ($handle === false) {
                throw new RuntimeException('Failed to open file for hashing');
            }
            
            try {
                while (!feof($handle)) {
                    $buffer = fread($handle, 8192);
                    if ($buffer === false) {
                        throw new RuntimeException('Failed to read file for hashing');
                    }
                    hash_update($context, $buffer);
                }
                
                return hash_final($context);
            } finally {
                fclose($handle);
            }
        }
        
        // For smaller files, use hash_file
        $hash = hash_file('sha256', $tempName);
        
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
            ? self::UPLOAD_ERRORS[$this->file['error']]
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

        $tempName = $this->getTempName();
        
        // Check if the file was uploaded via HTTP POST
        if (!is_uploaded_file($tempName)) {
            throw new RuntimeException('File was not uploaded via HTTP POST');
        }

        // Check if the file is readable
        if (!is_readable($tempName)) {
            throw new RuntimeException('Uploaded file is not readable');
        }
        
        // Check if the file size is consistent
        $reportedSize = $this->file['size'];
        $actualSize = filesize($tempName);
        
        if ($actualSize === false) {
            throw new RuntimeException('Failed to determine actual file size');
        }
        
        if (abs($reportedSize - $actualSize) > 1024) { // Allow small discrepancy (1KB)
            throw new RuntimeException("File size mismatch: reported {$reportedSize}, actual {$actualSize}");
        }
    }
}
