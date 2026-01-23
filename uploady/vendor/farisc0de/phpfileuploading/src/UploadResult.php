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
    private ?string $siteUrl = null;
    private ?string $userId = null;
    private ?string $fileId = null;
    private ?string $hashId = null;
    private ?string $folderName = null;

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
     * Set site URL for link generation
     */
    public function setSiteUrl(?string $url): self
    {
        $this->siteUrl = $url ? rtrim($url, '/') : null;
        return $this;
    }

    /**
     * Get site URL
     */
    public function getSiteUrl(): ?string
    {
        return $this->siteUrl;
    }

    /**
     * Set user ID
     */
    public function setUserId(?string $userId): self
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * Get user ID
     */
    public function getUserId(): ?string
    {
        return $this->userId;
    }

    /**
     * Set file ID
     */
    public function setFileId(?string $fileId): self
    {
        $this->fileId = $fileId;
        return $this;
    }

    /**
     * Get file ID
     */
    public function getFileId(): ?string
    {
        return $this->fileId;
    }

    /**
     * Set hash ID
     */
    public function setHashId(?string $hashId): self
    {
        $this->hashId = $hashId;
        return $this;
    }

    /**
     * Get hash ID
     */
    public function getHashId(): ?string
    {
        return $this->hashId;
    }

    /**
     * Set folder name
     */
    public function setFolderName(?string $folderName): self
    {
        $this->folderName = $folderName;
        return $this;
    }

    /**
     * Get folder name
     */
    public function getFolderName(): ?string
    {
        return $this->folderName;
    }

    /**
     * Generate QR code URL for the download link
     */
    public function getQrCode(int $size = 150): ?string
    {
        $downloadLink = $this->getDownloadLink();
        if ($downloadLink === null) {
            return null;
        }

        return sprintf(
            "https://quickchart.io/qr?text=%s&size=%d",
            urlencode($downloadLink),
            $size
        );
    }

    /**
     * Generate download link
     */
    public function getDownloadLink(): ?string
    {
        if (empty($this->siteUrl) || empty($this->fileId)) {
            return null;
        }

        return sprintf(
            "%s/download.php?file_id=%s",
            $this->siteUrl,
            urlencode($this->fileId)
        );
    }

    /**
     * Generate direct download link
     */
    public function getDirectLink(): ?string
    {
        if (empty($this->siteUrl) || empty($this->folderName) || empty($this->storedFilename)) {
            return null;
        }

        return sprintf(
            "%s/%s/%s",
            $this->siteUrl,
            $this->folderName,
            $this->storedFilename
        );
    }

    /**
     * Generate delete link
     */
    public function getDeleteLink(): ?string
    {
        if (empty($this->siteUrl) || empty($this->userId) || empty($this->fileId)) {
            return null;
        }

        return sprintf(
            "%s/delete.php?file_id=%s&user_id=%s",
            $this->siteUrl,
            urlencode($this->fileId),
            urlencode($this->userId)
        );
    }

    /**
     * Generate edit link
     */
    public function getEditLink(): ?string
    {
        if (empty($this->siteUrl) || empty($this->userId) || empty($this->fileId)) {
            return null;
        }

        return sprintf(
            "%s/edit.php?file_id=%s&user_id=%s",
            $this->siteUrl,
            urlencode($this->fileId),
            urlencode($this->userId)
        );
    }

    /**
     * Check if link generation is available
     */
    public function hasLinks(): bool
    {
        return !empty($this->siteUrl);
    }

    /**
     * Get all generated links
     */
    public function getLinks(): array
    {
        if (!$this->hasLinks()) {
            return [];
        }

        return array_filter([
            'qrcode' => $this->getQrCode(),
            'download' => $this->getDownloadLink(),
            'direct' => $this->getDirectLink(),
            'delete' => $this->getDeleteLink(),
            'edit' => $this->getEditLink(),
        ]);
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
            'links' => $this->getLinks(),
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
