<?php

namespace Farisc0de\PhpFileUploading\Exception;

use Exception;
use Throwable;

/**
 * Base exception class for all upload-related exceptions
 *
 * @package PhpFileUploading
 */
class UploadException extends Exception
{
    protected array $context = [];

    public function __construct(
        string $message = '',
        int $code = 0,
        ?Throwable $previous = null,
        array $context = []
    ) {
        parent::__construct($message, $code, $previous);
        $this->context = $context;
    }

    /**
     * Get additional context information about the exception
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Set context information
     */
    public function setContext(array $context): self
    {
        $this->context = $context;
        return $this;
    }

    /**
     * Add a single context item
     */
    public function addContext(string $key, mixed $value): self
    {
        $this->context[$key] = $value;
        return $this;
    }
}
