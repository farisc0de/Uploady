<?php

namespace Farisc0de\PhpFileUploading\Events;

use Farisc0de\PhpFileUploading\File;
use Farisc0de\PhpFileUploading\Security\ScanResult;

/**
 * Event for virus scan operations
 *
 * @package PhpFileUploading
 */
class ScanEvent extends FileEvent
{
    protected ?ScanResult $scanResult = null;

    public function __construct(string $name, ?File $file = null, ?ScanResult $scanResult = null, array $data = [])
    {
        parent::__construct($name, $file, $data);
        $this->scanResult = $scanResult;
    }

    /**
     * Get the scan result
     */
    public function getScanResult(): ?ScanResult
    {
        return $this->scanResult;
    }

    /**
     * Set the scan result
     */
    public function setScanResult(ScanResult $result): self
    {
        $this->scanResult = $result;
        return $this;
    }

    /**
     * Check if file is clean
     */
    public function isClean(): bool
    {
        return $this->scanResult?->isClean() ?? false;
    }

    /**
     * Check if file is infected
     */
    public function isInfected(): bool
    {
        return $this->scanResult?->isInfected() ?? false;
    }

    /**
     * Get virus name if infected
     */
    public function getVirusName(): ?string
    {
        return $this->scanResult?->getVirusName();
    }
}
