<?php

namespace Farisc0de\PhpFileUploading\Validation;

/**
 * Represents a single validation error
 *
 * @package PhpFileUploading
 */
class ValidationError
{
    private string $message;
    private string $code;
    private array $context;

    public function __construct(string $message, string $code = '', array $context = [])
    {
        $this->message = $message;
        $this->code = $code;
        $this->context = $context;
    }

    /**
     * Get the error message
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Get the error code
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * Get the error context
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Get a specific context value
     */
    public function getContextValue(string $key, mixed $default = null): mixed
    {
        return $this->context[$key] ?? $default;
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'message' => $this->message,
            'code' => $this->code,
            'context' => $this->context,
        ];
    }

    /**
     * Convert to string
     */
    public function __toString(): string
    {
        $str = $this->message;
        if ($this->code) {
            $str .= " [{$this->code}]";
        }
        return $str;
    }
}
