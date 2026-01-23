<?php

namespace Farisc0de\PhpFileUploading\Storage;

/**
 * Represents file/directory information from storage listing
 *
 * @package PhpFileUploading
 */
class FileInfo
{
    private string $path;
    private string $type;
    private int $size;
    private int $lastModified;
    private array $metadata;

    public function __construct(
        string $path,
        string $type,
        int $size = 0,
        int $lastModified = 0,
        array $metadata = []
    ) {
        $this->path = $path;
        $this->type = $type;
        $this->size = $size;
        $this->lastModified = $lastModified;
        $this->metadata = $metadata;
    }

    /**
     * Get the file/directory path
     */
    public function path(): string
    {
        return $this->path;
    }

    /**
     * Get the type ('file' or 'dir')
     */
    public function type(): string
    {
        return $this->type;
    }

    /**
     * Check if this is a file
     */
    public function isFile(): bool
    {
        return $this->type === 'file';
    }

    /**
     * Check if this is a directory
     */
    public function isDir(): bool
    {
        return $this->type === 'dir';
    }

    /**
     * Get the file size in bytes
     */
    public function size(): int
    {
        return $this->size;
    }

    /**
     * Get the last modified timestamp
     */
    public function lastModified(): int
    {
        return $this->lastModified;
    }

    /**
     * Get additional metadata
     */
    public function metadata(): array
    {
        return $this->metadata;
    }

    /**
     * Get a specific metadata value
     */
    public function getMetadata(string $key, mixed $default = null): mixed
    {
        return $this->metadata[$key] ?? $default;
    }

    /**
     * Get the filename (basename)
     */
    public function filename(): string
    {
        return basename($this->path);
    }

    /**
     * Get the directory name
     */
    public function dirname(): string
    {
        return dirname($this->path);
    }

    /**
     * Get the file extension
     */
    public function extension(): string
    {
        return pathinfo($this->path, PATHINFO_EXTENSION);
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'path' => $this->path,
            'type' => $this->type,
            'size' => $this->size,
            'last_modified' => $this->lastModified,
            'metadata' => $this->metadata,
        ];
    }
}
