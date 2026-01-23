<?php

namespace Farisc0de\PhpFileUploading\Events;

use Farisc0de\PhpFileUploading\File;

/**
 * Event related to file operations
 *
 * @package PhpFileUploading
 */
class FileEvent extends AbstractEvent
{
    protected ?File $file;

    public function __construct(string $name, ?File $file = null, array $data = [])
    {
        parent::__construct($name, $data);
        $this->file = $file;
    }

    /**
     * Get the file associated with this event
     */
    public function getFile(): ?File
    {
        return $this->file;
    }

    /**
     * Set the file
     */
    public function setFile(?File $file): self
    {
        $this->file = $file;
        return $this;
    }

    /**
     * Get the filename
     */
    public function getFilename(): ?string
    {
        return $this->file?->getName();
    }
}
