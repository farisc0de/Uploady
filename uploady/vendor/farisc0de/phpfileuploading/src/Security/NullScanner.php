<?php

namespace Farisc0de\PhpFileUploading\Security;

/**
 * Null virus scanner that always returns clean
 * 
 * Use this when virus scanning is not required or not available.
 *
 * @package PhpFileUploading
 */
class NullScanner implements VirusScannerInterface
{
    public function scan(string $filePath): ScanResult
    {
        return ScanResult::skipped($filePath, 'Virus scanning disabled');
    }

    public function isAvailable(): bool
    {
        return true;
    }

    public function getVersion(): string
    {
        return 'NullScanner (no scanning)';
    }
}
