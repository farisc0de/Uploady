<?php

namespace Farisc0de\PhpFileUploading\Security;

/**
 * Interface for virus scanning implementations
 *
 * @package PhpFileUploading
 */
interface VirusScannerInterface
{
    /**
     * Scan a file for viruses
     *
     * @param string $filePath Path to the file to scan
     * @return ScanResult The scan result
     */
    public function scan(string $filePath): ScanResult;

    /**
     * Check if the scanner is available and properly configured
     *
     * @return bool True if scanner is available
     */
    public function isAvailable(): bool;

    /**
     * Get the scanner name/version
     *
     * @return string Scanner identification
     */
    public function getVersion(): string;
}
