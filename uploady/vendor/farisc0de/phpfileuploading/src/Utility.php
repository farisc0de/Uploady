<?php

namespace Farisc0de\PhpFileUploading;

use InvalidArgumentException;
use RuntimeException;

/**
 * Utility Class for File Operations
 *
 * This class provides utility functions for file operations, size conversions,
 * and security measures.
 *
 * @version 2.6.0
 * @category File_Upload
 * @package PhpFileUploading
 * @author fariscode <farisksa79@gmail.com>
 * @license MIT
 * @link https://github.com/farisc0de/PhpFileUploading
 */
class Utility
{
    /**
     * Storage units and their power values
     */
    private const STORAGE_UNITS = [
        'B' => 0,
        'KB' => 1,
        'MB' => 2,
        'GB' => 3,
        'TB' => 4
    ];

    /**
     * Default file permissions
     */
    private const DEFAULT_FILE_PERMISSIONS = 0644;
    private const DEFAULT_DIR_PERMISSIONS = 0755;

    /**
     * Fix integer overflow for large file sizes
     *
     * @param int|float $value The value to fix
     * @return float The corrected value
     * @throws InvalidArgumentException If the input is not numeric
     */
    public function fixIntOverflow(int|float $value): float
    {
        if (!is_numeric($value)) {
            throw new InvalidArgumentException('Input must be numeric');
        }

        if ($value < 0) {
            $value += 2.0 * (PHP_INT_MAX + 1);
        }

        return (float)$value;
    }

    /**
     * Convert human-readable size to bytes
     *
     * @param string $size Size string (e.g., "7.2 MB")
     * @return float Size in bytes
     * @throws InvalidArgumentException If the size format is invalid
     */
    public function sizeInBytes(string $size): float
    {
        $matches = [];
        if (!preg_match('/^(?<size>[\d.]+)\s*(?<unit>[BKMGT]B?)?$/i', trim($size), $matches)) {
            throw new InvalidArgumentException('Invalid size format. Expected format: "7.2 MB"');
        }

        $sizeValue = (float)$matches['size'];
        if ($sizeValue < 0) {
            throw new InvalidArgumentException('Size value must be non-negative');
        }

        $unit = isset($matches['unit']) ? strtoupper($matches['unit']) : 'B';
        // Add B suffix if not present
        if ($unit !== 'B' && substr($unit, -1) !== 'B') {
            $unit .= 'B';
        }

        if (!isset(self::STORAGE_UNITS[$unit])) {
            throw new InvalidArgumentException("Invalid unit: {$unit}");
        }

        return $sizeValue * pow(1024, self::STORAGE_UNITS[$unit]);
    }

    /**
     * Format bytes to human-readable size
     *
     * @param int|float $bytes Size in bytes
     * @param int $precision Number of decimal places
     * @return string Formatted size string
     * @throws InvalidArgumentException If input is invalid
     */
    public function formatBytes(int|float $bytes, int $precision = 2): string
    {
        if (!is_numeric($bytes) || $bytes < 0) {
            throw new InvalidArgumentException('Bytes must be a non-negative number');
        }

        if ($precision < 0) {
            throw new InvalidArgumentException('Precision must be non-negative');
        }

        $bytes = (float)$bytes;
        if ($bytes === 0.0) {
            return '0 B';
        }

        $units = array_keys(self::STORAGE_UNITS);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);
        return sprintf("%.{$precision}f %s", $bytes, $units[$pow]);
    }

    /**
     * Convert size between storage units
     *
     * @param float $size Size value to convert
     * @param string $unit Target unit (B, KB, MB, GB, TB)
     * @return float Converted size
     * @throws InvalidArgumentException If unit is invalid
     */
    public function convertUnit(float $size, string $unit): float
    {
        $unit = strtoupper($unit);

        // Add B suffix if not present
        if ($unit !== 'B' && substr($unit, -1) !== 'B') {
            $unit .= 'B';
        }

        if (!isset(self::STORAGE_UNITS[$unit])) {
            throw new InvalidArgumentException(
                "Invalid unit. Supported units: " . implode(', ', array_keys(self::STORAGE_UNITS))
            );
        }

        // Convert bytes to the target unit
        return $size / pow(1024, self::STORAGE_UNITS[$unit]);
    }

    /**
     * Sanitize input string
     *
     * @param string|null $value Input string to sanitize
     * @return string|null Sanitized string or null if input is null
     */
    public function sanitize(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);
        $value = strip_tags($value);
        return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Normalize file input array structure
     *
     * @param array $filePost $_FILES array
     * @return array Normalized array
     * @throws InvalidArgumentException If input array is invalid
     */
    public function normalizeFileArray(array $filePost): array
    {
        // Check if it's already a normalized array
        if (isset($filePost[0]['name'])) {
            return $filePost;
        }

        // Check if it's a single file (not an array of files)
        if (isset($filePost['name']) && !is_array($filePost['name'])) {
            return [$filePost];
        }

        // Handle multi-file upload
        if (!isset($filePost['name']) || !is_array($filePost['name'])) {
            throw new InvalidArgumentException('Invalid file array structure');
        }

        $fileArray = [];
        $fileKeys = array_keys($filePost);

        // Get the first dimension keys (could be numeric or string keys)
        $firstDimKeys = array_keys($filePost['name']);

        foreach ($firstDimKeys as $i) {
            $fileItem = [];
            foreach ($fileKeys as $key) {
                if (!isset($filePost[$key][$i])) {
                    throw new InvalidArgumentException("Missing key '{$key}' at index {$i}");
                }
                $fileItem[$key] = $filePost[$key][$i];
            }
            $fileArray[] = $fileItem;
        }

        return $fileArray;
    }

    /**
     * Set PHP INI settings
     *
     * @param array<string, mixed> $settings Key-value pairs of settings
     * @param string|null $iniPath Optional path to php.ini file
     * @return array<string, array{old: mixed, new: mixed}> Changes made
     * @throws RuntimeException If settings cannot be applied
     */
    public function setPhpIniSettings(array $settings, ?string $iniPath = null): array
    {
        $changes = [];

        foreach ($settings as $key => $value) {
            $oldValue = ini_get($key);
            if ($oldValue === false) {
                throw new RuntimeException("Invalid INI setting: {$key}");
            }

            if (ini_set($key, (string)$value) === false) {
                throw new RuntimeException("Failed to set {$key} to {$value}");
            }

            $changes[$key] = ['old' => $oldValue, 'new' => $value];
        }

        if ($iniPath !== null) {
            $this->writePhpIniFile($iniPath, $settings);
        }

        return $changes;
    }

    /**
     * Execute a callback function with arguments
     *
     * @param callable $callback Function to execute
     * @param array $args Arguments to pass to the callback
     * @return mixed Callback result
     * @throws InvalidArgumentException If callback is invalid
     */
    public function executeCallback(callable $callback, array $args = []): mixed
    {
        if (!is_callable($callback)) {
            throw new InvalidArgumentException('Invalid callback provided');
        }

        return call_user_func_array($callback, $args);
    }

    /**
     * Secure a directory against browsing
     *
     * @param string $path Directory path
     * @param bool $preventListing Whether to prevent directory listing
     * @param bool $denyAccess Whether to deny direct file access
     * @return void
     * @throws RuntimeException If directory cannot be secured
     */
    public function secureDirectory(
        string $path,
        bool $preventListing = true,
        bool $denyAccess = true
    ): void {
        $path = rtrim($path, '/\\');

        if (!is_dir($path)) {
            throw new RuntimeException("Directory does not exist: {$path}");
        }

        if (!is_writable($path)) {
            throw new RuntimeException("Directory is not writable: {$path}");
        }

        // Create .htaccess
        if ($preventListing || $denyAccess) {
            $htaccess = [];

            if ($preventListing) {
                $htaccess[] = 'Options -Indexes';
            }

            if ($denyAccess) {
                $htaccess[] = '<Files ~ "^\.">
    Order allow,deny
    Deny from all
</Files>';
            }

            $htaccessPath = $path . DIRECTORY_SEPARATOR . '.htaccess';
            if (!file_put_contents($htaccessPath, implode("\n", $htaccess))) {
                throw new RuntimeException("Failed to create .htaccess file");
            }

            // Apply permissions to the .htaccess file
            if (!chmod($htaccessPath, self::DEFAULT_FILE_PERMISSIONS)) {
                throw new RuntimeException("Failed to set permissions on .htaccess file");
            }
        }

        // Create empty index.php
        $indexPath = $path . DIRECTORY_SEPARATOR . 'index.php';
        if (!file_exists($indexPath)) {
            if (!file_put_contents($indexPath, '<?php http_response_code(403);')) {
                throw new RuntimeException("Failed to create index.php file");
            }

            // Apply permissions to the index.php file
            if (!chmod($indexPath, self::DEFAULT_FILE_PERMISSIONS)) {
                throw new RuntimeException("Failed to set permissions on index.php file");
            }
        }
    }

    /**
     * Write settings to php.ini file
     *
     * @param string $path Path to php.ini
     * @param array<string, mixed> $settings Settings to write
     * @throws RuntimeException If file cannot be written
     */
    private function writePhpIniFile(string $path, array $settings): void
    {
        $content = ['[PHP]'];
        foreach ($settings as $key => $value) {
            $content[] = "{$key} = " . (is_bool($value) ? ($value ? 'On' : 'Off') : $value);
        }

        if (!file_put_contents($path, implode("\n", $content))) {
            throw new RuntimeException("Failed to write php.ini file: {$path}");
        }

        if (!chmod($path, self::DEFAULT_FILE_PERMISSIONS)) {
            throw new RuntimeException("Failed to set permissions on php.ini file: {$path}");
        }
    }
}
