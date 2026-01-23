<?php

namespace Farisc0de\PhpFileUploading\Storage;

use Farisc0de\PhpFileUploading\Exception\StorageException;
use Farisc0de\PhpFileUploading\Exception\FileNotFoundException;
use Farisc0de\PhpFileUploading\Logging\LoggerAwareTrait;
use DateTimeInterface;
use DirectoryIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Local filesystem storage adapter
 *
 * @package PhpFileUploading
 */
class LocalStorage implements StorageInterface
{
    use LoggerAwareTrait;

    private string $rootPath;
    private int $directoryPermission;
    private int $filePermission;
    private ?string $publicUrlBase;

    public function __construct(
        string $rootPath,
        int $directoryPermission = 0755,
        int $filePermission = 0644,
        ?string $publicUrlBase = null
    ) {
        $this->rootPath = rtrim($rootPath, DIRECTORY_SEPARATOR);
        $this->directoryPermission = $directoryPermission;
        $this->filePermission = $filePermission;
        $this->publicUrlBase = $publicUrlBase ? rtrim($publicUrlBase, '/') : null;

        $this->ensureDirectoryExists($this->rootPath);
    }

    public function write(string $path, string $contents, array $config = []): void
    {
        $fullPath = $this->getFullPath($path);
        $this->ensureDirectoryExists(dirname($fullPath));

        $this->logDebug('Writing file: {path}', ['path' => $path]);

        if (file_put_contents($fullPath, $contents, LOCK_EX) === false) {
            throw StorageException::writeFailed($path);
        }

        chmod($fullPath, $config['permission'] ?? $this->filePermission);
    }

    public function writeStream(string $path, $contents, array $config = []): void
    {
        $fullPath = $this->getFullPath($path);
        $this->ensureDirectoryExists(dirname($fullPath));

        $this->logDebug('Writing stream to file: {path}', ['path' => $path]);

        $handle = fopen($fullPath, 'wb');
        if ($handle === false) {
            throw StorageException::writeFailed($path, 'Failed to open file for writing');
        }

        try {
            while (!feof($contents)) {
                $chunk = fread($contents, 8192);
                if ($chunk === false) {
                    throw StorageException::writeFailed($path, 'Failed to read from source stream');
                }
                if (fwrite($handle, $chunk) === false) {
                    throw StorageException::writeFailed($path, 'Failed to write to destination');
                }
            }
        } finally {
            fclose($handle);
        }

        chmod($fullPath, $config['permission'] ?? $this->filePermission);
    }

    public function read(string $path): string
    {
        $fullPath = $this->getFullPath($path);

        if (!file_exists($fullPath)) {
            throw new FileNotFoundException($path);
        }

        $contents = file_get_contents($fullPath);
        if ($contents === false) {
            throw StorageException::readFailed($path);
        }

        return $contents;
    }

    public function readStream(string $path)
    {
        $fullPath = $this->getFullPath($path);

        if (!file_exists($fullPath)) {
            throw new FileNotFoundException($path);
        }

        $handle = fopen($fullPath, 'rb');
        if ($handle === false) {
            throw StorageException::readFailed($path, 'Failed to open file for reading');
        }

        return $handle;
    }

    public function delete(string $path): void
    {
        $fullPath = $this->getFullPath($path);

        if (!file_exists($fullPath)) {
            return; // File doesn't exist, nothing to delete
        }

        $this->logDebug('Deleting file: {path}', ['path' => $path]);

        if (!unlink($fullPath)) {
            throw StorageException::deleteFailed($path);
        }
    }

    public function deleteDirectory(string $path): void
    {
        $fullPath = $this->getFullPath($path);

        if (!is_dir($fullPath)) {
            return;
        }

        $this->logDebug('Deleting directory: {path}', ['path' => $path]);

        $this->deleteDirectoryRecursive($fullPath);
    }

    private function deleteDirectoryRecursive(string $path): void
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isDir()) {
                rmdir($file->getPathname());
            } else {
                unlink($file->getPathname());
            }
        }

        rmdir($path);
    }

    public function createDirectory(string $path, array $config = []): void
    {
        $fullPath = $this->getFullPath($path);
        $permission = $config['permission'] ?? $this->directoryPermission;

        $this->logDebug('Creating directory: {path}', ['path' => $path]);

        if (!is_dir($fullPath)) {
            if (!mkdir($fullPath, $permission, true)) {
                throw StorageException::directoryCreateFailed($path);
            }
        }
    }

    public function fileExists(string $path): bool
    {
        $fullPath = $this->getFullPath($path);
        return file_exists($fullPath) && is_file($fullPath);
    }

    public function directoryExists(string $path): bool
    {
        $fullPath = $this->getFullPath($path);
        return is_dir($fullPath);
    }

    public function move(string $source, string $destination, array $config = []): void
    {
        $sourcePath = $this->getFullPath($source);
        $destPath = $this->getFullPath($destination);

        if (!file_exists($sourcePath)) {
            throw new FileNotFoundException($source);
        }

        $this->ensureDirectoryExists(dirname($destPath));

        $this->logDebug('Moving file from {source} to {destination}', [
            'source' => $source,
            'destination' => $destination
        ]);

        if (!rename($sourcePath, $destPath)) {
            throw StorageException::moveFailed($source, $destination);
        }
    }

    public function copy(string $source, string $destination, array $config = []): void
    {
        $sourcePath = $this->getFullPath($source);
        $destPath = $this->getFullPath($destination);

        if (!file_exists($sourcePath)) {
            throw new FileNotFoundException($source);
        }

        $this->ensureDirectoryExists(dirname($destPath));

        $this->logDebug('Copying file from {source} to {destination}', [
            'source' => $source,
            'destination' => $destination
        ]);

        if (!copy($sourcePath, $destPath)) {
            throw StorageException::copyFailed($source, $destination);
        }

        chmod($destPath, $config['permission'] ?? $this->filePermission);
    }

    public function fileSize(string $path): int
    {
        $fullPath = $this->getFullPath($path);

        if (!file_exists($fullPath)) {
            throw new FileNotFoundException($path);
        }

        $size = filesize($fullPath);
        if ($size === false) {
            throw StorageException::readFailed($path, 'Failed to get file size');
        }

        return $size;
    }

    public function mimeType(string $path): string
    {
        $fullPath = $this->getFullPath($path);

        if (!file_exists($fullPath)) {
            throw new FileNotFoundException($path);
        }

        $mimeType = mime_content_type($fullPath);
        if ($mimeType === false) {
            throw StorageException::readFailed($path, 'Failed to determine MIME type');
        }

        return $mimeType;
    }

    public function lastModified(string $path): int
    {
        $fullPath = $this->getFullPath($path);

        if (!file_exists($fullPath)) {
            throw new FileNotFoundException($path);
        }

        $mtime = filemtime($fullPath);
        if ($mtime === false) {
            throw StorageException::readFailed($path, 'Failed to get modification time');
        }

        return $mtime;
    }

    public function listContents(string $path, bool $deep = false): iterable
    {
        $fullPath = $this->getFullPath($path);

        if (!is_dir($fullPath)) {
            return;
        }

        if ($deep) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($fullPath, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST
            );
        } else {
            $iterator = new DirectoryIterator($fullPath);
        }

        foreach ($iterator as $file) {
            if ($file->isDot()) {
                continue;
            }

            $relativePath = $this->getRelativePath($file->getPathname());

            yield new FileInfo(
                $relativePath,
                $file->isDir() ? 'dir' : 'file',
                $file->isFile() ? $file->getSize() : 0,
                $file->getMTime()
            );
        }
    }

    public function publicUrl(string $path): string
    {
        if ($this->publicUrlBase === null) {
            throw new \RuntimeException('Public URL base not configured');
        }

        return $this->publicUrlBase . '/' . ltrim($path, '/');
    }

    public function temporaryUrl(string $path, DateTimeInterface $expiration, array $config = []): string
    {
        // Local storage doesn't support temporary URLs natively
        // This could be extended to generate signed URLs with a secret key
        return $this->publicUrl($path);
    }

    /**
     * Get the full filesystem path
     */
    private function getFullPath(string $path): string
    {
        // Prevent directory traversal
        $path = str_replace(['../', '..\\'], '', $path);
        return $this->rootPath . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR);
    }

    /**
     * Get relative path from full path
     */
    private function getRelativePath(string $fullPath): string
    {
        return ltrim(str_replace($this->rootPath, '', $fullPath), DIRECTORY_SEPARATOR);
    }

    /**
     * Ensure a directory exists
     */
    private function ensureDirectoryExists(string $path): void
    {
        if (!is_dir($path)) {
            if (!mkdir($path, $this->directoryPermission, true)) {
                throw StorageException::directoryCreateFailed($path);
            }
        }
    }

    /**
     * Get the root path
     */
    public function getRootPath(): string
    {
        return $this->rootPath;
    }

    /**
     * Get available disk space
     */
    public function getAvailableSpace(): int
    {
        $space = disk_free_space($this->rootPath);
        return $space !== false ? (int)$space : 0;
    }

    /**
     * Get total disk space
     */
    public function getTotalSpace(): int
    {
        $space = disk_total_space($this->rootPath);
        return $space !== false ? (int)$space : 0;
    }
}
