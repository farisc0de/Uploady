<?php

namespace Farisc0de\PhpFileUploading\Storage;

/**
 * Interface for storage adapters
 * 
 * This interface is compatible with Flysystem's FilesystemOperator
 * allowing easy integration with cloud storage providers.
 *
 * @package PhpFileUploading
 */
interface StorageInterface
{
    /**
     * Write content to a file
     *
     * @param string $path The file path
     * @param string $contents The file contents
     * @param array $config Additional configuration options
     * @throws \Farisc0de\PhpFileUploading\Exception\StorageException
     */
    public function write(string $path, string $contents, array $config = []): void;

    /**
     * Write a stream to a file
     *
     * @param string $path The file path
     * @param resource $contents The file stream
     * @param array $config Additional configuration options
     * @throws \Farisc0de\PhpFileUploading\Exception\StorageException
     */
    public function writeStream(string $path, $contents, array $config = []): void;

    /**
     * Read file contents
     *
     * @param string $path The file path
     * @return string The file contents
     * @throws \Farisc0de\PhpFileUploading\Exception\StorageException
     */
    public function read(string $path): string;

    /**
     * Read file as a stream
     *
     * @param string $path The file path
     * @return resource The file stream
     * @throws \Farisc0de\PhpFileUploading\Exception\StorageException
     */
    public function readStream(string $path);

    /**
     * Delete a file
     *
     * @param string $path The file path
     * @throws \Farisc0de\PhpFileUploading\Exception\StorageException
     */
    public function delete(string $path): void;

    /**
     * Delete a directory
     *
     * @param string $path The directory path
     * @throws \Farisc0de\PhpFileUploading\Exception\StorageException
     */
    public function deleteDirectory(string $path): void;

    /**
     * Create a directory
     *
     * @param string $path The directory path
     * @param array $config Additional configuration options
     * @throws \Farisc0de\PhpFileUploading\Exception\StorageException
     */
    public function createDirectory(string $path, array $config = []): void;

    /**
     * Check if a file exists
     *
     * @param string $path The file path
     * @return bool True if file exists
     */
    public function fileExists(string $path): bool;

    /**
     * Check if a directory exists
     *
     * @param string $path The directory path
     * @return bool True if directory exists
     */
    public function directoryExists(string $path): bool;

    /**
     * Move a file
     *
     * @param string $source Source path
     * @param string $destination Destination path
     * @param array $config Additional configuration options
     * @throws \Farisc0de\PhpFileUploading\Exception\StorageException
     */
    public function move(string $source, string $destination, array $config = []): void;

    /**
     * Copy a file
     *
     * @param string $source Source path
     * @param string $destination Destination path
     * @param array $config Additional configuration options
     * @throws \Farisc0de\PhpFileUploading\Exception\StorageException
     */
    public function copy(string $source, string $destination, array $config = []): void;

    /**
     * Get file size
     *
     * @param string $path The file path
     * @return int File size in bytes
     * @throws \Farisc0de\PhpFileUploading\Exception\StorageException
     */
    public function fileSize(string $path): int;

    /**
     * Get file MIME type
     *
     * @param string $path The file path
     * @return string The MIME type
     * @throws \Farisc0de\PhpFileUploading\Exception\StorageException
     */
    public function mimeType(string $path): string;

    /**
     * Get file last modified time
     *
     * @param string $path The file path
     * @return int Unix timestamp
     * @throws \Farisc0de\PhpFileUploading\Exception\StorageException
     */
    public function lastModified(string $path): int;

    /**
     * List contents of a directory
     *
     * @param string $path The directory path
     * @param bool $deep Whether to list recursively
     * @return iterable List of file/directory info
     */
    public function listContents(string $path, bool $deep = false): iterable;

    /**
     * Get the public URL for a file
     *
     * @param string $path The file path
     * @return string The public URL
     */
    public function publicUrl(string $path): string;

    /**
     * Get a temporary URL for a file (for private files)
     *
     * @param string $path The file path
     * @param \DateTimeInterface $expiration URL expiration time
     * @param array $config Additional configuration options
     * @return string The temporary URL
     */
    public function temporaryUrl(string $path, \DateTimeInterface $expiration, array $config = []): string;
}
