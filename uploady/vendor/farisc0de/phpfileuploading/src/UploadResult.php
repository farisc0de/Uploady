<?php

namespace Farisc0de\PhpFileUploading;

use Farisc0de\PhpFileUploading\Validation\ValidationResult;
use Farisc0de\PhpFileUploading\Security\ScanResult;

/**
 * Represents the result of an upload operation
 *
 * @package PhpFileUploading
 */
class UploadResult
{
    private bool $success = false;
    private ?string $error = null;
    private ?int $errorCode = null;
    private ?string $originalFilename = null;
    private ?string $storedFilename = null;
    private ?string $storedPath = null;
    private ?string $publicUrl = null;
    private ?int $fileSize = null;
    private ?string $mimeType = null;
    private ?string $fileHash = null;
    private ?ValidationResult $validationResult = null;
    private ?ScanResult $scanResult = null;
    private array $rateLimitHeaders = [];
    private array $metadata = [];

    /**
     * Check if upload was successful
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * Check if upload failed
     */
    public function isFailed(): bool
    {
        return !$this->success;
    }

    /**
     * Set success status
     */
    public function setSuccess(bool $success): self
    {
        $this->success = $success;
        return $this;
    }

    /**
     * Get error message
     */
    public function getError(): ?string
    {
        return $this->error;
    }

    /**
     * Set error message
     */
    public function setError(?string $error): self
    {
        $this->error = $error;
        return $this;
    }

    /**
     * Get error code
     */
    public function getErrorCode(): ?int
    {
        return $this->errorCode;
    }

    /**
     * Set error code
     */
    public function setErrorCode(?int $code): self
    {
        $this->errorCode = $code;
        return $this;
    }

    /**
     * Get original filename
     */
    public function getOriginalFilename(): ?string
    {
        return $this->originalFilename;
    }

    /**
     * Set original filename
     */
    public function setOriginalFilename(?string $filename): self
    {
        $this->originalFilename = $filename;
        return $this;
    }

    /**
     * Get stored filename
     */
    public function getStoredFilename(): ?string
    {
        return $this->storedFilename;
    }

    /**
     * Set stored filename
     */
    public function setStoredFilename(?string $filename): self
    {
        $this->storedFilename = $filename;
        return $this;
    }

    /**
     * Get stored path
     */
    public function getStoredPath(): ?string
    {
        return $this->storedPath;
    }

    /**
     * Set stored path
     */
    public function setStoredPath(?string $path): self
    {
        $this->storedPath = $path;
        return $this;
    }

    /**
     * Get public URL
     */
    public function getPublicUrl(): ?string
    {
        return $this->publicUrl;
    }

    /**
     * Set public URL
     */
    public function setPublicUrl(?string $url): self
    {
        $this->publicUrl = $url;
        return $this;
    }

    /**
     * Get file size
     */
    public function getFileSize(): ?int
    {
        return $this->fileSize;
    }

    /**
     * Set file size
     */
    public function setFileSize(?int $size): self
    {
        $this->fileSize = $size;
        return $this;
    }

    /**
     * Get MIME type
     */
    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    /**
     * Set MIME type
     */
    public function setMimeType(?string $mimeType): self
    {
        $this->mimeType = $mimeType;
        return $this;
    }

    /**
     * Get file hash
     */
    public function getFileHash(): ?string
    {
        return $this->fileHash;
    }

    /**
     * Set file hash
     */
    public function setFileHash(?string $hash): self
    {
        $this->fileHash = $hash;
        return $this;
    }

    /**
     * Get validation result
     */
    public function getValidationResult(): ?ValidationResult
    {
        return $this->validationResult;
    }

    /**
     * Set validation result
     */
    public function setValidationResult(?ValidationResult $result): self
    {
        $this->validationResult = $result;
        return $this;
    }

    /**
     * Get scan result
     */
    public function getScanResult(): ?ScanResult
    {
        return $this->scanResult;
    }

    /**
     * Set scan result
     */
    public function setScanResult(?ScanResult $result): self
    {
        $this->scanResult = $result;
        return $this;
    }

    /**
     * Get rate limit headers
     */
    public function getRateLimitHeaders(): array
    {
        return $this->rateLimitHeaders;
    }

    /**
     * Set rate limit headers
     */
    public function setRateLimitHeaders(array $headers): self
    {
        $this->rateLimitHeaders = $headers;
        return $this;
    }

    /**
     * Get metadata
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * Set metadata
     */
    public function setMetadata(array $metadata): self
    {
        $this->metadata = $metadata;
        return $this;
    }

    /**
     * Add metadata item
     */
    public function addMetadata(string $key, mixed $value): self
    {
        $this->metadata[$key] = $value;
        return $this;
    }

    /**
     * Get metadata value
     */
    public function getMetadataValue(string $key, mixed $default = null): mixed
    {
        return $this->metadata[$key] ?? $default;
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'error' => $this->error,
            'error_code' => $this->errorCode,
            'original_filename' => $this->originalFilename,
            'stored_filename' => $this->storedFilename,
            'stored_path' => $this->storedPath,
            'public_url' => $this->publicUrl,
            'file_size' => $this->fileSize,
            'mime_type' => $this->mimeType,
            'file_hash' => $this->fileHash,
            'validation' => $this->validationResult?->toArray(),
            'scan' => $this->scanResult?->toArray(),
            'metadata' => $this->metadata,
        ];
    }

    /**
     * Convert to JSON
     */
    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_THROW_ON_ERROR);
    }
}
