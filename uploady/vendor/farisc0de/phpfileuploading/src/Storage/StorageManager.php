<?php

namespace Farisc0de\PhpFileUploading\Storage;

use Farisc0de\PhpFileUploading\Exception\ConfigurationException;
use Farisc0de\PhpFileUploading\Logging\LoggerAwareTrait;

/**
 * Manages multiple storage adapters
 *
 * @package PhpFileUploading
 */
class StorageManager
{
    use LoggerAwareTrait;

    /** @var StorageInterface[] */
    private array $disks = [];
    private ?string $defaultDisk = null;

    /**
     * Register a storage disk
     */
    public function addDisk(string $name, StorageInterface $storage): self
    {
        $this->disks[$name] = $storage;

        if ($this->defaultDisk === null) {
            $this->defaultDisk = $name;
        }

        $this->logDebug('Registered storage disk: {name}', ['name' => $name]);

        return $this;
    }

    /**
     * Get a storage disk by name
     */
    public function disk(?string $name = null): StorageInterface
    {
        $name = $name ?? $this->defaultDisk;

        if ($name === null) {
            throw ConfigurationException::missingConfig('default_disk');
        }

        if (!isset($this->disks[$name])) {
            throw ConfigurationException::invalidConfig('disk', $name, 'A registered disk name');
        }

        return $this->disks[$name];
    }

    /**
     * Set the default disk
     */
    public function setDefaultDisk(string $name): self
    {
        if (!isset($this->disks[$name])) {
            throw ConfigurationException::invalidConfig('default_disk', $name, 'A registered disk name');
        }

        $this->defaultDisk = $name;
        return $this;
    }

    /**
     * Get the default disk name
     */
    public function getDefaultDisk(): ?string
    {
        return $this->defaultDisk;
    }

    /**
     * Check if a disk exists
     */
    public function hasDisk(string $name): bool
    {
        return isset($this->disks[$name]);
    }

    /**
     * Get all registered disk names
     */
    public function getDiskNames(): array
    {
        return array_keys($this->disks);
    }

    /**
     * Remove a disk
     */
    public function removeDisk(string $name): self
    {
        unset($this->disks[$name]);

        if ($this->defaultDisk === $name) {
            $this->defaultDisk = !empty($this->disks) ? array_key_first($this->disks) : null;
        }

        return $this;
    }

    /**
     * Create a local storage disk
     */
    public function createLocalDisk(
        string $name,
        string $rootPath,
        ?string $publicUrlBase = null
    ): self {
        $storage = new LocalStorage($rootPath, 0755, 0644, $publicUrlBase);

        if ($this->logger !== null) {
            $storage->setLogger($this->logger);
        }

        return $this->addDisk($name, $storage);
    }
}
