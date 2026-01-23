<?php

namespace Farisc0de\PhpFileUploading\Events;

use Farisc0de\PhpFileUploading\File;
use Farisc0de\PhpFileUploading\Validation\ValidationResult;

/**
 * Event for validation operations
 *
 * @package PhpFileUploading
 */
class ValidationEvent extends FileEvent
{
    protected ?ValidationResult $result = null;

    public function __construct(string $name, ?File $file = null, ?ValidationResult $result = null, array $data = [])
    {
        parent::__construct($name, $file, $data);
        $this->result = $result;
    }

    /**
     * Get the validation result
     */
    public function getResult(): ?ValidationResult
    {
        return $this->result;
    }

    /**
     * Set the validation result
     */
    public function setResult(ValidationResult $result): self
    {
        $this->result = $result;
        return $this;
    }

    /**
     * Check if validation passed
     */
    public function isValid(): bool
    {
        return $this->result?->isValid() ?? false;
    }
}
