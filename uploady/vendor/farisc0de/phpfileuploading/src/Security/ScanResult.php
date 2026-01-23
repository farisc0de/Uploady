<?php

namespace Farisc0de\PhpFileUploading\Security;

/**
 * Represents the result of a virus scan
 *
 * @package PhpFileUploading
 */
class ScanResult
{
    public const STATUS_CLEAN = 'clean';
    public const STATUS_INFECTED = 'infected';
    public const STATUS_ERROR = 'error';
    public const STATUS_SKIPPED = 'skipped';

    private string $status;
    private ?string $virusName;
    private ?string $errorMessage;
    private string $filePath;
    private float $scanTime;
    private array $metadata;

    public function __construct(
        string $status,
        string $filePath,
        ?string $virusName = null,
        ?string $errorMessage = null,
        float $scanTime = 0.0,
        array $metadata = []
    ) {
        $this->status = $status;
        $this->filePath = $filePath;
        $this->virusName = $virusName;
        $this->errorMessage = $errorMessage;
        $this->scanTime = $scanTime;
        $this->metadata = $metadata;
    }

    /**
     * Create a clean result
     */
    public static function clean(string $filePath, float $scanTime = 0.0): self
    {
        return new self(self::STATUS_CLEAN, $filePath, null, null, $scanTime);
    }

    /**
     * Create an infected result
     */
    public static function infected(string $filePath, string $virusName, float $scanTime = 0.0): self
    {
        return new self(self::STATUS_INFECTED, $filePath, $virusName, null, $scanTime);
    }

    /**
     * Create an error result
     */
    public static function error(string $filePath, string $errorMessage): self
    {
        return new self(self::STATUS_ERROR, $filePath, null, $errorMessage);
    }

    /**
     * Create a skipped result
     */
    public static function skipped(string $filePath, string $reason): self
    {
        return new self(self::STATUS_SKIPPED, $filePath, null, $reason);
    }

    /**
     * Check if the file is clean
     */
    public function isClean(): bool
    {
        return $this->status === self::STATUS_CLEAN;
    }

    /**
     * Check if the file is infected
     */
    public function isInfected(): bool
    {
        return $this->status === self::STATUS_INFECTED;
    }

    /**
     * Check if there was an error during scanning
     */
    public function hasError(): bool
    {
        return $this->status === self::STATUS_ERROR;
    }

    /**
     * Check if the scan was skipped
     */
    public function wasSkipped(): bool
    {
        return $this->status === self::STATUS_SKIPPED;
    }

    /**
     * Get the scan status
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Get the virus name (if infected)
     */
    public function getVirusName(): ?string
    {
        return $this->virusName;
    }

    /**
     * Get the error message (if error)
     */
    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    /**
     * Get the scanned file path
     */
    public function getFilePath(): string
    {
        return $this->filePath;
    }

    /**
     * Get the scan time in seconds
     */
    public function getScanTime(): float
    {
        return $this->scanTime;
    }

    /**
     * Get additional metadata
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * Add metadata
     */
    public function addMetadata(string $key, mixed $value): self
    {
        $this->metadata[$key] = $value;
        return $this;
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'status' => $this->status,
            'file_path' => $this->filePath,
            'virus_name' => $this->virusName,
            'error_message' => $this->errorMessage,
            'scan_time' => $this->scanTime,
            'metadata' => $this->metadata,
        ];
    }
}
