<?php

namespace Farisc0de\PhpFileUploading\Validation;

/**
 * Represents the result of a validation operation
 *
 * @package PhpFileUploading
 */
class ValidationResult
{
    private bool $valid;
    private array $errors = [];
    private array $warnings = [];
    private array $metadata = [];

    public function __construct(bool $valid = true)
    {
        $this->valid = $valid;
    }

    /**
     * Create a successful validation result
     */
    public static function success(array $metadata = []): self
    {
        $result = new self(true);
        $result->metadata = $metadata;
        return $result;
    }

    /**
     * Create a failed validation result
     */
    public static function failure(string $error, string $code = '', array $context = []): self
    {
        $result = new self(false);
        $result->addError($error, $code, $context);
        return $result;
    }

    /**
     * Check if validation passed
     */
    public function isValid(): bool
    {
        return $this->valid && empty($this->errors);
    }

    /**
     * Check if validation failed
     */
    public function isFailed(): bool
    {
        return !$this->isValid();
    }

    /**
     * Add an error to the result
     */
    public function addError(string $message, string $code = '', array $context = []): self
    {
        $this->valid = false;
        $this->errors[] = new ValidationError($message, $code, $context);
        return $this;
    }

    /**
     * Add a warning to the result (doesn't affect validity)
     */
    public function addWarning(string $message, string $code = '', array $context = []): self
    {
        $this->warnings[] = new ValidationError($message, $code, $context);
        return $this;
    }

    /**
     * Get all errors
     *
     * @return ValidationError[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get all warnings
     *
     * @return ValidationError[]
     */
    public function getWarnings(): array
    {
        return $this->warnings;
    }

    /**
     * Get the first error message
     */
    public function getFirstError(): ?string
    {
        return isset($this->errors[0]) ? $this->errors[0]->getMessage() : null;
    }

    /**
     * Get all error messages as array
     */
    public function getErrorMessages(): array
    {
        return array_map(fn(ValidationError $e) => $e->getMessage(), $this->errors);
    }

    /**
     * Get all warning messages as array
     */
    public function getWarningMessages(): array
    {
        return array_map(fn(ValidationError $e) => $e->getMessage(), $this->warnings);
    }

    /**
     * Check if there are any errors
     */
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    /**
     * Check if there are any warnings
     */
    public function hasWarnings(): bool
    {
        return !empty($this->warnings);
    }

    /**
     * Set metadata
     */
    public function setMetadata(array $metadata): self
    {
        $this->metadata = $metadata;
        return $this;
    }

    /**
     * Add metadata item
     */
    public function addMetadata(string $key, mixed $value): self
    {
        $this->metadata[$key] = $value;
        return $this;
    }

    /**
     * Get metadata
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * Get specific metadata value
     */
    public function getMetadataValue(string $key, mixed $default = null): mixed
    {
        return $this->metadata[$key] ?? $default;
    }

    /**
     * Merge another validation result into this one
     */
    public function merge(ValidationResult $other): self
    {
        foreach ($other->getErrors() as $error) {
            $this->errors[] = $error;
            $this->valid = false;
        }

        foreach ($other->getWarnings() as $warning) {
            $this->warnings[] = $warning;
        }

        $this->metadata = array_merge($this->metadata, $other->getMetadata());

        return $this;
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'valid' => $this->isValid(),
            'errors' => array_map(fn(ValidationError $e) => $e->toArray(), $this->errors),
            'warnings' => array_map(fn(ValidationError $e) => $e->toArray(), $this->warnings),
            'metadata' => $this->metadata,
        ];
    }

    /**
     * Convert to JSON
     */
    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_THROW_ON_ERROR);
    }
}
